<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../css/login_style.css">
</head>

<body>

    <form action="login-action.php" method="POST">
        <div class="container">
            <div class="content">
                <h1>เข้าสู่ระบบ</h1>

                <?php
                // if (isset($_SESSION['status'])) {
                //     echo "<h5 class='alert success'>" . $_SESSION['status'] . "</h5>";
                //     unset($_SESSION['status']);
                // }
                ?>

                <div class="form-group">
                    <label for="email">อีเมล</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">รหัสผ่าน</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <p style="text-align: center; padding-bottom: 10px; padding-top: -30px; margin-bottom: 5px;"><a href="forgot-password.php">ลืมรหัสผ่าน?</a></p>

                <button type="submit" name="login-btn">เข้าสู่ระบบ</button>
            </div>
        </div>
    </form>

</body>

</html>