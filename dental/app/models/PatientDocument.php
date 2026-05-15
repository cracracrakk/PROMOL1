<?php
class PatientDocument
{
    public const TYPES = ['xray','photo','consent','document','other'];

    public static function listFor(int $patientId, ?string $type = null): array
    {
        $sql = 'SELECT d.*, u.name AS uploaded_by_name
                FROM patient_documents d
                LEFT JOIN users u ON u.id = d.uploaded_by
                WHERE d.patient_id = ?';
        $params = [$patientId];
        if ($type) { $sql .= ' AND d.type = ?'; $params[] = $type; }
        $sql .= ' ORDER BY d.created_at DESC';
        return Database::query($sql, $params);
    }

    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM patient_documents WHERE id = ?', [$id]);
    }

    public static function create(array $data): int
    {
        $allowed = ['patient_id','type','title','description','filename','original_name',
                    'mime','size_bytes','tooth_code','taken_at','uploaded_by'];
        return Database::insert('patient_documents', only($data, $allowed));
    }

    public static function delete(int $id): ?array
    {
        $doc = self::find($id);
        if (!$doc) return null;
        Database::delete('patient_documents', ['id' => $id]);
        return $doc;
    }
}
