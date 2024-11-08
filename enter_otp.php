<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter OTP</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .otp-box {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 24px;
            margin: 0 5px;
        }
    </style>
</head>

<body>

    <div class="container mt-5">
        <h2 class="text-center">Enter OTP</h2>

        <form method="POST" action="verify_otp.php" class="d-flex flex-column align-items-center">
            <div class="d-flex justify-content-center mb-3">
                <input type="text" maxlength="1" class="otp-box form-control mx-1" name="otp[]" id="otp1"
                    oninput="moveNext(1)">
                <input type="text" maxlength="1" class="otp-box form-control mx-1" name="otp[]" id="otp2"
                    oninput="moveNext(2)" onkeydown="moveBack(2)">
                <input type="text" maxlength="1" class="otp-box form-control mx-1" name="otp[]" id="otp3"
                    oninput="moveNext(3)" onkeydown="moveBack(3)">
                <input type="text" maxlength="1" class="otp-box form-control mx-1" name="otp[]" id="otp4"
                    oninput="moveNext(4)" onkeydown="moveBack(4)">
                <input type="text" maxlength="1" class="otp-box form-control mx-1" name="otp[]" id="otp5"
                    oninput="moveNext(5)" onkeydown="moveBack(5)">
                <input type="text" maxlength="1" class="otp-box form-control mx-1" name="otp[]" id="otp6"
                    onkeydown="moveBack(6)">
            </div>

            <input type="hidden" name="email"
                value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>">

            <div class="text-center mt-3">
                <button type="submit" class="btn btn-primary">Verify OTP</button>
            </div>
        </form>


    </div>

    <script>
        function moveNext(currentBox) {
            if (document.getElementById('otp' + currentBox).value !== '') {
                const nextBox = document.getElementById('otp' + (currentBox + 1));
                if (nextBox) {
                    nextBox.focus();
                }
            }
        }

        function moveBack(currentBox) {
            const currentInput = document.getElementById('otp' + currentBox);
            if (event.key === "Backspace" && currentInput.value === '') {
                const previousBox = document.getElementById('otp' + (currentBox - 1));
                if (previousBox) {
                    previousBox.focus();
                }
            }
        }
    </script>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>