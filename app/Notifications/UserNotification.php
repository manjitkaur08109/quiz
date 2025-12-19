<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Notifications\Messages\BroadcastMessage;

class UserNotification extends Notification implements ShouldBroadcastNow
{
    use Queueable;
     public $title;
     public $description;
     public $type;
     public $user_id;



    /**
     * Create a new notification instance.
     */
    public function __construct( $title, $description, $type, $user_id)
    {
        $this->title = $title;
        $this->description = $description;
        $this->type = $type;
        $this->user_id = $user_id;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database','broadcast'];
    }
    public function toDatabase($notifiable){
        return [
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
        ]);
    }

    /**
     * Broadcast channel
     */
    public function broadcastOn()
    {
        // Private channel for the user
        return new \Illuminate\Broadcasting\PrivateChannel('App.Models.User.' . $this->user_id);
    }

    /**
     * Get the broadcast channel name for the notification.
     */
    public function broadcastAs()
    {
        return 'notification.created';
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [

        ];
    }
}
