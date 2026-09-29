<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCreated extends Notification
{
    use Queueable;

    public function __construct(
        public Booking $booking
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Booking Baru - ' . $this->booking->booking_code)
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Ada booking baru dari ' . $this->booking->customer->name)
            ->line('Tanggal: ' . $this->booking->booking_date->format('d M Y'))
            ->line('Jam: ' . $this->booking->booking_time)
            ->line('Lokasi: ' . $this->booking->address)
            ->action('Lihat Detail', url('/barber/bookings/' . $this->booking->id))
            ->line('Segera konfirmasi booking ini!');
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Booking Baru!',
            'message' => "Booking baru dari {$this->booking->customer->name}",
            'booking_id' => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
            'type' => 'new_booking',
        ];
    }
}