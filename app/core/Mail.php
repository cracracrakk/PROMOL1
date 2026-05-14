<?php
/**
 * Mail helper - envío simple sin dependencias externas.
 * Usa mail() de PHP. Para SMTP completo, integrar PHPMailer.
 *
 * Plantillas en /app/views/emails/
 */
class Mail {

    public static function send(string $to, string $subject, string $htmlBody, ?string $fromName = null): bool {
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) return false;

        $fromName  = $fromName ?? setting('spa_name', 'Spa');
        $fromEmail = setting('spa_email', 'no-reply@localhost');

        $headers   = [];
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=UTF-8';
        $headers[] = 'From: ' . self::encode($fromName) . ' <' . $fromEmail . '>';
        $headers[] = 'Reply-To: ' . $fromEmail;
        $headers[] = 'X-Mailer: SpaSystem/1.0';

        $body = self::wrapTemplate($subject, $htmlBody);
        $subj = self::encode($subject);

        try {
            return @mail($to, $subj, $body, implode("\r\n", $headers));
        } catch (Throwable $e) {
            error_log('Mail error: ' . $e->getMessage());
            return false;
        }
    }

    private static function encode(string $s): string {
        return '=?UTF-8?B?' . base64_encode($s) . '?=';
    }

    private static function wrapTemplate(string $title, string $content): string {
        $brand = e(setting('spa_name', 'Spa'));
        $logo  = brand_logo();
        $color = e(setting('color_primary', '#6b8a7a'));
        $accent = e(setting('color_accent', '#c9a96e'));
        $address = e(setting('spa_address', ''));
        $phone   = e(setting('spa_phone', ''));

        return <<<HTML
<!DOCTYPE html>
<html lang="es"><head><meta charset="UTF-8"><title>{$title}</title></head>
<body style="margin:0;padding:0;background:#f5f7f6;font-family:'Inter',Helvetica,Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f7f6;padding:40px 0;">
    <tr><td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,.08);overflow:hidden;">
        <tr>
          <td style="background:{$color};padding:24px;text-align:center;">
            <h1 style="color:#fff;margin:0;font-family:Georgia,serif;font-size:24px;">{$brand}</h1>
          </td>
        </tr>
        <tr>
          <td style="padding:32px;color:#2c3e35;line-height:1.6;">
            {$content}
          </td>
        </tr>
        <tr>
          <td style="background:#f5f7f6;padding:20px;text-align:center;color:#7a8a82;font-size:12px;">
            <p style="margin:0;">{$brand}</p>
            <p style="margin:4px 0;">{$address}</p>
            <p style="margin:4px 0;">{$phone}</p>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body></html>
HTML;
    }

    /** Email de confirmación de cita pendiente */
    public static function sendBookingPending(array $appointment, array $customer, array $service): bool {
        $name  = e($customer['first_name']);
        $svc   = e($service['name']);
        $date  = dt($appointment['starts_at'], 'l, d \d\e F \d\e Y');
        $time  = dt($appointment['starts_at'], 'H:i');
        $brand = e(brand_name());
        $body = "
            <h2 style=\"color:#4a6356;\">Hola {$name},</h2>
            <p>¡Gracias por tu solicitud de cita en <strong>{$brand}</strong>!</p>
            <p>Hemos recibido los siguientes datos:</p>
            <table style=\"background:#f5f7f6;padding:16px;border-radius:8px;width:100%;\">
              <tr><td><strong>Servicio:</strong></td><td>{$svc}</td></tr>
              <tr><td><strong>Fecha:</strong></td><td>{$date}</td></tr>
              <tr><td><strong>Hora:</strong></td><td>{$time}</td></tr>
            </table>
            <p>Tu cita queda <strong>pendiente de confirmación</strong>. Te contactaremos en breve por teléfono o WhatsApp para confirmarla.</p>
            <p>Si necesitas modificarla o cancelarla, responde a este email o llámanos.</p>
            <p>Hasta pronto,<br><em>{$brand}</em></p>
        ";
        return self::send($customer['email'], "Solicitud de cita recibida – {$brand}", $body);
    }

    public static function sendBookingConfirmed(array $appointment, array $customer, array $service): bool {
        $name  = e($customer['first_name']);
        $svc   = e($service['name']);
        $date  = dt($appointment['starts_at'], 'l, d \d\e F \d\e Y');
        $time  = dt($appointment['starts_at'], 'H:i');
        $brand = e(brand_name());
        $body = "
            <h2 style=\"color:#4a6356;\">¡Cita confirmada, {$name}!</h2>
            <p>Tu cita en <strong>{$brand}</strong> ha sido confirmada.</p>
            <table style=\"background:#f5f7f6;padding:16px;border-radius:8px;width:100%;\">
              <tr><td><strong>Servicio:</strong></td><td>{$svc}</td></tr>
              <tr><td><strong>Fecha:</strong></td><td>{$date}</td></tr>
              <tr><td><strong>Hora:</strong></td><td>{$time}</td></tr>
            </table>
            <p>Te esperamos. <strong>Llega 10 minutos antes</strong> para disfrutar de la experiencia completa.</p>
        ";
        return self::send($customer['email'], "Cita confirmada – {$brand}", $body);
    }

    public static function sendGiftCard(array $card): bool {
        $code = e($card['code']);
        $amount = money($card['initial_amount']);
        $expires = dt($card['expires_at'], 'd/m/Y');
        $message = e($card['message'] ?? '');
        $body = "
            <h2 style=\"color:#4a6356;\">¡Has recibido una tarjeta regalo!</h2>
            " . ($message ? "<p><em>\"{$message}\"</em></p>" : '') . "
            <div style=\"background:linear-gradient(135deg,#c9a96e,#a88a52);padding:30px;border-radius:12px;text-align:center;color:#fff;margin:20px 0;\">
                <div style=\"font-size:14px;letter-spacing:3px;text-transform:uppercase;opacity:.9;\">Saldo</div>
                <div style=\"font-size:36px;font-weight:bold;margin:8px 0;font-family:Georgia,serif;\">{$amount}</div>
                <div style=\"font-size:18px;font-family:monospace;background:rgba(255,255,255,.2);padding:8px 16px;border-radius:6px;display:inline-block;\">{$code}</div>
            </div>
            <p>Presenta este código en tu próxima visita. Válida hasta el <strong>{$expires}</strong>.</p>
        ";
        return self::send($card['recipient_email'] ?: $card['buyer_email'], "Tu tarjeta regalo – " . brand_name(), $body);
    }
}
