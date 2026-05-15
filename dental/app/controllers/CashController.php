<?php
class CashController extends Controller
{
    public function index(): void
    {
        $this->requireAuth(['admin','dentist','reception']);
        $current = CashSession::current();
        $history = CashSession::history(20);
        $expenses = Expense::recent(7);

        $this->view('admin/cash/index', [
            '_title'  => 'Caja diaria',
            'current' => $current,
            'history' => $history,
            'expenses'=> $expenses,
            'movements' => $current ? CashSession::movementsFor((int)$current['id']) : null,
            'expected'  => $current ? CashSession::expectedCash((int)$current['id']) : 0,
        ]);
    }

    public function open(): void
    {
        $this->requireAuth(['admin','dentist','reception']);
        $this->requireCsrf();
        try {
            $id = CashSession::open(Auth::id(), (float)input('opening_amount', 0), (string)input('notes', ''));
            Auth::log('open', 'cash_session', $id);
            flash('success', 'Caja abierta.');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }
        redirect(url('/admin/caja'));
    }

    public function close(): void
    {
        $this->requireAuth(['admin','dentist','reception']);
        $this->requireCsrf();
        $current = CashSession::current();
        if (!$current) {
            flash('error', 'No hay caja abierta.');
            redirect(url('/admin/caja'));
        }
        $result = CashSession::close(
            (int)$current['id'],
            Auth::id(),
            (float)input('closing_amount', 0),
            (string)input('notes', '')
        );
        Auth::log('close', 'cash_session', $current['id'], $result);
        flash('success', sprintf(
            'Caja cerrada. Esperado: %s · Contado: %s · Diferencia: %s',
            money($result['expected']), money($result['closing']), money($result['difference'])
        ));
        redirect(url('/admin/caja'));
    }

    public function show(string $id): void
    {
        $this->requireAuth();
        $session = CashSession::find((int)$id);
        if (!$session) { http_response_code(404); return; }
        $this->view('admin/cash/show', [
            '_title'   => 'Sesión de caja #' . $id,
            'session'  => $session,
            'movements'=> CashSession::movementsFor((int)$id),
        ]);
    }

    public function storeExpense(): void
    {
        $this->requireAuth(['admin','dentist','reception']);
        $this->requireCsrf();
        $current = CashSession::current();
        Expense::create([
            'expense_date'    => input('expense_date', date('Y-m-d')),
            'category'        => (string)input('category', ''),
            'description'     => (string)input('description', ''),
            'amount'          => (float)input('amount', 0),
            'method'          => (string)input('method', 'cash'),
            'cash_session_id' => $current ? (int)$current['id'] : null,
            'created_by'      => Auth::id(),
        ]);
        Auth::log('create', 'expense');
        flash('success', 'Gasto registrado.');
        redirect(url('/admin/caja'));
    }
}
