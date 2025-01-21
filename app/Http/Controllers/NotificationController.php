<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\RendezVous;
use App\Models\User;
use App\Models\Patient;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function envoyerRappelRDV()
    {
        $rdvProchains = RendezVous::with(['patient', 'medecin'])
            ->where('date_rdv', '>', now())
            ->where('date_rdv', '<=', now()->addDays(2))
            ->where('statut', 'programmé')
            ->get();

        foreach ($rdvProchains as $rdv) {
            $this->notificationService->envoyerRappelRDV($rdv);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Rappels envoyés avec succès',
            'count' => $rdvProchains->count()
        ]);
    }

    public function notifierUrgence(Request $request)
    {
        $request->validate([
            'type' => 'required|in:alerte_medicale,rappel_traitement,notification_resultat',
            'patient_id' => 'required|exists:patients,patient_id',
            'message' => 'required|string'
        ]);

        $patient = Patient::findOrFail($request->patient_id);
        $this->notificationService->envoyerNotificationUrgente($patient, $request->type, $request->message);

        return response()->json([
            'status' => 'success',
            'message' => 'Notification urgente envoyée'
        ]);
    }

    public function getNotificationsNonLues()
    {
        $user = auth()->user();
        return response()->json([
            'status' => 'success',
            'data' => $user->notifications()->unread()->get()
        ]);
    }

    public function marquerCommeLue($notificationId)
    {
        $notification = auth()->user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();

        return response()->json([
            'status' => 'success',
            'message' => 'Notification marquée comme lue'
        ]);
    }
}