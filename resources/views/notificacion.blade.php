<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación Agrícola</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
        }
        .email-container {
            max-width: auto;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #2E7D32, #4CAF50, #66BB6A);
            color: white;
            padding: 40px 20px;
            text-align: center;
            position: relative;
        }
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="40" r="0.8" fill="rgba(255,255,255,0.08)"/><circle cx="40" cy="80" r="1.2" fill="rgba(255,255,255,0.12)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>') repeat;
            opacity: 0.3;
        }
        .main-logo {
            max-width: 250px;
            height: auto;
            margin-bottom: 20px;
            display: block;
            margin-left: auto;
            margin-right: auto;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
            position: relative;
            z-index: 1;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            position: relative;
            z-index: 1;
        }
        .header p {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        .content {
            padding: 40px 30px;
            background: linear-gradient(to bottom, #ffffff, #fafafa);
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 25px;
            color: #2E7D32;
            font-weight: 500;
        }
        .details-box {
            background: linear-gradient(135deg, #f8f9fa, #e8f5e8);
            border: 1px solid #e0e0e0;
            border-left: 5px solid #4CAF50;
            padding: 25px;
            margin: 25px 0;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .detail-item {
            display: flex;
            margin-bottom: 15px;
            align-items: center;
            padding: 8px 0;
        }
        .detail-item:last-child {
            margin-bottom: 0;
        }
        .detail-label {
            font-weight: 600;
            color: #2E7D32;
            min-width: 120px;
            margin-right: 15px;
            font-size: 16px;
        }
        .detail-value {
            color: #333;
            font-size: 16px;
            background: rgba(76, 175, 80, 0.1);
            padding: 5px 10px;
            border-radius: 5px;
        }
        .content p {
            font-size: 16px;
            line-height: 1.7;
            margin-bottom: 20px;
        }
        .sponsors-section {
            background: linear-gradient(135deg, #f5f5f5, #eeeeee);
            padding: 30px 20px;
            text-align: center;
            border-top: 3px solid #4CAF50;
        }
        .sponsors-title {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        .sponsors-grid {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 25px;
            margin-bottom: 20px;
        }
        .sponsor-logo {
            height: 50px;
            width: auto;
            max-width: 120px;
            filter: grayscale(0%) contrast(1.1);
            transition: all 0.3s ease;
            border-radius: 5px;
            background: white;
            padding: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .sponsor-logo:hover {
            transform: scale(1.05);
            filter: grayscale(0%) contrast(1.2);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        .footer-info {
            background-color: #2E7D32;
            color: white;
            padding: 25px 20px;
            text-align: center;
            font-size: 14px;
        }
        .footer-info p {
            margin: 8px 0;
            opacity: 0.9;
        }
        .footer-info .year {
            font-weight: 600;
            font-size: 16px;
        }
        .divider {
            height: 3px;
            background: linear-gradient(90deg, #4CAF50, #66BB6A, #81C784, #66BB6A, #4CAF50);
            margin: 0;
        }

        /* Responsive */
        @media (max-width: 600px) {
            .main-logo {
                max-width: 200px;
            }
            .email-container {
                margin: 10px;
            }
            .content {
                padding: 25px 20px;
            }
            .sponsors-grid {
                gap: 15px;
            }
            .sponsor-logo {
                height: 40px;
                max-width: 100px;
            }
            .detail-item {
                flex-direction: column;
                align-items: flex-start;
            }
            .detail-label {
                min-width: auto;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header con logo principal -->
        <div class="header">
            <img src="{{ $message->embed(public_path('imagenes/logos/image.png')) }}" alt="Logo Principal" class="main-logo">
            <h1>Sistema de Gestión Agrícola</h1>
            <p>Notificación de Ciclo de Vida</p>
        </div>

        <div class="divider"></div>

        <!-- Contenido principal -->
        <div class="content">
            <div class="greeting">
                Nos complace informarle que se ha registrado exitosamente un nuevo <strong>ciclo de vida</strong> en nuestro sistema de gestión agrícola con los siguientes detalles:
            </div>

            <div class="details-box">
                <div class="detail-item">
                    <span class="detail-label">🏞️ Parcela:</span>
                    <span class="detail-value">{{ $nombre_parcela }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">🌾 Cultivo:</span>
                    <span class="detail-value">{{ $nombre_cultivo }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">👤 Propietario:</span>
                    <span class="detail-value">{{ $name }}</span>
                </div>
            </div>

            <p>Este registro ha sido procesado y almacenado correctamente en nuestra base de datos. El sistema continuará monitoreando y registrando todos los eventos relacionados con este ciclo de vida.</p>

            <p>Si tiene alguna consulta o requiere información adicional, no dude en ponerse en contacto con nuestro equipo de soporte técnico.</p>
        </div>

        <!-- Sección de patrocinadores -->
        <div class="sponsors-section">
            <div class="sponsors-title">
                Proyecto financiado por
            </div>
            <div class="sponsors-grid">
                <img src="{{ $message->embed(public_path('imagenes/logos/logocicy.png')) }}" alt="CICY" class="sponsor-logo">
                <img src="{{ $message->embed(public_path('imagenes/logos/logoeuropa.png')) }}" alt="CICY" class="sponsor-logo">
                <img src="{{ $message->embed(public_path('imagenes/logos/logojunta.png')) }}" alt="CICY" class="sponsor-logo">
                <img src="{{ $message->embed(public_path('imagenes/logos/logorefex.png')) }}" alt="CICY" class="sponsor-logo">
                <img src="{{ $message->embed(public_path('imagenes/logos/TID4AGRO.png')) }}" alt="CICY" class="sponsor-logo">
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-info">
            <p class="year">© {{ date('Y') }} Sistema de Gestión Agrícola</p>
            <p>Proyecto TID4AGRO - Tecnologías Inteligentes para el Desarrollo Agrícola</p>
            <p style="font-size: 12px; margin-top: 15px; opacity: 0.8;">
                Este es un mensaje automático generado por el sistema. Por favor, no responda a este correo electrónico.
            </p>
        </div>
    </div>
</body>
</html>
