<?php

namespace App\Mail;

use App\Models\Patient;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PatientPortalAccess extends Mailable
{
    use Queueable, SerializesModels;

    public $patient;
    public $password;
    public $settings;
    public $loginUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Patient $patient, string $password)
    {
        $this->patient = $patient;
        $this->password = $password;
        $this->settings = Setting::firstOrCreate(['id' => 1], ['business_name' => 'UVITECH Healthcare']);
        $this->loginUrl = url('/login');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Patient Portal Access Details - ' . $this->settings->business_name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.patients.portal-access',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
