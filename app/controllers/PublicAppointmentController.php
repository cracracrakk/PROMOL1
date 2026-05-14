<?php
/**
 * Acciones públicas sobre citas mediante token seguro:
 * - Confirmar / cancelar desde el email recibido
 * - Sin necesidad de login
 */
class PublicAppointmentController {

    public function confirm(string $token): void {
        $appointment = $this->findByToken($token);
        if (!$appointment) { $this->showError(); return; }
        if ($appointment['status'] === 'pendiente') {
            Database::update('appointments', ['status' => 'confirmada'],
                'id = :id', ['id' => $appointment['id']]);
            audit('confirm_token','appointment',(int)$appointment['id']);
        }
        $message = '¡Tu cita ha sido confirmada! Te esperamos.';
        $this->showSuccess($appointment, $message);
    }

    public function cancel(string $token): void {
        $appointment = $this->findByToken($token);
        if (!$appointment) { $this->showError(); return; }
        if (in_array($appointment['status'], ['pendiente','confirmada'])) {
            Database::update('appointments', ['status' => 'cancelada'],
                'id = :id', ['id' => $appointment['id']]);
            audit('cancel_token','appointment',(int)$appointment['id']);
        }
        $this->showSuccess($appointment, 'Tu cita ha sido cancelada. ¡Esperamos verte pronto!');
    }

    public function survey(string $token): void {
        $appointment = $this->findByToken($token);
        if (!$appointment) { $this->showError(); return; }
        $appointmentId = (int)$appointment['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_verify();
            Database::insert('surveys', [
                'appointment_id' => $appointmentId,
                'customer_id'    => (int)$appointment['customer_id'],
                'nps'            => (int)($_POST['nps'] ?? 0),
                'comment'        => trim($_POST['comment'] ?? ''),
            ]);
            view('public/survey_thanks');
            return;
        }
        view('public/survey', compact('appointment','token'));
    }

    private function findByToken(string $token): ?array {
        return Database::fetch(
            'SELECT * FROM appointments WHERE confirmation_token = ? LIMIT 1',
            [$token]
        );
    }

    private function showSuccess(array $appointment, string $message): void {
        view('public/appointment_action', [
            'success'    => true,
            'message'    => $message,
            'appointment'=> $appointment,
        ]);
    }

    private function showError(): void {
        view('public/appointment_action', [
            'success' => false,
            'message' => 'Enlace inválido o caducado.',
        ]);
    }
}
