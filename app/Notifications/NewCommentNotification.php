<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Comment $comment)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $guideTitle = '';
        if ($this->comment->commentable) {
            $guideTitle = $this->comment->commentable->title;
            $guideUrl = route('guides.show', $this->comment->commentable->slug);
        }

        return (new MailMessage)
            ->subject('Neuer Kommentar wartet auf Genehmigung')
            ->greeting('Hallo!')
            ->line("Ein neuer Kommentar von {$this->comment->user->name} wartet auf Ihre Genehmigung.")
            ->line("Anleitung: {$guideTitle}")
            ->line("Kommentar: {$this->comment->body}")
            ->action('Kommentare moderieren', route('admin.comments.index'))
            ->line('Vielen Dank für Ihre Arbeit!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'comment_id' => $this->comment->id,
            'user_name' => $this->comment->user->name,
            'body' => $this->comment->body,
        ];
    }
}
