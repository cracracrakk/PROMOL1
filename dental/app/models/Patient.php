<?php
class Patient
{
    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM patients WHERE id = ?', [$id]);
    }

    public static function findWithHistory(int $id): ?array
    {
        $p = self::find($id);
        if (!$p) return null;
        $p['medical_history'] = Database::one(
            'SELECT * FROM patient_medical_history WHERE patient_id = ?', [$id]
        );
        return $p;
    }

    public static function search(string $q = '', int $page = 1, int $perPage = 20): array
    {
        $offset = max(0, ($page - 1) * $perPage);
        $where = 'WHERE 1=1';
        $params = [];
        if ($q !== '') {
            $where .= ' AND (CONCAT(first_name," ",last_name) LIKE ?
                        OR document_number LIKE ? OR phone LIKE ? OR mobile LIKE ? OR code LIKE ?)';
            $like = "%$q%";
            $params = [$like, $like, $like, $like, $like];
        }
        $rows = Database::query(
            "SELECT id, code, first_name, last_name, document_number, birth_date, gender,
                    phone, mobile, email, is_active, created_at
             FROM patients $where
             ORDER BY last_name, first_name
             LIMIT $perPage OFFSET $offset",
            $params
        );
        $total = (int)Database::value("SELECT COUNT(*) FROM patients $where", $params);

        return [
            'data'       => $rows,
            'total'      => $total,
            'page'       => $page,
            'per_page'   => $perPage,
            'last_page'  => max(1, (int)ceil($total / $perPage)),
        ];
    }

    public static function nextCode(): string
    {
        $last = (int)Database::value('SELECT IFNULL(MAX(id),0) FROM patients');
        return next_code('P', $last);
    }

    public static function create(array $data): int
    {
        $data['code'] = $data['code'] ?? self::nextCode();
        $allowed = ['code','first_name','last_name','document_type','document_number',
                    'birth_date','gender','blood_type','email','phone','mobile',
                    'address','city','state','occupation','emergency_name',
                    'emergency_phone','emergency_rel','referred_by','notes','created_by'];
        return Database::insert('patients', only($data, $allowed));
    }

    public static function update(int $id, array $data): int
    {
        $allowed = ['first_name','last_name','document_type','document_number',
                    'birth_date','gender','blood_type','email','phone','mobile',
                    'address','city','state','occupation','emergency_name',
                    'emergency_phone','emergency_rel','referred_by','notes','is_active'];
        return Database::update('patients', only($data, $allowed), ['id' => $id]);
    }

    public static function delete(int $id): int
    {
        return Database::execute('UPDATE patients SET is_active = 0 WHERE id = ?', [$id]);
    }

    public static function saveMedicalHistory(int $patientId, array $data): void
    {
        $allowed = ['allergies','chronic_conditions','current_medications','past_surgeries',
                    'smokes','drinks_alcohol','pregnant','diabetes','hypertension',
                    'heart_disease','bleeding_disorders','notes'];
        $clean = only($data, $allowed);

        $exists = Database::value('SELECT 1 FROM patient_medical_history WHERE patient_id = ?', [$patientId]);
        if ($exists) {
            Database::update('patient_medical_history', $clean, ['patient_id' => $patientId]);
        } else {
            $clean['patient_id'] = $patientId;
            Database::insert('patient_medical_history', $clean);
        }
    }

    public static function stats(): array
    {
        return [
            'total'     => (int)Database::value('SELECT COUNT(*) FROM patients WHERE is_active = 1'),
            'new_month' => (int)Database::value(
                "SELECT COUNT(*) FROM patients
                 WHERE DATE_FORMAT(created_at,'%Y-%m') = DATE_FORMAT(NOW(),'%Y-%m')"
            ),
        ];
    }
}
