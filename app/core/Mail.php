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

    /** Email de confirmación de cita pendiente con botones de 1 click */
    public static function sendBookingPending(array $appointment, array $customer, array $service): bool {
        if (empty($customer['email'])) return false;
        $name  = e($customer['first_name']);
        $svc   = e($service['name']);
        $date  = dt($appointment['starts_at'], 'l, d \d\e F \d\e Y');
        $time  = dt($appointment['starts_at'], 'H:i');
        $brand = e(brand_name());
        $confirmUrl = url('/cita/confirmar/' . $appointment['confirmation_token']);
        $cancelUrl  = url('/cita/cancelar/'  . $appointment['confirmation_token']);
        $body = "
            <h2 style=\"color:#4a6356;\">Hola {$name},</h2>
            <p>¡Gracias por tu solicitud de cita en <strong>{$brand}</strong>!</p>
            <table style=\"background:#f5f7f6;padding:16px;border-radius:8px;width:100%;margin:16px 0;\">
              <tr><td><strong>Servicio:</strong></td><td>{$svc}</td></tr>
              <tr><td><strong>Fecha:</strong></td><td>{$date}</td></tr>
              <tr><td><strong>Hora:</strong></td><td>{$time}</td></tr>
            </table>
            <p>Confirma o cancela tu cita con un solo clic:</p>
            <p style=\"text-align:center;margin:30px 0;\">
              <a href=\"{$confirmUrl}\" style=\"background:#28a745;color:#fff;padding:14px 28px;border-radius:30px;text-decoration:none;margin:6px;display:inline-block;\">✓ Confirmar cita</a>
              <a href=\"{$cancelUrl}\" style=\"background:#dc3545;color:#fff;padding:14px 28px;border-radius:30px;text-decoration:none;margin:6px;display:inline-block;\">✕ Cancelar</a>
            </p>
            <p style=\"font-size:12px;color:#7a8a82;\">Si los botones no funcionan, copia este enlace: {$confirmUrl}</p>
        ";
        return self::send($customer['email'], "Tu cita en {$brand} - Confirma con 1 clic", $body);
    }

    /** Email recordatorio 24h antes */
    public static function sendReminder(array $appointment, array $customer, array $service): bool {
        if (empty($customer['email'])) return false;
        $name  = e($customer['first_name']);
        $svc   = e($service['name']);
        $time  = dt($appointment['starts_at'], 'H:i');
        $brand = e(brand_name());
        $body = "
            <h2 style=\"color:#4a6356;\">Te esperamos mañana, {$name} 👋</h2>
            <p>Este es un recordatorio amistoso de tu cita:</p>
            <table style=\"background:#f5f7f6;padding:16px;border-radius:8px;width:100%;\">
              <tr><td><strong>Servicio:</strong></td><td>{$svc}</td></tr>
              <tr><td><strong>Hora:</strong></td><td>{$time}</td></tr>
            </table>
            <p><strong>Recomendación:</strong> llega 10 minutos antes para disfrutar de la experiencia completa.</p>
        ";
        return self::send($customer['email'], "Recordatorio: tu cita mañana en {$brand}", $body);
    }

    /** Email de cumpleaños con descuento */
    public static function sendBirthday(array $customer, string $promoCode, int $discountPercent): bool {
        if (empty($customer['email'])) return false;
        $name  = e($customer['first_name']);
        $brand = e(brand_name());
        $body = "
            <h2 style=\"color:#4a6356;\">¡Feliz cumpleaños, {$name}! 🎉</h2>
            <p>En tu día especial queremos regalarte un <strong>{$discountPercent}% de descuento</strong> en cualquier servicio.</p>
            <div style=\"background:linear-gradient(135deg,#c9a96e,#a88a52);padding:30px;border-radius:12px;text-align:center;color:#fff;margin:20px 0;\">
                <div style=\"font-size:14px;letter-spacing:3px;text-transform:uppercase;opacity:.9;\">Código de descuento</div>
                <div style=\"font-size:32px;font-weight:bold;margin:8px 0;font-family:monospace;\">{$promoCode}</div>
                <div style=\"font-size:14px;opacity:.9;\">Válido durante todo el mes</div>
            </div>
            <p><a href=\"" . url('/reservar') . "\" style=\"color:#c9a96e;\">Reserva tu cita aquí</a></p>
        ";
        return self::send($customer['email'], "🎂 ¡Feliz cumpleaños! Un regalo de {$brand}", $body);
    }

    /** Win-back: clientes inactivos */
    public static function sendWinback(array $customer, string $promoCode, int $discountPercent): bool {
        if (empty($customer['email'])) return false;
        $name  = e($customer['first_name']);
        $brand = e(brand_name());
        $body = "
            <h2 style=\"color:#4a6356;\">Te echamos de menos, {$name}</h2>
            <p>Hace tiempo que no nos visitas y queremos recibirte de vuelta con un <strong>{$discountPercent}% de descuento</strong>.</p>
            <div style=\"background:#f5f7f6;padding:24px;border-radius:12px;text-align:center;margin:20px 0;\">
                <div style=\"font-size:24px;font-family:monospace;color:#c9a96e;\"><strong>{$promoCode}</strong></div>
            </div>
            <p><a href=\"" . url('/reservar') . "\" style=\"background:#6b8a7a;color:#fff;padding:12px 24px;border-radius:30px;text-decoration:none;\">Reservar ahora</a></p>
        ";
        return self::send($customer['email'], "Te extrañamos – Un regalo de {$brand}", $body);
    }

    /** Encuesta NPS post-servicio */
    public static function sendSurvey(array $appointment, array $customer, array $service): bool {
        if (empty($customer['email'])) return false;
        $name  = e($customer['first_name']);
        $svc   = e($service['name']);
        $brand = e(brand_name());
        $url   = url('/cita/encuesta/' . $appointment['confirmation_token']);
        $body = "
            <h2 style=\"color:#4a6356;\">¿Cómo te sentiste, {$name}?</h2>
            <p>Esperamos que hayas disfrutado tu <strong>{$svc}</strong> en {$brand}.</p>
            <p>Tu opinión nos ayuda a mejorar. ¿Podrías dedicarnos 30 segundos?</p>
            <p style=\"text-align:center;margin:30px 0;\">
              <a href=\"{$url}\" style=\"background:#c9a96e;color:#fff;padding:14px 32px;border-radius:30px;text-decoration:none;\">Responder encuesta</a>
            </p>
        ";
        return self::send($customer['email'], "Tu opinión sobre tu visita a {$brand}", $body);
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
