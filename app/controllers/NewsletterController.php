<?php
/**
 * Newsletter: envío masivo con segmentación.
 */
class NewsletterController {
    public function __construct() { Auth::requireRole(['admin','gerente']); }

    public function index(): void {
        $history = Database::fetchAll(
            "SELECT * FROM newsletters ORDER BY created_at DESC LIMIT 50"
        );
        view('admin/newsletter/index', compact('history'));
    }

    public function send(): void {
        csrf_verify();
        $segment = $_POST['segment'] ?? 'all';
        $subject = trim($_POST['subject']);
        $body    = trim($_POST['body']);
        if (!$subject || !$body) { flash('error','Asunto y mensaje obligatorios.'); back(); }

        $sql = "SELECT * FROM customers WHERE email IS NOT NULL AND accepts_marketing = 1";
        $params = [];
        switch ($segment) {
            case 'vip':
                $sql .= " AND vip = 1";
                break;
            case 'inactive':
                $sql .= " AND NOT EXISTS (SELECT 1 FROM appointments WHERE customer_id = customers.id AND starts_at >= DATE_SUB(NOW(), INTERVAL 60 DAY))";
                break;
            case 'recent':
                $sql .= " AND EXISTS (SELECT 1 FROM appointments WHERE customer_id = customers.id AND starts_at >= DATE_SUB(NOW(), INTERVAL 30 DAY))";
                break;
        }
        $recipients = Database::fetchAll($sql, $params);

        $sent = 0;
        foreach ($recipients as $c) {
            $personalized = str_replace(['{{name}}','{{firstname}}'], [$c['first_name'],$c['first_name']], $body);
            if (Mail::send($c['email'], $subject, nl2br($personalized))) $sent++;
        }
        Database::insert('newsletters', [
            'subject'    => $subject,
            'body'       => $body,
            'segment'    => $segment,
            'recipients' => count($recipients),
            'sent'       => $sent,
            'user_id'    => Auth::id(),
        ]);
        audit('newsletter_send','newsletter',null,"$sent/" . count($recipients));
        flash('success', "Newsletter enviado a {$sent} de " . count($recipients) . " destinatarios.");
        redirect('/admin/newsletter');
    }
}
