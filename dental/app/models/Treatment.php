<?php
class Treatment
{
    public static function all(bool $onlyActive = true): array
    {
        $sql = 'SELECT * FROM treatments';
        if ($onlyActive) $sql .= ' WHERE is_active = 1';
        $sql .= ' ORDER BY category, name';
        return Database::query($sql);
    }

    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM treatments WHERE id = ?', [$id]);
    }

    public static function byCategory(): array
    {
        $rows = self::all();
        $grouped = [];
        foreach ($rows as $r) {
            $grouped[$r['category'] ?: 'Otros'][] = $r;
        }
        return $grouped;
    }

    public static function create(array $data): int
    {
        $allowed = ['code','name','category','description','default_price','duration_min','requires_tooth','is_active'];
        return Database::insert('treatments', only($data, $allowed));
    }

    public static function update(int $id, array $data): int
    {
        $allowed = ['code','name','category','description','default_price','duration_min','requires_tooth','is_active'];
        return Database::update('treatments', only($data, $allowed), ['id' => $id]);
    }

    public static function delete(int $id): int
    {
        return Database::update('treatments', ['is_active' => 0], ['id' => $id]);
    }
}

class ClinicalNote
{
    public static function forPatient(int $patientId, int $limit = 50): array
    {
        return Database::query(
            'SELECT n.*, u.name AS dentist_name
             FROM clinical_notes n
             JOIN users u ON u.id = n.dentist_id
             WHERE n.patient_id = ?
             ORDER BY n.visit_date DESC, n.id DESC
             LIMIT ' . (int)$limit,
            [$patientId]
        );
    }

    public static function create(array $data): int
    {
        $allowed = ['patient_id','dentist_id','appointment_id','visit_date',
                    'chief_complaint','diagnosis','treatment_done','prescription','next_visit'];
        return Database::insert('clinical_notes', only($data, $allowed));
    }

    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM clinical_notes WHERE id = ?', [$id]);
    }
}

class TreatmentPlan
{
    public static function nextCode(): string
    {
        $last = (int)Database::value('SELECT IFNULL(MAX(id),0) FROM treatment_plans');
        return next_code('PLAN', $last);
    }

    public static function find(int $id): ?array
    {
        $plan = Database::one('SELECT * FROM treatment_plans WHERE id = ?', [$id]);
        if (!$plan) return null;
        $plan['items'] = Database::query(
            'SELECT i.*, t.name AS treatment_name, t.code AS treatment_code
             FROM treatment_plan_items i
             JOIN treatments t ON t.id = i.treatment_id
             WHERE i.treatment_plan_id = ?',
            [$id]
        );
        return $plan;
    }

    public static function forPatient(int $patientId): array
    {
        return Database::query(
            'SELECT p.*, u.name AS dentist_name
             FROM treatment_plans p
             JOIN users u ON u.id = p.dentist_id
             WHERE p.patient_id = ?
             ORDER BY p.created_at DESC',
            [$patientId]
        );
    }

    public static function create(array $data, array $items = []): int
    {
        return Database::transaction(function () use ($data, $items) {
            $data['code'] = $data['code'] ?? self::nextCode();
            $total = 0;
            foreach ($items as $it) $total += (float)$it['line_total'];
            $data['total_amount'] = $total - (float)($data['discount'] ?? 0);

            $allowed = ['patient_id','dentist_id','code','title','diagnosis','status',
                        'total_amount','discount','notes'];
            $id = Database::insert('treatment_plans', only($data, $allowed));
            foreach ($items as $it) {
                $it['treatment_plan_id'] = $id;
                $itAllowed = ['treatment_plan_id','treatment_id','tooth_code','surface',
                              'quantity','unit_price','line_total','status','notes'];
                Database::insert('treatment_plan_items', only($it, $itAllowed));
            }
            return $id;
        });
    }
}
