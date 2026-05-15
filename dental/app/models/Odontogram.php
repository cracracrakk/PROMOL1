<?php
class Odontogram
{
    public const STATUSES = [
        'healthy','caries','filled','crown','root_canal','extracted',
        'missing','implant','bridge','sealant','fractured','to_extract'
    ];

    public static function forPatient(int $patientId): array
    {
        $rows = Database::query(
            'SELECT * FROM odontogram WHERE patient_id = ?',
            [$patientId]
        );
        $byTooth = [];
        foreach ($rows as $r) $byTooth[$r['tooth_code']] = $r;

        $result = [
            'permanent' => self::buildLayout(teeth_permanent(), $byTooth, 'permanent'),
            'deciduous' => self::buildLayout(teeth_deciduous(), $byTooth, 'deciduous'),
        ];
        return $result;
    }

    private static function buildLayout(array $rows, array $byTooth, string $dentition): array
    {
        $out = [];
        foreach ($rows as $row) {
            $line = [];
            foreach ($row as $code) {
                $data = $byTooth[$code] ?? [
                    'tooth_code' => $code,
                    'status'     => 'healthy',
                    'dentition'  => $dentition,
                    'surface_v'  => null, 'surface_l' => null,
                    'surface_m'  => null, 'surface_d' => null, 'surface_o' => null,
                    'notes'      => null, 'updated_at' => null,
                ];
                $line[] = $data;
            }
            $out[] = $line;
        }
        return $out;
    }

    public static function updateTooth(int $patientId, string $toothCode, array $data, ?int $userId = null): void
    {
        $allowed = ['status','surface_v','surface_l','surface_m','surface_d','surface_o','notes','dentition'];
        $clean = only($data, $allowed);
        if (isset($clean['status']) && !in_array($clean['status'], self::STATUSES, true)) {
            throw new InvalidArgumentException("Estado inválido: {$clean['status']}");
        }

        $existing = Database::one(
            'SELECT * FROM odontogram WHERE patient_id = ? AND tooth_code = ?',
            [$patientId, $toothCode]
        );

        $clean['updated_by'] = $userId;

        if ($existing) {
            Database::update('odontogram', $clean, ['id' => $existing['id']]);
            $previousStatus = $existing['status'];
        } else {
            $clean['patient_id'] = $patientId;
            $clean['tooth_code'] = $toothCode;
            $clean['dentition'] = $clean['dentition'] ?? 'permanent';
            Database::insert('odontogram', $clean);
            $previousStatus = null;
        }

        if (!empty($clean['status']) && $clean['status'] !== $previousStatus) {
            Database::insert('odontogram_history', [
                'patient_id'      => $patientId,
                'tooth_code'      => $toothCode,
                'previous_status' => $previousStatus,
                'new_status'      => $clean['status'],
                'note'            => $clean['notes'] ?? null,
                'changed_by'      => $userId,
            ]);
        }
    }

    public static function bulkUpdate(int $patientId, array $teeth, ?int $userId = null): void
    {
        Database::transaction(function () use ($patientId, $teeth, $userId) {
            foreach ($teeth as $t) {
                if (empty($t['tooth_code'])) continue;
                self::updateTooth($patientId, $t['tooth_code'], $t, $userId);
            }
        });
    }

    public static function historyFor(int $patientId, int $limit = 50): array
    {
        return Database::query(
            'SELECT h.*, u.name AS changed_by_name
             FROM odontogram_history h
             LEFT JOIN users u ON u.id = h.changed_by
             WHERE h.patient_id = ?
             ORDER BY h.changed_at DESC
             LIMIT ' . (int)$limit,
            [$patientId]
        );
    }
}
