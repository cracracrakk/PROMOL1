<?php
class PublicAppointmentController extends Controller
{
    public function confirm(string $token): void
    {
        $t = AppointmentToken::find($token);
        if (!$t || $t['action'] !== 'confirm' || $t['used_at']) {
            $this->view('public/token_invalid', ['_title' => 'Enlace no válido'], 'public');
            return;
        }
        Appointment::updateStatus((int)$t['appointment_id'], 'confirmed');
        AppointmentToken::markUsed((int)$t['id']);
        Auth::log('confirm', 'appointment', $t['appointment_id'], ['source' => 'public_token']);
        $this->view('public/appointment_action', [
            '_title' => 'Cita confirmada',
            'action' => 'confirm',
            'data'   => $t,
        ], 'public');
    }

    public function cancel(string $token): void
    {
        $t = AppointmentToken::find($token);
        if (!$t || $t['action'] !== 'cancel' || $t['used_at']) {
            $this->view('public/token_invalid', ['_title' => 'Enlace no válido'], 'public');
            return;
        }
        Appointment::updateStatus((int)$t['appointment_id'], 'cancelled');
        AppointmentToken::markUsed((int)$t['id']);
        Auth::log('cancel', 'appointment', $t['appointment_id'], ['source' => 'public_token']);
        $this->view('public/appointment_action', [
            '_title' => 'Cita cancelada',
            'action' => 'cancel',
            'data'   => $t,
        ], 'public');
    }

    public function survey(string $token): void
    {
        $t = AppointmentToken::find($token);
        if (!$t || $t['action'] !== 'survey') {
            $this->view('public/token_invalid', ['_title' => 'Enlace no válido'], 'public');
            return;
        }
        $this->view('public/survey', [
            '_title' => 'Tu opinión nos importa',
            'token'  => $token,
            'data'   => $t,
            'submitted' => (bool)$t['used_at'],
        ], 'public');
    }

    public function submitSurvey(string $token): void
    {
        $this->requireCsrf();
        $t = AppointmentToken::find($token);
        if (!$t || $t['action'] !== 'survey' || $t['used_at']) {
            redirect(url('/'));
        }
        Database::insert('surveys', [
            'appointment_id' => $t['appointment_id'],
            'score'          => (int)input('score', 0),
            'comment'        => (string)input('comment', ''),
            'submitted_at'   => now(),
        ]);
        AppointmentToken::markUsed((int)$t['id']);
        $this->view('public/survey_thanks', ['_title' => 'Gracias'], 'public');
    }
}
