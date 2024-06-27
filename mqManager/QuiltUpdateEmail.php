<?php

namespace App\Mail;

use App\Models\Quilt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuiltUpdateEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Quilt $quilt,
        public string $copy,
        public string $status,
        public string $base64Photo,
        public string $customerName,
        // public string $imageUrl,
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('app.quilt_update_email'), config('app.quilt_update_name')),
            replyTo: [
                new Address(config('app.quilt_update_reply_to'), config('app.quilt_update_name')),
            ],
            subject: 'Your Quilt Status is: ' . $this->status,
        );
    }

    /**
     * Get the message content definition.
     */

    public function content(): Content
    {


        return new Content(
            view: 'email.quiltUpdate',
            with: [
                'copy' => $this->copy,
                'status' => $this->status,
                'photo' => $this->base64Photo,
                'customerName' => $this->customerName,
            ],
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
