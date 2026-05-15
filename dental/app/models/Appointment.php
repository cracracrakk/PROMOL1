<?php
class Appointment
{
    public static function find(int $id): ?array
    {
        return Database::one(
            'SELECT a.*,
                    CONCAT(p.first_name," ",p.last_name) AS patient_name, p.code AS patient_code,
                    p.phone AS patient_phone, p.mobile AS patient_mobile,
                    u.name AS dentist_name,
                    r.name AS room_name, r.color AS room_color,
                    t.name AS treatment_name
             FROM appointments a
             JOIN patients p ON p.id = a.patient_id
             JOIN users    u ON u.id = a.dentist_id
             LEFT JOIN rooms r ON r.id = a.room_id
             LEFT JOIN treatments t ON t.id = a.treatment_id
             WHERE a.id = ?', [$id]
        );
    }

    public static function listForRange(string $from, string $to, array $filters = []): array
    {
        $sql = 'SELECT a.id, a.patient_id, a.dentist_id, a.room_id, a.treatment_id,
                       a.starts_at, a.ends_at, a.status, a.reason,
                       CONCAT(p.first_name," ",p.last_name) AS patient_name,
                       u.name AS dentist_name,
                       r.name AS room_name, r.color AS room_color,
                       t.name AS treatment_name
                FROM appointments a
                JOIN patients p ON p.id = a.patient_id
                JOIN users    u ON u.id = a.dentist_id
                LEFT JOIN rooms r ON r.id = a.room_id
                LEFT JOIN treatments t ON t.id = a.treatment_id
                WHERE a.starts_at >= ? AND a.starts_at < ?';
        $params = [$from, $to];

        if (!empty($filters['dentist_id'])) {
            $sql .= ' AND a.dentist_id = ?';
            $params[] = $filters['dentist_id'];
        }
        if (!empty($filters['status'])) {
            $sql .= ' AND a.status = ?';
            $params[] = $filters['status'];
        }
        $sql .= ' ORDER BY a.starts_at';
        return Database::query($sql, $params);
    }

    public static function hasConflict(int $dentistId, string $starts, string $ends, ?int $ignoreId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM appointments
                WHERE dentist_id = ?
                  AND status NOT IN ("cancelled","no_show")
                  AND starts_at < ? AND ends_at > ?';
        $params = [$dentistId, $ends, $starts];
        if ($ignoreId) {
            $sql .= ' AND id != ?';
            $params[] = $ignoreId;
        }
        return (int)Database::value($sql, $params) > 0;
    }

    public static function create(array $data): int
    {
        $allowed = ['patient_id','dentist_id','room_id','treatment_id','starts_at','ends_at',
                    'status','reason','notes','created_by'];
        return Database::insert('appointments', only($data, $allowed));
    }

    public static function update(int $id, array $data): int
    {
        $allowed = ['patient_id','dentist_id','room_id','treatment_id','starts_at','ends_at',
                    'status','reason','notes'];
        return Database::update('appointments', only($data, $allowed), ['id' => $id]);
    }

    public static function updateStatus(int $id, string $status): int
    {
        return Database::update('appointments', ['status' => $status], ['id' => $id]);
    }

    public static function delete(int $id): int
    {
        return Database::delete('appointments', ['id' => $id]);
    }

    public static function todaySummary(): array
    {
        return [
            'total'      => (int)Database::value("SELECT COUNT(*) FROM appointments WHERE DATE(starts_at) = CURDATE()"),
            'pending'    => (int)Database::value("SELECT COUNT(*) FROM appointments WHERE DATE(starts_at) = CURDATE() AND status IN('scheduled','confirmed')"),
            'completed'  => (int)Database::value("SELECT COUNT(*) FROM appointments WHERE DATE(starts_at) = CURDATE() AND status = 'completed'"),
            'no_show'    => (int)Database::value("SELECT COUNT(*) FROM appointments WHERE DATE(starts_at) = CURDATE() AND status = 'no_show'"),
        ];
    }

    public static function upcomingFor(int $patientId, int $limit = 5): array
    {
        return Database::query(
            'SELECT a.*, u.name AS dentist_name, t.name AS treatment_name
             FROM appointments a
             JOIN users u ON u.id = a.dentist_id
             LEFT JOIN treatments t ON t.id = a.treatment_id
             WHERE a.patient_id = ? AND a.starts_at >= NOW()
             ORDER BY a.starts_at LIMIT ' . (int)$limit,
            [$patientId]
        );
    }

    public static function historyFor(int $patientId, int $limit = 20): array
    {
        return Database::query(
            'SELECT a.*, u.name AS dentist_name, t.name AS treatment_name
             FROM appointments a
             JOIN users u ON u.id = a.dentist_id
             LEFT JOIN treatments t ON t.id = a.treatment_id
             WHERE a.patient_id = ?
             ORDER BY a.starts_at DESC LIMIT ' . (int)$limit,
            [$patientId]
        );
    }
}
