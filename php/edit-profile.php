<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/top_style.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <title>Join Me</title>
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
                <span class="text">แก้ไขข้อมูลส่วนตัว</span>
            </div>

            <?php
            if (isset($_SESSION['status'])) {
                echo "<h5 class='alert success'>" . $_SESSION['status'] . "</h5>";
                unset($_SESSION['status']);
            }
            ?>


            <div class="profile">
                <form action="edit-profile-action.php" method="POST" enctype="multipart/form-data">
                    <div class="profile-info">

                        <div class="profile-picture">
                            <img src="<?= htmlspecialchars($profileImage); ?>" alt="Profile Image" class="profile-img">
                        </div>

                        <div class="form-group profile-container">
                            <img id="profileImagePreview" src="#" alt="โปรไฟล์รูปภาพ">
                        </div>

                        <div class="profile-field profile-edit">
                            <strong for="profileImage">รูปโปรไฟล์:</strong>
                            <input type="file" name="profileImage" id="profileImage" accept="image/*" onchange="previewImage()">
                        </div>

                        <div class="profile-field" style="display: none;">
                            <strong>อีเมล:</strong>
                            <input type="text" name="email" value="<?= htmlspecialchars($email); ?>" required>
                        </div>

                        <div class="profile-field" style="display: none;">
                            <strong>รหัสนิสิต:</strong>
                            <input type="text" name="studentID" value="<?= htmlspecialchars($studentID); ?>" required>
                        </div>

                        <div class="profile-field" style="display: none;">
                            <strong>คำนำหน้า:</strong>
                            <input type="text" name="title" value="<?= htmlspecialchars($title); ?>" required>
                        </div>

                        <div class="profile-field">
                            <strong>ชื่อ:</strong>
                            <input type="text" name="firstName" value="<?= htmlspecialchars($firstName); ?>" required>
                        </div>

                        <div class="profile-field">
                            <strong>นามสกุล:</strong>
                            <input type="text" name="lastName" value="<?= htmlspecialchars($lastName); ?>" required>
                        </div>

                        <?php if (empty($userRoles)) {
                            $userRoles = 'ไม่มีบทบาท';
                        } else {
                            $role = strtolower(trim($userRoles));
                            switch ($role) {
                                case 'ผู้ใช้ทั่วไป':
                                    $userRoles = 'User';
                                    break;
                                case 'แอดมิน':
                                    $userRoles = 'Admin';
                                    break;
                                case 'ภาควิชาวิทยาการคอมพิวเตอร์':
                                    $userRoles = 'Departmentcs';
                                    break;
                                case 'ภาควิชาวิทยาการข้อมูล':
                                    $userRoles = 'Departmentads';
                                    break;
                                default:
                                    $userRoles = 'ไม่มีบทบาท';
                                    break;
                            }
                        } ?>

                        <div class="form-group" style="display: none;">
                            <strong for="userRoles">บทบาท</strong>
                            <select name="userRoles" id="userRoles">
                                <option value="Admin" <?= ($userRoles == 'Admin') ? 'selected' : '' ?>>Admin</option>
                                <option value="User" <?= ($userRoles == 'User') ? 'selected' : '' ?>>User</option>
                                <option value="Departmentcs" <?= ($userRoles == 'Departmentcs') ? 'selected' : '' ?>>DepartmentCS</option>
                                <option value="Departmentads" <?= ($userRoles == 'Departmentads') ? 'selected' : '' ?>>DepartmentADS</option>
                            </select>
                        </div>

                        <div class="form-group" style="display: none;">
                            <strong for="department">คณะ</strong>
                            <select name="department" id="department" value="<?= $department ?>">
                                <option value="IT">วิทยาการสารสนเทศ</option>
                            </select>
                        </div>

                        <?php if (empty($statusPost)) {
                            $statusPost = 'ไม่ระบุ';
                        } else {
                            $statusP = strtolower(trim($statusPost));
                            switch ($statusPost) {
                                case 'มีสิทธิ์':
                                    $statusPost = 'true';
                                    break;
                                case 'ไม่มีสิทธิ์':
                                    $statusPost = 'false';
                                    break;
                                default:
                                    $statusPost = 'ไม่ระบุ';
                                    break;
                            }
                        } ?>

                        <div class="profile-field" style="display: none;">
                            <strong>สิทธิในการโพสต์:</strong>
                            <input type=" text" name="statusPost" value="<?= htmlspecialchars($statusPost); ?>" required>
                        </div>

                        <div class="profile-field">
                            <strong for="major">สาขา:</strong>
                            <select name="major" id="major">
                                <option value="CS" <?= ($major == 'CS') ? 'selected' : '' ?>>วิทยาการคอมพิวเตอร์ (CS)</option>
                                <option value="CS INTER" <?= ($major == 'CS INTER') ? 'selected' : '' ?>>วิทยาการคอมพิวเตอร์ หลักสูตรนานาชาติ (CS-INTER)</option>
                                <option value="ADS" <?= ($major == 'ADS') ? 'selected' : '' ?>>วิทยาการข้อมูลประยุกต์ (ADS)</option>
                            </select>
                        </div>

                        <div class="profile-field">
                            <strong>หมายเลขโทรศัพท์:</strong>
                            <?php
                            $phoneNumber = preg_replace('/^\+66/', '0', $phoneNumber);
                            ?>
                            <input type="text" name="phoneNumber" value="<?= htmlspecialchars($phoneNumber); ?>" required>
                        </div>

                        <button type="submit" name="edit-profile" class="edit-profile-submit">อัพเดตข้อมูล</button>
                    </div>
                </form>
            </div>

    </section>

    <script src="../script/script_sidebar.js"></script>
    <script src="../script/script_image-profile-previwe.js"></script>
</body>

</html>