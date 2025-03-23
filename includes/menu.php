<?php

session_start();
include('../config/dbconfig.php');

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['verified_user_id'])) {
    echo "No user logged in.";
    exit;
}

$uid = $_SESSION['verified_user_id'];

try {
    // ดึงข้อมูลผู้ใช้จาก Firebase Authentication
    $user = $auth->getUser($uid);

    // ตรวจสอบ displayName และให้ค่าเริ่มต้นหากไม่มีค่า
    $displayName = isset($user->displayName) && !empty($user->displayName) ? $user->displayName : 'ไม่ระบุชื่อ';
    $email = $user->email;
    $phoneNumber = isset($user->phoneNumber) ? $user->phoneNumber : 'ไม่ระบุหมายเลขโทรศัพท์';

    // ดึงข้อมูล customClaims
    $customClaims = isset($user->customClaims) ? $user->customClaims : [];
    $title = isset($customClaims['title']) ? $customClaims['title'] : 'ไม่ระบุบทบาท';
    $userRoles = isset($customClaims['userRoles']) ? $customClaims['userRoles'] : 'ไม่ระบุบทบาท';
    $firstName = isset($customClaims['firstName']) ? $customClaims['firstName'] : 'ไม่ระบุบ';
    $lastName = isset($customClaims['lastName']) ? $customClaims['lastName'] : 'ไม่ระบุบ';
    $studentID = isset($customClaims['studentID']) ? $customClaims['studentID'] : 'ไม่ระบุรหัสนักศึกษา';
    $statusPost = isset($customClaims['statusPost']) ? $customClaims['statusPost'] : 'ไม่ระบุ';
    $department = isset($customClaims['department']) ? $customClaims['department'] : 'ไม่ระบุ';
    $major = isset($customClaims['major']) ? $customClaims['major'] : 'ไม่ระบุ';
    $profileImage = isset($customClaims['profileImage']) ? $customClaims['profileImage'] : 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_1280.png';

    if (empty($userRoles)) {
        $userRoles = 'ไม่มีบทบาท';
    } else {
        $role = strtolower(trim($userRoles));
        switch ($role) {
            case 'user':
                $userRoles = 'ผู้ใช้ทั่วไป';
                break;
            case 'admin':
                $userRoles = 'แอดมิน';
                break;
            case 'departmentcs':
                $userRoles = 'ภาควิชาวิทยาการคอมพิวเตอร์';
                break;
            case 'departmentads':
                $userRoles = 'ภาควิชาวิทยาการข้อมูล';
                break;
            default:
                $userRoles = 'ไม่มีบทบาท';
                break;
        }
    }

    if (empty($statusPost)) {
        $statusPost = 'ไม่ระบุ';
    } else {
        $statusP = strtolower(trim($statusPost));
        switch ($statusPost) {
            case 'true':
                $statusPost = 'มีสิทธิ์';
                break;
            case 'false':
                $statusPost = 'ไม่มีสิทธิ์';
                break;
            default:
                $statusPost = 'ไม่ระบุ';
                break;
        }
    }
} catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
    echo "User not found: " . $e->getMessage();
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/menu_style.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <title>Menu</title>
</head>

<body>
    <nav>
        <div class="logo-name">
            <div class="logo-image">
                <img src="../img/logo.png" alt="">
            </div>
            <span class="logo-name">JoinMe</span>
        </div>

        <div class="menu-items">
            <ul class="nav-links">
                <li><a href="home-page.php">
                        <i class="uil uil-estate"></i>
                        <span class="link-name">หน้าหลัก</span>
                    </a></li>

                <?php if ($statusPost === 'มีสิทธิ์') {
                ?>
                    <li><a href="add-post.php">
                            <i class="uil uil-focus-add"></i>
                            <span class="link-name">เพิ่มโพสต์</span>
                        </a></li>
                <?php }
                ?>

                <?php
                if (isset($userRoles) && strtolower(trim($userRoles)) === 'แอดมิน') {
                ?>
                    <li><a href="../php/user-manage.php">
                            <i class="uil uil-users-alt"></i>
                            <span class="link-name">จัดการข้อมูลสมาชิก</span>
                        </a></li>
                <?php
                } ?>

                <?php
                if (isset($userRoles) && strtolower(trim($userRoles)) === 'ภาควิชาวิทยาการคอมพิวเตอร์') {
                ?>
                    <li><a href="../php/user-manage-cs.php">
                            <i class="uil uil-users-alt"></i>
                            <span class="link-name">จัดการข้อมูลนิสิต</span>
                        </a></li>
                <?php
                } ?>

                <?php
                if (isset($userRoles) && strtolower(trim($userRoles)) === 'ภาควิชาวิทยาการข้อมูล') {
                ?>
                    <li><a href="../php/user-manage-ads.php">
                            <i class="uil uil-users-alt"></i>
                            <span class="link-name">จัดการข้อมูลนิสิต</span>
                        </a></li>
                <?php
                } ?>

                <?php
                if (isset($userRoles) && strtolower(trim($userRoles)) === 'แอดมิน' || isset($userRoles) && strtolower(trim($userRoles)) === 'ภาควิชาวิทยาการคอมพิวเตอร์' || isset($userRoles) && strtolower(trim($userRoles)) === 'ภาควิชาวิทยาการข้อมูล') {
                ?>
                    <li><a href="register-result-page.php">
                            <i class="uil uil-file-landscape-alt"></i>
                            <span class="link-name">กิจกรรมทั้งหมด</span>
                        </a></li>
                <?php
                } ?>

                <li><a href="register-result-user.php">
                        <i class="uil uil-file-landscape-alt"></i>
                        <span class="link-name">กิจกรรมที่ลงทะเบียน</span>
                    </a></li>

                <!-- <li><a href="certificate-page.php">
                            <i class="uil uil-postcard"></i>
                            <span class="link-name">เกียรติบัตร</span>
                        </a></li> -->

                <li><a href="calendar_activity.php">
                        <i class="uil uil-calender"></i>
                        <span class="link-name">ปฏิทินกิจกรรม</span>
                    </a></li>
            </ul>

            <ul class="logout-mode">
                <li><a href="logout.php">
                        <i class="uil uil-signout"></i>
                        <span class="link-name">ออกจากระบบ</span>
                    </a></li>

                <li class="mode"><a href="#">
                        <i class="uil uil-moon"></i>
                        <span class="link-name">Dark Mode</span>
                    </a>
                    <div class="mode-toggle">
                        <span class="switch"></span>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <script src="../script/script_menu.js"></script>
</body>

</html>