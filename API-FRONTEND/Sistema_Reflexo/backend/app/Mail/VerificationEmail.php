<?php


namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;

class VerificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $code;
    public string $subjectCustom;
    public string $viewName;


    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $code, string $subject = 'Verificación de cuenta', string $view = 'emails.verification')
    {
        $this->user = $user;
        $this->code = $code;
        $this->subjectCustom = $subject;
        $this->viewName = $view;
    }   


    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectCustom,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: $this->viewName,
            with: [
                'user' => $this->user,
                'code' => $this->code,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}