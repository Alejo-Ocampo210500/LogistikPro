<?php

namespace App\Http\Modules\Contact\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Modules\Contact\Request\ContactSuggestionRequest;
use App\Mail\ContactSuggestionMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ContactController extends Controller
{
    public function store(ContactSuggestionRequest $request): JsonResponse
    {
        try {
            $payload = $request->validated();
            $payload['source'] = 'Sitio web';
            $payload['origin'] = 'Landing de contacto';
            $payload['context'] = 'Software LogistikPro';

            Mail::to(config('mail.contact_recipient'))->send(new ContactSuggestionMail($payload));

            return response()->json([
                'success' => true,
                'message' => 'Solicitud enviada correctamente. Te responderemos pronto.',
            ], 200);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'No fue posible enviar la solicitud. Intenta nuevamente.',
            ], 500);
        }
    }
}
