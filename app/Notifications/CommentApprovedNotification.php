<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentApprovedNotification extends Notification implements ShouldQueue
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
        $guideUrl = '/';
        if ($this->comment->commentable) {
            $guideTitle = $this->comment->commentable->title;
            $guideUrl = route('guides.show', $this->comment->commentable->slug);
        }

        return (new MailMessage)
            ->subject('Ihr Kommentar wurde genehmigt')
            ->greeting("Hallo {$this->comment->user->name}!")
            ->line("Ihr Kommentar zur Anleitung \"{$guideTitle}\" wurde genehmigt und ist jetzt sichtbar.")
            ->line("Kommentar: {$this->comment->body}")
            ->action('Anleitung ansehen', $guideUrl)
            ->line('Vielen Dank für Ihren Beitrag!');
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
            'body' => $this->comment->body,
        ];
    }
}
