<?php

namespace App\Listeners;

use App\Events\UserRequestedCertificate;
use App\Models\Comment;
use App\Models\User;
use App\Notifications\Discord;
use Illuminate\Contracts\Queue\ShouldQueue;
use Notification;

class CertificateRequested implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(UserRequestedCertificate $event): void
    {
        $certificate = $event->certificate;
        $certifiable_type = $event->certificate->certifiable_type;

        // Send Discord notification
        if ($certifiable_type === "App\\Models\\ChallengeUser") {
            // dd($event->certifiable->challenge->name);
            new Discord(
                "💻 {$event->certifiable->challenge->name}\n👤 {$event->user->name}\n🔗 Submissão: <https://codante.io/mini-projetos/{$event->certifiable->slug}/submissoes/{$event->user->github_user}>\nPara aprovar, substitua o status para published: <https://api.codante.io/admin/certificate/{$certificate->id}/edit>\nID: $certificate->id",
                "pedidos-certificados"
            );
        }
    }
}