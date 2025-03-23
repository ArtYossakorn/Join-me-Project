<?php

session_start();
include('../config/dbconfig.php');  // รวมไฟล์การเชื่อมต่อ Firebase

if (isset($_POST['edit'])) {

$disable_enable = $_POST['statusLogin'];
$uid = $_POST['ena_dis_user_id'];

if ($disable_enable == "disable") {
    $updateUser = $auth->disableUser($uid);
} else {
    $updateUser = $auth->enableUser($uid);
}

    // Collecting form data
    $id = $_POST['userID'];
    $email = $_POST['email'];
    $studentID = $_POST['studentID'];
    $title = $_POST['title'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $department = $_POST['department'];
    $major = $_POST['major'];
    $phoneNumber = $_POST['phoneNumber'];
    $userRoles = isset($_POST['userRoles']) ? $_POST['userRoles'] : 'User';
    $statusPost = $_POST['statusPost'];

    // ดึงข้อมูลผู้ใช้จาก Firebase Authentication
    try {
        $userRecord = $auth->getUserByEmail($email);

        // ตรวจสอบว่ามีการกำหนดค่า profileImage ใน custom claims หรือไม่
        $profileImage = $userRecord->customClaims['profileImage'] ?? 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_1280.png'; // ใช้รูปเริ่มต้นหากไม่มีค่า

    } catch (\Kreait\Firebase\Exception\AuthException $e) {
        $_SESSION['status'] = "ไม่พบข้อมูลผู้ใช้ใน Firebase Authentication: " . $e->getMessage();
        header('Location: user-manage.php');
        exit();
    }

    // ตรวจสอบการอัปโหลดรูปโปรไฟล์ใหม่
    if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] === UPLOAD_ERR_OK) {
        // เก็บไฟล์ที่อัปโหลดในโฟลเดอร์บนเซิร์ฟเวอร์
        $fileTmpPath = $_FILES['profileImage']['tmp_name'];
        $fileName = $_FILES['profileImage']['name'];
        $fileSize = $_FILES['profileImage']['size'];
        $fileType = $_FILES['profileImage']['type'];

        // กำหนดโฟลเดอร์ที่เก็บรูป
        $uploadDir = '../uploads/profile_images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // กำหนดชื่อไฟล์ใหม่เพื่อป้องกันการซ้ำ
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = uniqid('profile_', true) . '.' . $fileExt;
        $filePath = $uploadDir . $newFileName;

        // ตรวจสอบประเภทไฟล์ที่อนุญาต
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($fileTmpPath, $filePath)) {
                $profileImage = $filePath; // เก็บ path ของรูปที่อัปโหลด
            } else {
                $_SESSION['status'] = 'ไม่สามารถอัปโหลดไฟล์ได้';
                header('Location: profile.php');
                exit();
            }
        } else {
            $_SESSION['status'] = 'ไฟล์ไม่รองรับ';
            header('Location: profile.php');
            exit();
        }
    }

    // ข้อมูลผู้ใช้ที่จะเก็บใน Realtime Database
    $updateData = [
        'email' => $email,
        'studentID' => $studentID,
        'title' => $title,
        'firstName' => $firstName,
        'lastName' => $lastName,
        'department' => $department,
        'major' => $major,
        'phoneNumber' => $phoneNumber,
        'userRoles' => $userRoles,
        'statusPost' => $statusPost,
        'profileImage' => $profileImage, // ใช้ค่ารูปภาพที่ได้จากการอัปโหลดหรือ custom claims
    ];

    // ดึงค่า userID จาก URL หรือจากข้อมูลที่ส่งมา
    $userID = $_GET['userID'];

    // อัปเดตข้อมูลใน Realtime Database
    $ref_table = "users/" . $id;
    $updateResult = $database->getReference($ref_table)->update($updateData);

    if ($updateResult) {
        // ข้อมูลที่จะอัปเดตใน Firebase Authentication
        $userProperties = [
            'email' => $email,
            'emailVerified' => false,
            'phoneNumber' => '+66' . $phoneNumber,
            'displayName' => $firstName . ' ' . $lastName
        ];

        // อัปเดตผู้ใช้ใน Firebase Authentication
        try {
            // ค้นหา UID ของผู้ใช้จากฐานข้อมูล
            $userRecord = $auth->getUserByEmail($email);

            // อัปเดตข้อมูลผู้ใช้ใน Firebase Authentication
            $auth->updateUser($userRecord->uid, $userProperties);

            // อัปเดต custom claims สำหรับผู้ใช้ที่เลือกเท่านั้น
            $uid = $userRecord->uid;  // รับ UID ของผู้ใช้ที่อัปเดต
            $auth->setCustomUserClaims($uid, [
                'title' => $title,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'userRoles' => $userRoles,
                'studentID' => $studentID,
                'department' => $department,
                'major' => $major,
                'statusPost' => $statusPost, // อัปเดต statusPost ของผู้ใช้ที่เลือกเท่านั้น
                'profileImage' => $profileImage
            ]);

            $_SESSION['status'] = "อัปเดตสมาชิกสำเร็จ";
            header('Location: user-manage.php');
            exit();
        } catch (\Kreait\Firebase\Exception\AuthException $e) {
            $_SESSION['status'] = "เกิดข้อผิดพลาดในการอัปเดตผู้ใช้ใน Firebase Authentication: " . $e->getMessage();
            header('Location: user-manage.php');
            exit();
        }
    } else {
        $_SESSION['status'] = "อัปเดตสมาชิกไม่สำเร็จ";
        header('Location: user-manage.php');
        exit();
    }
}
