<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AcceptRechargeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $driverData;
    public $opMangerData;
    public $requestRecharge;

    /**
     * Create a new message instance.
     */
    public function __construct($driverData,$opMangerData, $requestRecharge)
    {
        $this->driverData = $driverData;
        $this->opMangerData = $opMangerData;
        $this->requestRecharge = $requestRecharge;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Accept Recharge Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.recharge.accept-recharge',
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
