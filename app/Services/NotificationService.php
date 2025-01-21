<?php
namespace App\Services;

use App\Models\User;
use App\Models\Patient;
use App\Models\RendezVous;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Envoyer une notification par email
     */
    public function envoyerEmail($destinataire, $sujet, $contenu, $template = 'emails.notification')
    {
        try {
            Mail::send($template, ['contenu' => $contenu], function($message) use ($destinataire, $sujet) {
                $message->to($destinataire)
                        ->subject($sujet);
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur d\'envoi d\'email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Envoyer un rappel de rendez-vous
     */
    public function envoyerRappelRDV(RendezVous $rdv)
    {
        $patient = $rdv->patient;
        
        // Email au patient
        if ($patient->email) {
            $this->envoyerEmail(
                $patient->email,
                'Rappel de rendez-vous',
                [
                    'patient' => $patient,
                    'rdv' => $rdv,
                    'medecin' => $rdv->medecin
                ],
                'emails.rappel-rdv'
            );
        }

        // SMS si numéro disponible
        if ($patient->telephone) {
            $this->envoyerSMS(
                $patient->telephone,
                "Rappel: RDV le " . $rdv->date_rdv->format('d/m/Y H:i') . 
                " avec Dr. " . $rdv->medecin->nom
            );
        }
    }

    /**
     * Envoyer une notification urgente
     */
    public function envoyerNotificationUrgente($patient, $type, $message)
    {
        // Notification email
        if ($patient->email) {
            $this->envoyerEmail(
                $patient->email,
                'Notification urgente - ' . $type,
                ['message' => $message],
                'emails.urgence'
            );
        }

        // SMS urgent
        if ($patient->telephone) {
            $this->envoyerSMS($patient->telephone, $message, true);
        }

        // Notifier le médecin traitant
        if ($patient->medecin_traitant) {
            $this->notifierMedecin($patient->medecin_traitant, $type, $message, $patient);
        }

        // Log de la notification
        Log::channel('notifications')->info('Notification urgente envoyée', [
            'patient_id' => $patient->patient_id,
            'type' => $type,
            'message' => $message
        ]);
    }

    /**
     * Envoyer un SMS
     */
    private function envoyerSMS($numero, $message, $urgent = false)
    {
        try {
            // Implémenter l'intégration avec un service SMS
            // Exemple avec Twilio ou autre service
            return true;
        } catch (\Exception $e) {
            Log::error('Erreur d\'envoi SMS: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Notifier un médecin
     */
    private function notifierMedecin($medecin, $type, $message, $patient)
    {
        $this->envoyerEmail(
            $medecin->email,
            'Notification médicale - ' . $type,
            [
                'medecin' => $medecin,
                'patient' => $patient,
                'message' => $message
            ],
            'emails.notification-medecin'
        );
    }
}