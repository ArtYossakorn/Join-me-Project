<?php
session_start();
include('../config/dbconfig.php');

if (!isset($_SESSION['verified_user_id'])) {
    echo "No user logged in.";
    exit;
}

$uid = $_SESSION['verified_user_id'];

if (!isset($_POST['postID']) || empty($_POST['postID'])) {
    echo "ไม่พบ postID";
    exit;
}

$postID = $_POST['postID'];

$ref_table = "register";
$ref_post = "post"; // Table for post details

// ฟังก์ชันสำหรับสร้างรหัสสุ่ม 6 หลัก
function generateRandomCode($length = 6)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

// ตรวจสอบว่าผู้ใช้เคยลงทะเบียนโพสต์นี้หรือไม่
$existingRegistrations = $database->getReference($ref_table)
    ->orderByChild("userID")
    ->equalTo($uid)
    ->getValue();

$alreadyRegistered = false;
if (!empty($existingRegistrations)) {
    foreach ($existingRegistrations as $registration) {
        if ($registration['postID'] === $postID) {
            $alreadyRegistered = true;
            break;
        }
    }
}

if ($alreadyRegistered) {
    $_SESSION['status'] = "ท่านลงทะเบียนกิจกรรมนี้แล้ว";
    header('Location: home-page.php');
    exit();
}

// ตรวจสอบจำนวนผู้ลงทะเบียนในกิจกรรมนี้
$registrantsCount = $database->getReference($ref_table)
    ->orderByChild("postID")
    ->equalTo($postID)
    ->getValue();

$registrantsNumber = !empty($registrantsCount) ? count($registrantsCount) : 0;

// ดึงข้อมูลรายละเอียดกิจกรรม (เช่น limit) จาก Firebase
$postDetails = $database->getReference($ref_post . '/' . $postID)->getValue();

if (empty($postDetails) || !isset($postDetails['limit'])) {
    echo "ไม่พบข้อมูลกิจกรรมนี้หรือไม่ได้กำหนดจำนวนผู้เข้าร่วม";
    exit;
}

$postLimit = $postDetails['limit']; // จำนวนสูงสุดของผู้ลงทะเบียน

// ตรวจสอบว่ามีผู้ลงทะเบียนครบจำนวนหรือยัง
if ($registrantsNumber >= $postLimit) {
    $_SESSION['status'] = "ไม่สามารถลงทะเบียนได้ เนื่องจากมีผู้ลงทะเบียนเต็มจำนวนแล้ว!!";
    header('Location: home-page.php');
    exit();
}

// สร้างรหัสสุ่ม checkInCode
$checkInCode = generateRandomCode();

// บันทึกข้อมูลลง Firebase
$postData = [
    'userID' => $uid,
    'postID' => $postID,
    'checkInCode' => $checkInCode, // เพิ่ม checkInCode
    'statusCheck' => 'false'
];

$postRef_result = $database->getReference($ref_table)->push($postData);

if ($postRef_result) {
    $_SESSION['status'] = "ลงทะเบียนสำเร็จ";
    $_SESSION['checkInCode'] = $checkInCode; // เก็บรหัส checkInCode ไว้ใน session
    header('Location: home-page.php');
    exit();
} else {
    $_SESSION['status'] = "เกิดข้อผิดพลาดในการลงทะเบียน";
    header('Location: home-page.php');
    exit();
}
