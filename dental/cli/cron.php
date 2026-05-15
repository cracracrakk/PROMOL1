<?php
// DentalCore - Tareas programadas
// Configurar en crontab (cada hora):
//   0 * * * * php /ruta/dental/cli/cron.php
//
// Tareas:
//   - Envío de recordatorios de citas próximas
//   - Envío de encuestas post-visita
//   - Recall a pacientes inactivos

if (PHP_SAPI !== 'cli') exit("Solo CLI.\n");

$config = require __DIR__ . '/../app/config/config.php';
$GLOBALS['app_config'] = $config;
date_default_timezone_set($config['app']['timezone'] ?? 'UTC');

$base = __DIR__ . '/..';
require $base . '/app/core/helpers.php';
require $base . '/app/core/Database.php';
require $base . '/app/core/Auth.php';
require $base . '/app/core/Mail.php';

spl_autoload_register(function ($cls) use ($base) {
    foreach (['models','core'] as $d) {
        $f = $base . '/app/' . $d . '/' . $cls . '.php';
        if (is_file($f)) { require $f; return; }
    }
});

$started = microtime(true);
echo "[" . date('Y-m-d H:i:s') . "] Cron iniciado\n";

// ---- 1. Recordatorios de citas ----
if (setting('reminder_enabled', '1') === '1') {
    $hoursBefore = (int)setting('reminder_hours_before', 24);
    $targetTime  = date('Y-m-d H:i:s', strtotime("+{$hoursBefore} hours"));
    $window      = date('Y-m-d H:i:s', strtotime("+{$hoursBefore} hours +1 hour"));

    $upcoming = Database::query(
        "SELECT a.*, p.first_name, p.last_name, p.email, p.mobile, u.name AS dentist_name
         FROM appointments a
         JOIN patients p ON p.id = a.patient_id
         JOIN users u    ON u.id = a.dentist_id
         WHERE a.reminder_sent = 0
           AND a.status IN ('scheduled','confirmed')
           AND a.starts_at BETWEEN ? AND ?
           AND p.email IS NOT NULL AND p.email != ''",
        [$targetTime, $window]
    );

    echo "Recordatorios a enviar: " . count($upcoming) . "\n";
    foreach ($upcoming as $a) {
        $confirmToken = AppointmentToken::generate((int)$a['id'], 'confirm');
        $cancelToken  = AppointmentToken::generate((int)$a['id'], 'cancel');

        $body = Mail::template('reminder', [
            'patient_name'  => $a['first_name'],
            'date'          => format_date($a['starts_at'], 'd/m/Y H:i'),
            'dentist'       => $a['dentist_name'],
            'confirm_token' => $confirmToken,
            'cancel_token'  => $cancelToken,
        ]);
        $ok = Mail::send(
            $a['email'],
            'Recordatorio de tu cita dental',
            $body,
            (string)$a['patient_id'],
            (int)$a['id']
        );
        if ($ok) {
            Database::update('appointments', ['reminder_sent' => 1], ['id' => $a['id']]);
            echo "  ✓ " . $a['email'] . "\n";
        } else {
            echo "  ✗ " . $a['email'] . "\n";
        }
    }
}

// ---- 2. Encuestas post-visita ----
if (setting('survey_enabled', '1') === '1') {
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    $completed = Database::query(
        "SELECT a.id, a.patient_id, p.first_name, p.email
         FROM appointments a
         JOIN patients p ON p.id = a.patient_id
         WHERE a.status = 'completed'
           AND DATE(a.ends_at) = ?
           AND p.email IS NOT NULL
           AND NOT EXISTS (SELECT 1 FROM surveys s WHERE s.appointment_id = a.id)
           AND NOT EXISTS (SELECT 1 FROM appointment_tokens t WHERE t.appointment_id = a.id AND t.action = 'survey')",
        [$yesterday]
    );

    echo "Encuestas a enviar: " . count($completed) . "\n";
    foreach ($completed as $a) {
        $token = AppointmentToken::generate((int)$a['id'], 'survey', 168);
        $body = Mail::template('default', [
            'message' => "Hola {$a['first_name']}, ¡gracias por tu visita! ¿Podrías compartir tu opinión en este breve formulario? "
                . '<br><a href="' . cfg('app.url') . '/cita/encuesta/' . $token . '" '
                . 'style="background:#0ea5e9;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;display:inline-block;margin-top:10px;">Dejar mi opinión</a>',
        ]);
        Mail::send($a['email'], '¿Cómo fue tu experiencia con nosotros?', $body, (string)$a['patient_id'], (int)$a['id']);
        echo "  ✓ Survey → " . $a['email'] . "\n";
    }
}

// ---- 3. Recall ----
$recallMonths = (int)setting('recall_months', 6);
$lastRecallSent = (string)setting('last_recall_run', '2000-01-01');
$now = date('Y-m-d');
if ($lastRecallSent !== $now) {
    $candidates = Report::patientsForRecall($recallMonths);
    echo "Recall: " . count($candidates) . " candidatos\n";
    foreach (array_slice($candidates, 0, 50) as $p) {
        if (!$p['email']) continue;
        // Solo una vez al mes por paciente
        $alreadySent = (int)Database::value(
            "SELECT COUNT(*) FROM communications
             WHERE patient_id = ? AND subject LIKE 'Es momento de tu revisión%'
             AND created_at > DATE_SUB(NOW(), INTERVAL 30 DAY)",
            [$p['id']]
        );
        if ($alreadySent) continue;

        $body = Mail::template('recall', ['patient_name' => $p['first_name']]);
        Mail::send($p['email'], 'Es momento de tu revisión dental', $body, (string)$p['id']);
        echo "  ✓ Recall → " . $p['email'] . "\n";
    }
    Setting::set('last_recall_run', $now);
}

$elapsed = round(microtime(true) - $started, 2);
echo "[" . date('Y-m-d H:i:s') . "] Cron terminado ({$elapsed}s)\n";
