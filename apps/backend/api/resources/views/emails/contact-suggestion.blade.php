<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo contacto web</title>
</head>
<body style="margin:0;padding:0;background:#eef3fb;font-family:Arial,sans-serif;color:#10233f;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#eef3fb;padding:26px 0;">
    <tr>
        <td align="center">
            <table role="presentation" width="660" cellspacing="0" cellpadding="0" style="max-width:660px;background:#ffffff;border-radius:18px;border:1px solid #d4deef;overflow:hidden;box-shadow:0 12px 28px rgba(10,28,64,0.12);">
                <tr>
                    <td style="background:linear-gradient(128deg,#0b1f4b 0%,#153a74 70%,#0b1f4b 100%);padding:20px 24px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td valign="middle">
                                    <h1 style="margin:0;font-size:22px;color:#f4b640;letter-spacing:0.02em;">Nuevo correo recibido</h1>
                                    <p style="margin:6px 0 0;color:#dbe6ff;font-size:13px;">LogistikPro - Notificacion comercial</p>
                                </td>
                                <td align="right" valign="middle" style="width:180px;">
                                    @if (!empty($logoDataUri))
                                        <img src="{{ $logoDataUri }}" alt="Logo LogistikPro" width="156" style="display:block;max-width:156px;height:auto;">
                                    @else
                                        <div style="display:inline-block;background:#ffffff;border-radius:10px;padding:10px 14px;border:1px solid rgba(255,255,255,0.35);font-size:12px;font-weight:700;color:#153a74;">
                                            LOGISTIKPRO
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="padding:18px 24px 8px;">
                        <p style="margin:0;font-size:14px;line-height:1.6;color:#1b355f;">
                            Se recibio una nueva solicitud desde el formulario de contacto.
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:0 24px 18px;">
                        <table role="presentation" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:8px 0;">
                            <tr>
                                <td style="padding:7px 12px;background:#fff6e3;border:1px solid #f7d792;border-radius:999px;font-size:12px;font-weight:700;color:#7d5600;">
                                    Origen: {{ $payload['source'] ?? 'Sitio web' }}
                                </td>
                                <td style="padding:7px 12px;background:#edf4ff;border:1px solid #bdd6ff;border-radius:999px;font-size:12px;font-weight:700;color:#214980;">
                                    Contexto: {{ $payload['context'] ?? 'Software LogistikPro' }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="padding:0 24px 24px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;border:1px solid #e2e9f6;border-radius:12px;overflow:hidden;">
                            <tr>
                                <td style="width:170px;padding:11px;border-bottom:1px solid #e2e9f6;background:#f6f9ff;font-weight:700;font-size:13px;">Fecha y hora</td>
                                <td style="padding:11px;border-bottom:1px solid #e2e9f6;font-size:13px;">{{ $submittedAt }}</td>
                            </tr>
                            <tr>
                                <td style="padding:11px;border-bottom:1px solid #e2e9f6;background:#f6f9ff;font-weight:700;font-size:13px;">Origen del envio</td>
                                <td style="padding:11px;border-bottom:1px solid #e2e9f6;font-size:13px;">{{ $payload['origin'] ?? 'Landing de contacto' }}</td>
                            </tr>
                            <tr>
                                <td style="padding:11px;border-bottom:1px solid #e2e9f6;background:#f6f9ff;font-weight:700;font-size:13px;">Nombre</td>
                                <td style="padding:11px;border-bottom:1px solid #e2e9f6;font-size:13px;">{{ $payload['name'] }}</td>
                            </tr>
                            <tr>
                                <td style="padding:11px;border-bottom:1px solid #e2e9f6;background:#f6f9ff;font-weight:700;font-size:13px;">Empresa</td>
                                <td style="padding:11px;border-bottom:1px solid #e2e9f6;font-size:13px;">{{ $payload['company'] ?: 'No registra' }}</td>
                            </tr>
                            <tr>
                                <td style="padding:11px;border-bottom:1px solid #e2e9f6;background:#f6f9ff;font-weight:700;font-size:13px;">Correo</td>
                                <td style="padding:11px;border-bottom:1px solid #e2e9f6;font-size:13px;">
                                    <a href="mailto:{{ $payload['email'] }}" style="color:#0d4ea2;text-decoration:none;font-weight:700;">{{ $payload['email'] }}</a>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:11px;border-bottom:1px solid #e2e9f6;background:#f6f9ff;font-weight:700;font-size:13px;">Telefono</td>
                                <td style="padding:11px;border-bottom:1px solid #e2e9f6;font-size:13px;">{{ $payload['phone'] }}</td>
                            </tr>
                            <tr>
                                <td style="padding:11px;border-bottom:1px solid #e2e9f6;background:#f6f9ff;font-weight:700;font-size:13px;">Tema</td>
                                <td style="padding:11px;border-bottom:1px solid #e2e9f6;font-size:13px;">{{ $payload['topic'] }}</td>
                            </tr>
                            <tr>
                                <td style="padding:11px;background:#f6f9ff;font-weight:700;font-size:13px;vertical-align:top;">Mensaje</td>
                                <td style="padding:11px;font-size:13px;line-height:1.6;white-space:pre-wrap;">{{ $payload['message'] }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="padding:14px 24px;background:#f8fbff;border-top:1px solid #e2e9f6;font-size:12px;color:#4f6284;line-height:1.6;">
                        Este mensaje fue generado por el modulo de contacto del sitio web de LogistikPro.<br>
                        Destino configurado: contactologistikpro@gmail.com
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
