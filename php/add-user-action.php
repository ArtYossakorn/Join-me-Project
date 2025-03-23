<?php
session_start();
include('../config/dbconfig.php');  // รวมไฟล์การเชื่อมต่อ Firebase

if (isset($_POST['save'])) {
    // Collecting form data
    $email = $_POST['email'];
    $studentID = $_POST['studentID'];
    $title = $_POST['title'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $department = $_POST['department'];
    $major = $_POST['major'];
    $phoneNumber = $_POST['phoneNumber'];

    // Hashing the password before saving it
    $password = password_hash($studentID, PASSWORD_DEFAULT); // Hashing the studentID as password

    $userRoles = isset($_POST['userRoles']) ? $_POST['userRoles'] : 'User';  // If no userRoles is set, default to 'User'

    // Default value for statusPost
    $statusPost = 'false';   // Default to false

    // Default profile image URL
    if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profileImage']['tmp_name'];
        $fileName = $_FILES['profileImage']['name'];
        $fileSize = $_FILES['profileImage']['size'];
        $fileType = $_FILES['profileImage']['type'];

        // ตรวจสอบว่าโฟลเดอร์อัปโหลดมีอยู่หรือไม่ ถ้าไม่มีให้สร้าง
        $uploadDir = '../uploads/profile_images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // กำหนดชื่อไฟล์ใหม่เพื่อป้องกันไฟล์ซ้ำ
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = uniqid('profile_', true) . '.' . $fileExt;
        $filePath = $uploadDir . $newFileName;

        // ตรวจสอบประเภทไฟล์ที่อนุญาต
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($fileTmpPath, $filePath)) {
                $profileImage = $filePath; // เก็บ URL หรือ path ของรูปที่อัปโหลดได้
            } else {
                $_SESSION['status'] = 'ไม่สามารถอัปโหลดไฟล์ได้';
                header('Location: add-user.php');
                exit();
            }
        } else {
            $_SESSION['status'] = 'ไฟล์ไม่รองรับ';
            header('Location: add-user.php');
            exit();
        }
    } else {
        $profileImage = 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_1280.png';
    }

    // Data to store in Firebase Authentication
    $userProperties = [
        'email' => $email,
        'emailVerified' => false,
        'phoneNumber' => '+66' . $phoneNumber,  // Include country code for phone number
        'password' => $studentID,  // Firebase Authentication will handle hashing
        'displayName' => $firstName . ' ' . $lastName,
    ];

    // Create user in Firebase Authentication
    try {
        $createdUser = $auth->createUser($userProperties);

        if ($createdUser) {
            // Get UID of the created user
            $uid = $createdUser->uid;

            // Data to store in Realtime Database using UID as key
            $postData = [
                'email' => $email,
                'studentID' => $studentID,
                'title' => $title,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'department' => $department,
                'major' => $major,
                'phoneNumber' => $phoneNumber,
                'password' => $password,  // Store the hashed password
                'userRoles' => $userRoles,
                'statusPost' => $statusPost,  // Default value for statusPost
                'profileImage' => $profileImage  // Add profile image URL to Realtime Database
            ];

            // Store user data in Realtime Database using the UID as the key
            $ref_table = "users/" . $uid;  // Use UID as the reference path
            $postRef_result = $database->getReference($ref_table)->set($postData);

            if ($postRef_result) {
                // Set custom claims (e.g., userRoles)
                $auth->setCustomUserClaims($uid, [
                    'title' => $title,
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'userRoles' => $userRoles,
                    'studentID' => $studentID,
                    'department' => $department,
                    'major' => $major,
                    'statusPost' => 'false',  // Default to false
                    'profileImage' => $profileImage  // Add profile image URL to custom claims
                ]);

                $_SESSION['status'] = "เพิ่มสมาชิกสำเร็จ";
                header('Location: user-manage.php');
                exit();
            } else {
                $_SESSION['status'] = "เพิ่มสมาชิกไม่สำเร็จ";
                header('Location: user-manage.php');
                exit();
            }
        } else {
            $_SESSION['status'] = "สร้างผู้ใช้ไม่สำเร็จ";
            header('Location: user-manage.php');
            exit();
        }
    } catch (\Kreait\Firebase\Exception\AuthException $e) {
        $_SESSION['status'] = "เกิดข้อผิดพลาดในการสร้างผู้ใช้ใน Firebase Authentication: " . $e->getMessage();
        header('Location: user-manage.php');
        exit();
    }
}
