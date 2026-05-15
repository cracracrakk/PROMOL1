<?php
class Periodontogram
{
    public static function listFor(int $patientId): array
    {
        return Database::query(
            'SELECT p.*, u.name AS examiner_name
             FROM periodontograms p
             LEFT JOIN users u ON u.id = p.examiner_id
             WHERE p.patient_id = ?
             ORDER BY p.exam_date DESC, p.id DESC',
            [$patientId]
        );
    }

    public static function find(int $id): ?array
    {
        $p = Database::one(
            'SELECT p.*, u.name AS examiner_name
             FROM periodontograms p
             LEFT JOIN users u ON u.id = p.examiner_id
             WHERE p.id = ?', [$id]
        );
        if (!$p) return null;
        $teeth = Database::query(
            'SELECT * FROM periodontogram_teeth WHERE periodontogram_id = ?', [$id]
        );
        $byTooth = [];
        foreach ($teeth as $t) $byTooth[$t['tooth_code']] = $t;
        $p['teeth'] = $byTooth;
        return $p;
    }

    public static function create(int $patientId, ?string $date = null, ?int $examinerId = null, ?string $notes = null): int
    {
        return Database::insert('periodontograms', [
            'patient_id' => $patientId,
            'exam_date'  => $date ?? date('Y-m-d'),
            'examiner_id'=> $examinerId,
            'notes'      => $notes,
        ]);
    }

    public static function upsertTooth(int $periodontogramId, string $toothCode, array $data): void
    {
        $allowed = ['pd_vm','pd_vc','pd_vd','pd_lm','pd_lc','pd_ld',
                    'rec_vm','rec_vc','rec_vd','rec_lm','rec_lc','rec_ld',
                    'bleeding','plaque','suppuration','mobility','furcation'];
        $clean = only($data, $allowed);

        $existing = Database::one(
            'SELECT id FROM periodontogram_teeth WHERE periodontogram_id = ? AND tooth_code = ?',
            [$periodontogramId, $toothCode]
        );
        if ($existing) {
            Database::update('periodontogram_teeth', $clean, ['id' => $existing['id']]);
        } else {
            $clean['periodontogram_id'] = $periodontogramId;
            $clean['tooth_code']        = $toothCode;
            Database::insert('periodontogram_teeth', $clean);
        }
    }
}
