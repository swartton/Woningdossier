<?php

namespace App\Notifications;

use App\Models\Cooperation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * The cooperation the user is associated with.
     *
     * @var Cooperation|null
     */
    public $cooperation;

    /**
     * Create a notification instance.
     *
     * @param string $token
     *
     * @return void
     */
    public function __construct($token, $cooperation)
    {
        $this->token = $token;
        $this->cooperation = $cooperation;
    }

    /**
     * Get the notification's channels.
     *
     * @param mixed $notifiable
     *
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->line(__('mail.reset_password.why'))
            ->action(__('mail.reset_password.action'), route('cooperation.password.reset', ['cooperation' => $this->cooperation, 'token' => $this->token]))
            //->action('Reset Password', url(config('app.url').route('password.reset', $this->token, false)))
            ->line(__('mail.reset_password.not_requested'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
        ];
    }
}
