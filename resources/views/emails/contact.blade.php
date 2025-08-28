<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouveau message de contact</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <table style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <tr>
            <td style="background-color: #007BFF; color: white; padding: 20px; text-align: center;">
                <h2 style="margin: 0;">Nouveau message de contact</h2>
            </td>
        </tr>
        <tr>
            <td style="padding: 30px;">
                <p><strong>Nom :</strong> {{ $name }}</p>
                <p><strong>Email :</strong> {{ $email }}</p>
                <p><strong>Sujet :</strong> {{ $subject }}</p>
                <hr style="margin: 20px 0;">
                <p style="white-space: pre-line;"><strong>Message :</strong></p>
                <p style="background-color: #f9f9f9; padding: 15px; border-radius: 5px; line-height: 1.6;">
                    {{ $messageContent }}
                </p>
            </td>
        </tr>
        <tr>
            <td style="background-color: #f0f0f0; color: #666; text-align: center; padding: 10px; font-size: 12px;">
                © {{ date('Y') }} TonSite.com - Tous droits réservés
            </td>
        </tr>
    </table>
</body>
</html>
