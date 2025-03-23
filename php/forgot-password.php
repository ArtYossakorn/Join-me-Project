<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลืมรหัสผ่าน</title>
    <link rel="stylesheet" href="../css/login_style.css">
    <script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-auth.js"></script>
</head>

<body>

    <div class="container">
        <div class="content">
            <h1>ลืมรหัสผ่าน</h1>

            <form id="reset-password-form">
                <div class="form-group">
                    <label for="email">อีเมล</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <button type="submit">ส่งลิงก์รีเซ็ต</button>
            </form>
        </div>
    </div>

    <!-- ใช้ type="module" เพื่อใช้การ import -->
    <script type="module">
        // Firebase configuration
        const firebaseConfig = {
            apiKey: "AIzaSyCMyqsSw0z5pGJj0ck2eKBENjgCZ6zgZwY",
            authDomain: "join-me-project.firebaseapp.com",
            projectId: "join-me-project",
            storageBucket: "join-me-project.firebasestorage.app",
            messagingSenderId: "742799431068",
            appId: "1:742799431068:web:6706644dab8c812e0ba459",
        };

        // Initialize Firebase
        import {
            initializeApp
        } from "https://www.gstatic.com/firebasejs/9.6.1/firebase-app.js";
        import {
            getAuth,
            sendPasswordResetEmail
        } from "https://www.gstatic.com/firebasejs/9.6.1/firebase-auth.js";

        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);

        // Handle form submission
        document.getElementById('reset-password-form').addEventListener('submit', function(event) {
            event.preventDefault();

            const email = document.getElementById('email').value;

            // Send password reset email
            sendPasswordResetEmail(auth, email)
                .then(() => {
                    alert('ลิงก์รีเซ็ตรหัสผ่านได้ถูกส่งไปยังอีเมลของคุณ');
                    window.location.href = 'login.php'; // เปลี่ยนเส้นทางไปยังหน้า login.php
                })
                .catch((error) => {
                    const errorCode = error.code;
                    const errorMessage = error.message;
                    if (errorCode === 'auth/user-not-found') {
                        alert('ไม่พบผู้ใช้ที่มีอีเมลนี้');
                    } else {
                        alert('เกิดข้อผิดพลาด: ' + errorMessage);
                    }
                });
        });
    </script>

</body>

</html>