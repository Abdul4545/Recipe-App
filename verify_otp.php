<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $otpInput = implode('', $_POST['otp']); 
    $stmt = $conn->prepare("SELECT otp FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($storedOtp);
    $stmt->fetch();
    $stmt->close();

    if (password_verify($otpInput, $storedOtp)) {
        $updateStmt = $conn->prepare("UPDATE users SET is_verified = 1 WHERE email = ?");
        $updateStmt->bind_param("s", $email);
        $updateStmt->execute();
        $updateStmt->close();

        echo '<div class="alert alert-success text-center" role="alert">
                OTP verified successfully! You will be redirected shortly...
              </div>';
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'welcome.php';
                }, 3000); // 3000ms = 3 seconds
              </script>";
    } else {
        // Redirect error message
        echo '<div class="alert alert-warning text-center" role="alert">
                Invalid OTP. Please enter the correct OTP.
              </div>';
    }
}

$conn->close();