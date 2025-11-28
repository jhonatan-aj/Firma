<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Código de Verificación</title>
    <!--[if mso]>
    <style type="text/css">
        table {border-collapse: collapse;}
    </style>
    <![endif]-->
</head>

<body
    style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f3f2ef; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale;">

    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
        style="background-color: #f3f2ef;">
        <tr>
            <td align="center" style="padding: 20px 0;">

                <!-- Contenedor del email -->
                <!--[if mso]>
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600">
                <tr>
                <td>
                <![endif]-->

                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
                    style="max-width: 600px; background-color: #ffffff;">

                    <!-- Header con color sólido -->
                    <tr>
                        <td align="center" bgcolor="#0a66c2" style="padding: 40px 20px;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <!-- Logo centrado -->
                                        <img src="{{ $message->embed(public_path('logo.jpg')) }}" alt="Logo"
                                            style="width: 80px; height: auto; display: block; margin: 0 auto;">
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding-top: 20px;">
                                        <h1
                                            style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 600; line-height: 1.3; font-family: Arial, Helvetica, sans-serif;">
                                            Verifica tu identidad
                                        </h1>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Contenido principal -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">

                                <!-- Saludo -->
                                <tr>
                                    <td style="padding-bottom: 20px;">
                                        <p
                                            style="margin: 0; color: #000000; font-size: 16px; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;">
                                            Hola <strong>{{ $name ?? 'Usuario' }}</strong>,
                                        </p>
                                    </td>
                                </tr>

                                <!-- Descripción -->
                                <tr>
                                    <td style="padding-bottom: 30px;">
                                        <p
                                            style="margin: 0; color: #666666; font-size: 15px; line-height: 1.6; font-family: Arial, Helvetica, sans-serif;">
                                            Recibimos una solicitud para: <strong
                                                style="color: #000000;">{{ $purpose }}</strong>
                                        </p>
                                    </td>
                                </tr>

                                <!-- Código de verificación -->
                                <tr>
                                    <td style="padding-bottom: 30px;">
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0"
                                            width="100%">
                                            <tr>
                                                <td align="center" bgcolor="#f3f2ef"
                                                    style="padding: 30px 20px; border-radius: 8px;">
                                                    <table role="presentation" border="0" cellpadding="0"
                                                        cellspacing="0">
                                                        <tr>
                                                            <td align="center" style="padding-bottom: 10px;">
                                                                <p
                                                                    style="margin: 0; color: #666666; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; font-weight: bold; font-family: Arial, Helvetica, sans-serif;">
                                                                    TU CÓDIGO DE VERIFICACIÓN
                                                                </p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center">
                                                                <p
                                                                    style="margin: 0; font-size: 36px; font-weight: bold; color: #0a66c2; letter-spacing: 8px; font-family: 'Courier New', Courier, monospace; line-height: 1.2;">
                                                                    {{ $code }}
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <!-- Alerta de expiración -->
                                <tr>
                                    <td style="padding-bottom: 20px;">
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0"
                                            width="100%">
                                            <tr>
                                                <td bgcolor="#fff4e6"
                                                    style="padding: 16px; border-left: 4px solid #f59e0b;">
                                                    <p
                                                        style="margin: 0; color: #92400e; font-size: 14px; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;">
                                                        <strong>⏱️ Importante:</strong> Este código expirará el
                                                        <strong>{{ $expiresAt }}</strong> por motivos de seguridad.
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <!-- Mensaje de seguridad -->
                                <tr>
                                    <td>
                                        <p
                                            style="margin: 0; color: #666666; font-size: 14px; line-height: 1.6; font-family: Arial, Helvetica, sans-serif;">
                                            Si no solicitaste este código, puedes ignorar este correo de forma segura.
                                            Tu cuenta permanece protegida.
                                        </p>
                                    </td>
                                </tr>

                            </table>
                        </td>
                    </tr>

                </table>

                <!--[if mso]>
                </td>
                </tr>
                </table>
                <![endif]-->

                <!-- Aviso adicional -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
                    style="max-width: 600px; margin-top: 20px;">
                    <tr>
                        <td align="center" style="padding: 0 30px;">
                            <p
                                style="margin: 0; color: #999999; font-size: 12px; line-height: 1.5; font-family: Arial, Helvetica, sans-serif; text-align: center;">
                                Este es un correo automático, por favor no respondas a este mensaje.
                            </p>
                        </td>
                    </tr>
                </table>

                <!-- Footer con logo -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
                    style="max-width: 600px; margin-top: 30px; background-color: #ffffff; border-top: 1px solid #e5e5e5;">
                    <tr>
                        <td style="padding: 30px;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="left" style="vertical-align: middle;">
                                        <img src="{{ $message->embed(public_path('logo.jpg')) }}" alt="Logo"
                                            style="width: 60px; height: auto; display: block;">
                                    </td>
                                    <td align="right" style="vertical-align: middle;">
                                        <p
                                            style="margin: 0; color: #999999; font-size: 11px; font-family: Arial, Helvetica, sans-serif;">
                                            © {{ date('Y') }} Todos los derechos reservados
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

</body>

</html>
