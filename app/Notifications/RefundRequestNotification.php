<?php

namespace App\Notifications;

use App\Models\Sale;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RefundRequestNotification extends Notification
{
    use Queueable;

    public $sale;
    public $requester;

    /**
     * Create a new notification instance.
     */
    public function __construct(Sale $sale, $requester)
    {
        $this->sale = $sale;
        $this->requester = $requester;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Store in database for top-bar view
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Refund Requested',
            'message' => 'Refund requested for Sale #' . str_pad($this->sale->id, 6, '0', STR_PAD_LEFT),
            'url' => route('admin.refunds.index'),
            'type' => 'info'
        ];
    }
}
