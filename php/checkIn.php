<?php
session_start();
include('../config/dbconfig.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userID = $_POST['userID'] ?? null;
    $postID = $_POST['postID'] ?? null;
    $inputCode = $_POST['checkInCode'] ?? '';

    // ดึงข้อมูล register ตาม userID
    $registrants = $database->getReference("register")->getValue();

    foreach ($registrants as $regID => $registrant) {
        if ($registrant['userID'] === $userID && $registrant['postID'] === $postID) {
            $firebaseCheckInCode = $registrant['checkInCode'];

            if ($firebaseCheckInCode === $inputCode) {
                // อัปเดต statusCheck เป็น true
                $database->getReference("register/$regID")->update(['statusCheck' => 'true']);

                echo "<script>alert('เช็คอินสำเร็จ!'); window.location.href='register-result-user.php';</script>";
                exit();
            } else {
                echo "<script>alert('รหัสเช็คอินไม่ถูกต้อง!'); window.history.back();</script>";
                exit();
            }
        }
    }
    echo "<script>alert('ไม่พบข้อมูลการลงทะเบียน!'); window.history.back();</script>";
}
?>
