<?php
class WalletController {
    public function __construct() { Auth::requireLogin(); }

    public function topup(): void {
        csrf_verify();
        $customerId = (int)$_POST['customer_id'];
        $amount     = (float)$_POST['amount'];
        if ($amount <= 0) { flash('error','Importe inválido.'); back(); }

        Database::query(
            "INSERT INTO customer_wallets (customer_id, balance) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE balance = balance + VALUES(balance)",
            [$customerId, $amount]
        );
        Database::insert('wallet_transactions', [
            'customer_id' => $customerId,
            'amount'      => $amount,
            'type'        => 'recarga',
            'reason'      => trim($_POST['reason'] ?? 'Recarga manual'),
            'user_id'     => Auth::id(),
        ]);
        audit('wallet_topup','customer',$customerId,money($amount));
        flash('success', 'Recarga de ' . money($amount) . ' aplicada.');
        redirect('/admin/clientes/' . $customerId);
    }
}
