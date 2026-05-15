<?php
class TaskController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $filter = (string)input('filter', 'mine');
        $userId = Auth::id();

        $sql = 'SELECT t.*, u.name AS assigned_name,
                       CONCAT(p.first_name," ",p.last_name) AS patient_name
                FROM tasks t
                LEFT JOIN users u ON u.id = t.assigned_to
                LEFT JOIN patients p ON p.id = t.related_patient_id
                WHERE 1=1';
        $params = [];
        if ($filter === 'mine') {
            $sql .= ' AND (t.assigned_to = ? OR t.created_by = ?)';
            $params = [$userId, $userId];
        } elseif ($filter === 'pending') {
            $sql .= " AND t.status IN ('open','in_progress')";
        }
        $sql .= ' ORDER BY t.status, t.priority DESC, t.due_date IS NULL, t.due_date';

        $this->view('admin/tasks/index', [
            '_title' => 'Tareas',
            'tasks'  => Database::query($sql, $params),
            'users'  => User::all(),
            'filter' => $filter,
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();
        $this->requireCsrf();
        Database::insert('tasks', [
            'title'              => (string)input('title'),
            'description'        => (string)input('description', ''),
            'assigned_to'        => input('assigned_to') ?: null,
            'related_patient_id' => input('related_patient_id') ?: null,
            'due_date'           => input('due_date') ?: null,
            'priority'           => (string)input('priority', 'medium'),
            'status'             => 'open',
            'created_by'         => Auth::id(),
        ]);
        flash('success', 'Tarea creada.');
        redirect(url('/admin/tareas'));
    }

    public function updateStatus(string $id): void
    {
        $this->requireAuth();
        $this->requireCsrf();
        $status = (string)input('status');
        if (!in_array($status, ['open','in_progress','done','cancelled'], true)) {
            $this->json(['error' => 'Estado inválido'], 422);
        }
        $upd = ['status' => $status];
        if ($status === 'done') $upd['completed_at'] = now();
        Database::update('tasks', $upd, ['id' => (int)$id]);
        if ($this->wantsJson()) $this->json(['ok' => true]);
        redirect(url('/admin/tareas'));
    }

    public function destroy(string $id): void
    {
        $this->requireAuth();
        $this->requireCsrf();
        Database::delete('tasks', ['id' => (int)$id]);
        redirect(url('/admin/tareas'));
    }
}
