<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactSuggestionMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public array $payload)
    {
    }

    public function build(): self
    {
        $logoDataUri = $this->resolveTransparentLogoDataUri();

        return $this
            ->subject('Nuevo contacto web - LogistikPro')
            ->replyTo($this->payload['email'], $this->payload['name'])
            ->view('emails.contact-suggestion')
            ->with([
                'payload' => $this->payload,
                'submittedAt' => now()->format('Y-m-d H:i:s'),
                'logoDataUri' => $logoDataUri,
            ]);
    }

    private function resolveTransparentLogoDataUri(): ?string
    {
        $logoPath = dirname(base_path(), 3).'/apps/frontend/static/branding/logo-logistikpro-removebg-preview.png';

        if (!is_file($logoPath)) {
            return null;
        }

        $contents = file_get_contents($logoPath);

        if ($contents === false) {
            return null;
        }

        return 'data:image/png;base64,'.base64_encode($contents);
    }
}
