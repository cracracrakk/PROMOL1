<?php
class AppointmentToken
{
    public static function generate(int $appointmentId, string $action, ?int $hoursTtl = 168): string
    {
        $token = bin2hex(random_bytes(24));
        Database::insert('appointment_tokens', [
            'appointment_id' => $appointmentId,
            'token'          => $token,
            'action'         => $action,
            'expires_at'     => date('Y-m-d H:i:s', strtotime("+{$hoursTtl} hours")),
        ]);
        return $token;
    }

    public static function find(string $token): ?array
    {
        return Database::one(
            'SELECT t.*, a.patient_id, a.dentist_id, a.starts_at, a.ends_at, a.status,
                    CONCAT(p.first_name," ",p.last_name) AS patient_name,
                    u.name AS dentist_name
             FROM appointment_tokens t
             JOIN appointments a ON a.id = t.appointment_id
             JOIN patients p ON p.id = a.patient_id
             JOIN users    u ON u.id = a.dentist_id
             WHERE t.token = ? AND t.expires_at > NOW()',
            [$token]
        );
    }

    public static function markUsed(int $id): void
    {
        Database::update('appointment_tokens', ['used_at' => now()], ['id' => $id]);
    }
}

class Communication
{
    public static function log(array $data): int
    {
        $allowed = ['patient_id','appointment_id','channel','direction','subject','body','to','status','sent_at','error'];
        return Database::insert('communications', only($data, $allowed));
    }

    public static function recent(int $limit = 50): array
    {
        return Database::query(
            'SELECT c.*, CONCAT(p.first_name," ",p.last_name) AS patient_name
             FROM communications c
             LEFT JOIN patients p ON p.id = c.patient_id
             ORDER BY c.created_at DESC LIMIT ' . (int)$limit
        );
    }
}
