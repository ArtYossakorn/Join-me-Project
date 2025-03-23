<?php

session_start();
include('../config/dbconfig.php');

use Kreait\Firebase\Auth as FirebaseAuth;

// ตรวจสอบว่า postID ถูกส่งมาหรือไม่
if (!isset($_GET['postID']) || empty($_GET['postID'])) {
    die("รหัสโพสต์ไม่ถูกต้อง!");
}

$postID = $_GET['postID'];

// ตรวจสอบว่า session มีข้อมูลผู้ใช้หรือไม่
if (!isset($_SESSION['verified_user_id'])) {
    die("กรุณาเข้าสู่ระบบก่อนทำรายการ!");
}

$uid = $_SESSION['verified_user_id'];

// ดึงข้อมูลผู้ใช้จาก Firebase Authentication
try {
    $user = $auth->getUser($uid);
    $userClaims = $user->customClaims; // ดึงข้อมูล customClaims ซึ่งเก็บ userRoles ไว้
} catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
    die("ผู้ใช้ไม่พบในระบบ: " . $e->getMessage());
}

// ตรวจสอบว่า userRoles ถูกกำหนดหรือไม่
$userRoles = isset($userClaims['userRoles']) ? $userClaims['userRoles'] : 'user'; // ค่าเริ่มต้นเป็น 'user'

// ดึงข้อมูลโพสต์จาก Firebase
$ref = $database->getReference('post/' . $postID);
$postData = $ref->getValue();

// ตรวจสอบว่าโพสต์มีอยู่จริง
if (!$postData) {
    die("ไม่พบโพสต์ที่ต้องการลบ!");
}

// ตรวจสอบว่าสิทธิ์ผู้ใช้เป็นเจ้าของโพสต์ หรือเป็นแอดมิน
if ($postData['userID'] !== $uid && $userRoles !== 'Admin') {
    $_SESSION['status'] = "คุณไม่มีสิทธิ์ลบโพสต์นี้!"; // เก็บข้อความใน session
    header('Location: home-page.php');
    exit();
}

// ดำเนินการลบโพสต์
$ref->remove();

if ($ref) {
    $_SESSION['status'] = "ลบโพสต์เรียบร้อย!";
    header('Location: home-page.php');
    exit();
}

// $_SESSION['status'] = "ลบโพสต์เรียบร้อย!"; // กำหนดข้อความ session
// echo "<script>
//         alert('" . $_SESSION['status'] . "'); // แสดงข้อความใน alert
//         header('Location: home-page.php');
//     </script>";
