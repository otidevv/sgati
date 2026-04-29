<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de verificación</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f1f5f9; margin: 0; padding: 24px; }
        .container { max-width: 480px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%); padding: 32px 40px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 20px; font-weight: 700; margin: 0; }
        .header p { color: #bfdbfe; font-size: 13px; margin: 6px 0 0; }
        .body { padding: 36px 40px; }
        .greeting { font-size: 15px; color: #374151; margin-bottom: 16px; }
        .message { font-size: 14px; color: #6b7280; line-height: 1.6; margin-bottom: 28px; }
        .code-box { text-align: center; background: #eff6ff; border: 2px dashed #93c5fd; border-radius: 12px; padding: 28px; margin: 24px 0; }
        .code { font-size: 42px; font-weight: 800; letter-spacing: 10px; color: #1d4ed8; font-family: 'Courier New', monospace; }
        .code-label { font-size: 12px; color: #6b7280; margin-top: 8px; }
        .warning { display: flex; align-items: flex-start; gap: 10px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 14px 16px; margin-top: 20px; }
        .warning-icon { font-size: 16px; flex-shrink: 0; margin-top: 1px; }
        .warning p { font-size: 12px; color: #92400e; margin: 0; line-height: 1.5; }
        .footer { background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 40px; text-align: center; }
        .footer p { font-size: 11px; color: #94a3b8; margin: 0; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 {{ config('app.name') }}</h1>
            <p>Sistema de Gestión de Tecnologías de Información</p>
        </div>
        <div class="body">
            <p class="greeting">Hola, <strong>{{ $user->nombre_completo }}</strong></p>
            <p class="message">
                Alguien está intentando iniciar sesión en tu cuenta. Usa el siguiente código de verificación para completar el acceso:
            </p>

            <div class="code-box">
                <div class="code">{{ $code }}</div>
                <div class="code-label">Código válido por <strong>10 minutos</strong></div>
            </div>

            <div class="warning">
                <span class="warning-icon">⚠️</span>
                <p>
                    Si no fuiste tú quien intentó iniciar sesión, ignora este correo y considera cambiar tu contraseña inmediatamente. Nunca compartas este código con nadie.
                </p>
            </div>
        </div>
        <div class="footer">
            <p>
                Este correo fue enviado automáticamente por {{ config('app.name') }} — OTI UNAMAD.<br>
                Por favor no respondas a este mensaje.
            </p>
        </div>
    </div>
</body>
</html>
