<?php
/**
 * Tareas programadas (cron). Ejecutar 1 vez al día.
 *
 * Configuración crontab típica:
 *   0 9 * * *  php /ruta/al/proyecto/cli/cron.php
 *
 * Tareas:
 *  1. Recordatorios 24h antes de la cita
 *  2. Felicitación de cumpleaños con descuento
 *  3. Win-back para clientes inactivos (60+ días sin venir)
 *  4. Encuesta NPS al día siguiente de la cita
 *  5. Marcar citas no-show automáticamente
 *  6. Actualizar bonos caducados
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403); die('CLI only');
}

require __DIR__ . '/../app/config/config.php';
require __DIR__ . '/../app/core/helpers.php';
require __DIR__ . '/../app/core/Database.php';
require __DIR__ . '/../app/core/Mail.php';

$started = microtime(true);
$log = [];

// ===== 1. Recordatorios 24h antes =====
$tomorrow = date('Y-m-d', strtotime('+1 day'));
$apps = Database::fetchAll(
    "SELECT a.*, c.first_name, c.last_name, c.email, s.name AS service_name
     FROM appointments a
     JOIN customers c ON c.id = a.customer_id
     JOIN services s ON s.id = a.service_id
     WHERE DATE(a.starts_at) = ? AND a.status IN ('pendiente','confirmada')
       AND a.reminder_sent_at IS NULL AND c.email IS NOT NULL",
    [$tomorrow]
);
foreach ($apps as $a) {
    $service = ['name' => $a['service_name']];
    if (Mail::sendReminder($a, $a, $service)) {
        Database::update('appointments', ['reminder_sent_at' => date('Y-m-d H:i:s')], 'id = :id', ['id' => $a['id']]);
        $log[] = "[Recordatorio] Cita #{$a['id']} → {$a['email']}";
    }
}

// ===== 2. Felicitación cumpleaños =====
$today = date('m-d');
$birthdayCustomers = Database::fetchAll(
    "SELECT * FROM customers
     WHERE DATE_FORMAT(birthdate, '%m-%d') = ?
       AND accepts_marketing = 1 AND email IS NOT NULL",
    [$today]
);
foreach ($birthdayCustomers as $c) {
    // Crear código promo personal
    $promoCode = 'BD-' . strtoupper(substr(md5($c['id'] . date('Y')), 0, 6));
    Database::query(
        "INSERT IGNORE INTO promo_codes (code, description, discount_type, discount_value, valid_until, max_uses, active)
         VALUES (?, ?, 'percent', 20, DATE_ADD(CURDATE(), INTERVAL 30 DAY), 1, 1)",
        [$promoCode, 'Cumpleaños ' . $c['first_name']]
    );
    if (Mail::sendBirthday($c, $promoCode, 20)) {
        $log[] = "[Cumpleaños] {$c['email']} → código {$promoCode}";
    }
}

// ===== 3. Win-back clientes inactivos =====
$inactiveCustomers = Database::fetchAll(
    "SELECT c.* FROM customers c
     WHERE c.accepts_marketing = 1 AND c.email IS NOT NULL
       AND NOT EXISTS (
         SELECT 1 FROM appointments a WHERE a.customer_id = c.id
           AND a.starts_at > DATE_SUB(NOW(), INTERVAL 60 DAY)
       )
       AND EXISTS (
         SELECT 1 FROM appointments a WHERE a.customer_id = c.id
           AND a.starts_at < DATE_SUB(NOW(), INTERVAL 60 DAY)
           AND a.starts_at > DATE_SUB(NOW(), INTERVAL 90 DAY)
       )
     LIMIT 50"
);
foreach ($inactiveCustomers as $c) {
    $promoCode = 'WB-' . strtoupper(substr(md5($c['id'] . date('Ym')), 0, 6));
    Database::query(
        "INSERT IGNORE INTO promo_codes (code, description, discount_type, discount_value, valid_until, max_uses, active)
         VALUES (?, ?, 'percent', 15, DATE_ADD(CURDATE(), INTERVAL 45 DAY), 1, 1)",
        [$promoCode, 'Win-back ' . $c['first_name']]
    );
    if (Mail::sendWinback($c, $promoCode, 15)) {
        $log[] = "[Win-back] {$c['email']}";
    }
}

// ===== 4. Encuesta NPS al día siguiente =====
$yesterday = date('Y-m-d', strtotime('-1 day'));
$surveyApps = Database::fetchAll(
    "SELECT a.*, c.first_name, c.last_name, c.email, s.name AS service_name
     FROM appointments a
     JOIN customers c ON c.id = a.customer_id
     JOIN services s ON s.id = a.service_id
     WHERE DATE(a.starts_at) = ? AND a.status = 'completada'
       AND c.accepts_marketing = 1 AND c.email IS NOT NULL
       AND NOT EXISTS (SELECT 1 FROM surveys s WHERE s.appointment_id = a.id)",
    [$yesterday]
);
foreach ($surveyApps as $a) {
    if (Mail::sendSurvey($a, $a, ['name' => $a['service_name']])) {
        $log[] = "[Encuesta] Cita #{$a['id']} → {$a['email']}";
    }
}

// ===== 5. Auto no-show =====
// Citas no completadas pasadas 3 horas → no_show
$cnt = Database::query(
    "UPDATE appointments SET status = 'no_show'
     WHERE status IN ('pendiente','confirmada')
       AND ends_at < DATE_SUB(NOW(), INTERVAL 3 HOUR)"
)->rowCount();
if ($cnt) $log[] = "[Auto no-show] {$cnt} citas marcadas";

// ===== 6. Bonos caducados =====
$cnt = Database::query(
    "UPDATE customer_packs SET status = 'caducado'
     WHERE status = 'activo' AND expires_at < NOW()"
)->rowCount();
if ($cnt) $log[] = "[Bonos caducados] {$cnt}";

// ===== 7. Tarjetas regalo caducadas =====
$cnt = Database::query(
    "UPDATE gift_cards SET status = 'caducada'
     WHERE status = 'activa' AND expires_at < NOW()"
)->rowCount();
if ($cnt) $log[] = "[Tarjetas caducadas] {$cnt}";

$duration = round((microtime(true) - $started) * 1000);
echo "[" . date('Y-m-d H:i:s') . "] CRON completado en {$duration}ms" . PHP_EOL;
foreach ($log as $line) echo "  - $line" . PHP_EOL;
echo "Total: " . count($log) . " tareas" . PHP_EOL;
