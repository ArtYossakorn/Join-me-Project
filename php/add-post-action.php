<?php

session_start();
include('../config/dbconfig.php');

// Check if user is logged in
if (!isset($_SESSION['verified_user_id'])) {
    echo "No user logged in.";
    exit;
}

$uid = $_SESSION['verified_user_id'];

// Fetch user data from Firebase Authentication
try {
    $user = $auth->getUser($uid);
    $userClaims = $user->customClaims;
} catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
    echo "User not found: " . $e->getMessage();
    exit;
}

// ตรวจสอบ userRoles
$userRoles = isset($userClaims['userRoles']) ? $userClaims['userRoles'] : 'user';

// ตรวจสอบว่ามีการส่งฟอร์มหรือไม่
if (isset($_POST['create-post-btn'])) {
    $description = $_POST["description"];
    $postName = $_POST["postName"];
    $postType = $_POST["postType"];
    $activityType = $_POST["activityType"];
    $location = $_POST["location"];
    $date = $_POST["date"];
    $startTime = $_POST["startTime"];
    $endTime = $_POST["endTime"];
    $limit = $_POST["limit"];
    $status = $_POST["status"];

    // ✅ ตั้งค่าภาษาให้รองรับไทย
    setlocale(LC_TIME, 'th_TH.utf8');

    // ✅ แปลงวันที่ให้เป็น "2 มีนาคม 2568"
    $dateObj = DateTime::createFromFormat("Y-m-d", $date);
    if ($dateObj) {
        $dateThai = $dateObj->format("j") . " " . $dateObj->format("F") . " " . ($dateObj->format("Y") + 543);

        // ✅ เปลี่ยนชื่อเดือนเป็นภาษาไทย
        $thaiMonths = [
            "January" => "มกราคม",
            "February" => "กุมภาพันธ์",
            "March" => "มีนาคม",
            "April" => "เมษายน",
            "May" => "พฤษภาคม",
            "June" => "มิถุนายน",
            "July" => "กรกฎาคม",
            "August" => "สิงหาคม",
            "September" => "กันยายน",
            "October" => "ตุลาคม",
            "November" => "พฤศจิกายน",
            "December" => "ธันวาคม"
        ];
        $dateThai = str_replace(array_keys($thaiMonths), array_values($thaiMonths), $dateThai);
    } else {
        $dateThai = $date;
    }

    // ✅ กำหนดค่าเวลาปัจจุบัน
    date_default_timezone_set("Asia/Bangkok");
    $postTime = date("Y-m-d H:i:s");

    $imageUrls = [];

    // ✅ ตรวจสอบว่ามีการอัปโหลดไฟล์
    if (isset($_FILES['images']) && count($_FILES['images']['name']) > 0) {
        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        for ($i = 0; $i < count($_FILES['images']['name']); $i++) {
            $imageName = basename($_FILES['images']['name'][$i]);
            $imageTmpName = $_FILES['images']['tmp_name'][$i];
            $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);
            $newFileName = uniqid() . '.' . $imageExtension;
            $filePath = $uploadDir . $newFileName;

            if (move_uploaded_file($imageTmpName, $filePath)) {
                $imageUrls[] = $filePath;
            }
        }
    }

    $displayName = $user->displayName;

    // ✅ เตรียมข้อมูลโพสต์
    $postData = [
        'description' => $description,
        'images' => json_encode($imageUrls),
        'postName' => $postName,
        'postType' => $postType,
        'activityType' => $activityType,
        'location' => $location,
        'date' => $dateThai, // ✅ บันทึกวันที่เป็น "2 มีนาคม 2568"
        'startTime' => $startTime,
        'endTime' => $endTime,
        'limit' => $limit,
        'userPost' => $displayName,
        'userRoles' => $userRoles,
        'userID' => $uid,
        'postTime' => $postTime,
        'status' => 'กำลังดำเนินการ'
    ];

    // ✅ บันทึกข้อมูลโพสต์ลง Firebase Database
    $ref_table = "post";
    $postRef_result = $database->getReference($ref_table)->push($postData);

    if ($postRef_result) {
        $_SESSION['status'] = "Create post successfully";
        header('Location: home-page.php');
        exit();
    } else {
        $_SESSION['status'] = "Create post not successfully";
        header('Location: add-post.php');
        exit();
    }
}
