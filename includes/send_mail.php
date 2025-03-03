<?php
require __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;


function SendMail($user_email, $name, $message_body, $subject)
{
    include("constants.php");
    $receiver_email = $user_email;

    $message = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4;">

    <div style="width: 100%; max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
        <div style="background-color: #007BFF; color: #ffffff; padding: 20px; text-align: center;">
            <h1 style="margin: 0;">' . $system_name . '</h1>
        </div>
        <div style="padding: 20px;">
            <p>Dear ' . htmlspecialchars($name) . ',</p>
            
            <p>' . nl2br(htmlspecialchars($message_body)) . '</p>

            <p>If you have any questions, feel free to reach out.</p>

            <p>Best Regards,<br>' . $system_name . '</p>
        </div>
    </div>

</body>
</html>';

    

    $mail = new PHPMailer();
    $mail->isHTML(true);
    $mail->isSMTP();
    $mail->Host = $smtp_server;
    $mail->SMTPAuth = true;
    $mail->Username = $smtp_mail;
    $mail->Password = $smtp_password;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Use PHPMailer::ENCRYPTION_STARTTLS if needed
    $mail->Port = $smtp_port;

    $mail->setFrom($smtp_mail, $system_name);
    $mail->addAddress($receiver_email);
    $mail->Subject = $subject;
    $mail->Body = $message;

    if (!$mail->send()) {
        return "Mailer Error: " . $mail->ErrorInfo;
    }
    return true;
}
function SendPassResetMail($user_email, $name, $message_body, $subject, $reset_url)
{
    include("constants.php");
    $receiver_email = $user_email;

    $message = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .button {
            background-color:rgb(255, 0, 0);
            color: white !important;
            padding: 15px 25px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 20px;
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color:rgb(0, 179, 128);
        }
    </style>
</head>
<body style="font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4;">

    <div style="width: 100%; max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
        <div style="background-color: #007BFF; color: #ffffff; padding: 20px; text-align: center;">
            <h1 style="margin: 0;">' . $system_name . '</h1>
        </div>
        <div style="padding: 20px;">
            <p>Dear ' . htmlspecialchars($name) . ',</p>
            
            <p>' . nl2br(htmlspecialchars($message_body)) . '</p>
            <a href="' . $reset_url . '" class="button">Reset Password</a>

            <p>If you have any questions, feel free to reach out.</p>

            <p>Best Regards,<br>' . $system_name . '</p>
        </div>
    </div>

</body>
</html>';

    

    $mail = new PHPMailer();
    $mail->isHTML(true);
    $mail->isSMTP();
    $mail->Host = $smtp_server;
    $mail->SMTPAuth = true;
    $mail->Username = $smtp_mail;
    $mail->Password = $smtp_password;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Use PHPMailer::ENCRYPTION_STARTTLS if needed
    $mail->Port = $smtp_port;

    $mail->setFrom($smtp_mail, $system_name);
    $mail->addAddress($receiver_email);
    $mail->Subject = $subject;
    $mail->Body = $message;

    if (!$mail->send()) {
        return "Mailer Error: " . $mail->ErrorInfo;
    }
    return true;
}
?>