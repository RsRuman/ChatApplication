<?php

namespace App\Notifications;

use App\Events\NewMessageNotificationEvent;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;


    public User $user;
    public string $message;

    public function __construct(User $user, $message)
    {
        $this->user = $user;
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['broadcast'];
    }


    public function toBroadcast($notifiable)
    {
      return new BroadcastMessage([
          'message' => 'You have a new message',
          'from' => $this->user
      ]);
    }


    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
