<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/top_style.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">

    <title>Profile Page</title>
</head>

<body>
    <?php include '../includes/menu.php'; ?>

    <section class="dashboard">
        <div class="top">
            <i class="uil uil-bars sidebar-toggle"></i>
            <div class="search-box">
                <i class="uil uil-search"></i>
                <input type="text" placeholder="Search here...">
            </div>
            <a href="profile.php"><img src="<?= htmlspecialchars($profileImage); ?>" alt=""></a>
        </div>

        <div class="dash-content">
            <div class="title">
                <i class="uil uil-users-alt"></i>
                <span class="text">บัญชีผู้ใช้</span>
            </div>
        </div>

        <?php
        if (isset($_SESSION['status'])) {
            echo "<h5 class='alert success'>" . $_SESSION['status'] . "</h5>";
            unset($_SESSION['status']);
        }
        ?>

        <div class="profile">
            <h2>ข้อมูลผู้ใช้</h2>
            <div class="profile-info">

                <!-- ตรวจสอบและแสดงรูปภาพโปรไฟล์ -->
                <div class="profile-picture">
                    <img src="<?= htmlspecialchars($profileImage); ?>" alt="Profile Image" class="profile-img">
                </div>

                <div class="profile-field">
                    <strong>รหัสนักศึกษา:</strong>
                    <span><?= htmlspecialchars($studentID); ?></span>
                </div>

                <div class="profile-field">
                    <strong>ชื่อผู้ใช้:</strong>
                    <span><?= htmlspecialchars($displayName); ?></span>
                </div>

                <div class="profile-field">
                    <strong>อีเมล:</strong>
                    <span><?= htmlspecialchars($email); ?></span>
                </div>

                <div class="profile-field">
                    <strong>สาขา:</strong>
                    <span><?= htmlspecialchars($major); ?></span>
                </div>

                <div class="profile-field">
                    <strong>หมายเลขโทรศัพท์:</strong>
                    <span>
                        <?php
                        $phoneNumber = preg_replace('/^\+66/', '0', $phoneNumber);
                        echo htmlspecialchars($phoneNumber);
                        ?>
                    </span>
                </div>

                <div class="profile-field">
                    <strong>บทบาท:</strong>
                    <span><?= htmlspecialchars($userRoles); ?></span>
                </div>

                <div class="profile-field">
                    <strong>สิทธิในการโพสต์:</strong>
                    <span><?= htmlspecialchars($statusPost); ?></span>
                </div>
            </div>

            <div class="profile-actions">
                <a href="edit-profile.php?userID=<?= htmlspecialchars($uid); ?>">
                    <button class="edit-profile-btn">แก้ไขข้อมูลส่วนตัว</button>
                </a>
                <a href="change-password.php?userID=<?= htmlspecialchars($uid); ?>">
                    <button class="change-password-btn">เปลี่ยนรหัสผ่าน</button>
                </a>
            </div>

        </div>
    </section>

    <script src="../script/script_sidebar.js"></script>
</body>

</html>