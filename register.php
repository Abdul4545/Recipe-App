<?php
include 'db.php';
include 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        die("Passwords do not match.");
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $otp = rand(100000, 999999);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, otp) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $hashed_password, $otp);

    if ($stmt->execute()) {
        // Send OTP via email
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'mycloud.storage.app1@gmail.com';
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('mycloud.storage.app1@gmail.com', 'Recipe App');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Email Verification';
            $mail->Body = "
<!DOCTYPE html>
<html lang='en'>
<head>
  <meta charset='UTF-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
  <style>
    body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
    .container { width: 100%; max-width: 600px; margin: 20px auto; background-color: #ffffff; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 20px; }
    .header { background-color: #007bff; padding: 15px; border-top-left-radius: 8px; border-top-right-radius: 8px; color: #ffffff; text-align: center; font-size: 24px; }
    .content { font-size: 16px; color: #333333; line-height: 1.6; margin-top: 20px; text-align: center; }
    .otp { display: inline-block; padding: 10px 20px; background-color: #007bff; color: #ffffff; font-weight: bold; font-size: 20px; border-radius: 5px; margin: 20px 0; }
    .footer { font-size: 14px; color: #777777; text-align: center; margin-top: 20px; }
  </style>
</head>
<body>
  <div class='container'>
    <div class='header'>Email Verification</div>
    <div class='content'>
      <p>Dear User,</p>
      <p>Your OTP for email verification is:</p>
      <p class='otp'>$otp</p>
      <p>Please enter this OTP in the verification page to confirm your email address.</p>
    </div>
    <div class='footer'>
      <p>Thank you for using our service!</p>
      <p>If you didn't request this, please ignore this email.</p>
    </div>
  </div>
</body>
</html>";

            $mail->send();
            header("Location: enter_otp.php?email=" . urlencode($email));
            exit();
        } catch (Exception $e) {
            echo "Error: Could not send OTP. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
