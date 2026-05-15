<?php
class Mail
{
    public static function send(string $to, string $subject, string $body, ?string $patientId = null, ?int $appointmentId = null): bool
    {
        $from = cfg('mail.from_address', 'no-reply@dental.local');
        $name = cfg('mail.from_name', 'DentalCore');

        $headers = [
            'From'         => "$name <$from>",
            'Reply-To'     => $from,
            'MIME-Version' => '1.0',
            'Content-Type' => 'text/html; charset=UTF-8',
        ];
        $hdr = '';
        foreach ($headers as $k => $v) $hdr .= "$k: $v\r\n";

        $ok = false;
        $err = null;
        try {
            $ok = @mail($to, $subject, $body, $hdr);
        } catch (Throwable $e) {
            $err = $e->getMessage();
        }

        Communication::log([
            'patient_id'     => $patientId,
            'appointment_id' => $appointmentId,
            'channel'        => 'email',
            'direction'      => 'outbound',
            'subject'        => $subject,
            'body'           => $body,
            'to'             => $to,
            'status'         => $ok ? 'sent' : 'failed',
            'sent_at'        => $ok ? now() : null,
            'error'          => $err,
        ]);
        return $ok;
    }

    public static function template(string $name, array $vars = []): string
    {
        $base = cfg('app.url', '');
        $primary = setting('clinic_color_primary', '#0ea5e9');
        $clinic = setting('clinic_name', 'DentalCore');

        $body = match ($name) {
            'reminder' => self::reminderTemplate($vars),
            'recall'   => self::recallTemplate($vars),
            'welcome'  => self::welcomeTemplate($vars),
            'portal'   => self::portalTemplate($vars),
            default    => '<p>' . e($vars['message'] ?? '') . '</p>',
        };

        return '<!doctype html><html><body style="font-family:Arial,sans-serif;background:#f8fafc;padding:20px;">'
            . '<div style="max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 6px rgba(0,0,0,0.1);">'
            . '<div style="background:' . $primary . ';color:#fff;padding:20px;text-align:center;">'
            . '<h1 style="margin:0;">' . e($clinic) . '</h1></div>'
            . '<div style="padding:30px;color:#334155;line-height:1.6;">' . $body . '</div>'
            . '<div style="background:#f1f5f9;color:#64748b;padding:15px;text-align:center;font-size:12px;">'
            . 'Este es un mensaje automático. ' . e($clinic) . '</div>'
            . '</div></body></html>';
    }

    private static function reminderTemplate(array $v): string
    {
        $base = cfg('app.url', '');
        return '<p>Hola <strong>' . e($v['patient_name'] ?? '') . '</strong>,</p>'
            . '<p>Te recordamos tu cita programada para:</p>'
            . '<p style="background:#e0f2fe;padding:15px;border-radius:8px;font-size:1.2rem;">'
            . '<strong>📅 ' . e($v['date'] ?? '') . '</strong><br>'
            . '🦷 ' . e($v['dentist'] ?? '') . '</p>'
            . '<p>Puedes confirmar o cancelar haciendo clic en uno de estos botones:</p>'
            . '<p><a href="' . $base . '/cita/confirmar/' . e($v['confirm_token'] ?? '') . '" '
            . 'style="background:#10b981;color:#fff;padding:12px 24px;border-radius:6px;text-decoration:none;display:inline-block;margin-right:10px;">✓ Confirmar</a>'
            . '<a href="' . $base . '/cita/cancelar/' . e($v['cancel_token'] ?? '') . '" '
            . 'style="background:#ef4444;color:#fff;padding:12px 24px;border-radius:6px;text-decoration:none;display:inline-block;">✕ Cancelar</a></p>';
    }

    private static function recallTemplate(array $v): string
    {
        $base = cfg('app.url', '');
        return '<p>Hola <strong>' . e($v['patient_name'] ?? '') . '</strong>,</p>'
            . '<p>Ha pasado tiempo desde tu última visita. Te recordamos la importancia de mantener una rutina de cuidado dental.</p>'
            . '<p>Te invitamos a agendar tu próxima limpieza:</p>'
            . '<p><a href="' . $base . '/mi-cuenta" style="background:#0ea5e9;color:#fff;padding:12px 24px;border-radius:6px;text-decoration:none;">Agendar cita</a></p>';
    }

    private static function welcomeTemplate(array $v): string
    {
        return '<p>Hola <strong>' . e($v['patient_name'] ?? '') . '</strong>,</p>'
            . '<p>¡Bienvenido a ' . e(setting('clinic_name')) . '! Estamos felices de cuidar tu sonrisa.</p>';
    }

    private static function portalTemplate(array $v): string
    {
        $base = cfg('app.url', '');
        return '<p>Hola <strong>' . e($v['patient_name'] ?? '') . '</strong>,</p>'
            . '<p>Para acceder al portal del paciente, haz clic en el siguiente enlace (válido por 1 hora):</p>'
            . '<p><a href="' . $base . '/mi-cuenta/acceso/' . e($v['token'] ?? '') . '" '
            . 'style="background:#0ea5e9;color:#fff;padding:12px 24px;border-radius:6px;text-decoration:none;">Entrar al portal</a></p>';
    }
}
