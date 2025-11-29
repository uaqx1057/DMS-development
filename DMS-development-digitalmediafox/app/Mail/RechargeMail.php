<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RechargeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $driverData;
    public $rechargeBy;
    public $recharge;
    public $requestRecharge;


    /**
     * Create a new message instance.
     */
    public function __construct($driverData, $rechargeBy, $recharge, $requestRecharge)
    {
        $this->driverData = $driverData;
        $this->rechargeBy = $rechargeBy;
        $this->recharge = $recharge;
        $this->requestRecharge = $requestRecharge;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Recharge Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.recharge.recharge',
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
