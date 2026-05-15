<?php
class Prescription
{
    public static function nextCode(): string
    {
        $last = (int)Database::value('SELECT IFNULL(MAX(id),0) FROM prescriptions');
        return next_code('RX', $last);
    }

    public static function find(int $id): ?array
    {
        $rx = Database::one(
            'SELECT r.*, CONCAT(p.first_name," ",p.last_name) AS patient_name,
                    p.code AS patient_code, p.document_number, p.birth_date,
                    u.name AS dentist_name, u.license_number AS dentist_license,
                    u.specialty AS dentist_specialty
             FROM prescriptions r
             JOIN patients p ON p.id = r.patient_id
             JOIN users    u ON u.id = r.dentist_id
             WHERE r.id = ?', [$id]
        );
        if (!$rx) return null;
        $rx['items'] = Database::query('SELECT * FROM prescription_items WHERE prescription_id = ?', [$id]);
        return $rx;
    }

    public static function listFor(int $patientId): array
    {
        return Database::query(
            'SELECT r.*, u.name AS dentist_name,
                    (SELECT COUNT(*) FROM prescription_items WHERE prescription_id = r.id) AS items_count
             FROM prescriptions r JOIN users u ON u.id = r.dentist_id
             WHERE r.patient_id = ? ORDER BY r.issue_date DESC, r.id DESC',
            [$patientId]
        );
    }

    public static function create(array $data, array $items): int
    {
        return Database::transaction(function () use ($data, $items) {
            $data['code'] = $data['code'] ?? self::nextCode();
            $data['issue_date'] = $data['issue_date'] ?? date('Y-m-d');
            $allowed = ['code','patient_id','dentist_id','issue_date','diagnosis','notes'];
            $id = Database::insert('prescriptions', only($data, $allowed));
            foreach ($items as $it) {
                if (empty($it['drug'])) continue;
                $it['prescription_id'] = $id;
                $itAllowed = ['prescription_id','drug','dosage','frequency','duration','instructions'];
                Database::insert('prescription_items', only($it, $itAllowed));
            }
            return $id;
        });
    }
}
