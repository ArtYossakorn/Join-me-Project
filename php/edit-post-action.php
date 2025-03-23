<?php
session_start();
include('../config/dbconfig.php');

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['verified_user_id'])) {
    echo "No user logged in.";
    exit;
}

$uid = $_SESSION['verified_user_id'];

try {
    $user = $auth->getUser($uid);
    $userClaims = $user->customClaims;
} catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
    echo "User not found: " . $e->getMessage();
    exit;
}

// ตรวจสอบ userRoles
$userRoles = isset($userClaims['userRoles']) ? $userClaims['userRoles'] : 'user';

// ตรวจสอบการส่งข้อมูลจากฟอร์ม
if (isset($_POST['edit-post-btn'])) {
    // รับข้อมูลจากฟอร์ม
    $postID = $_POST['postID'];
    $description = $_POST["description"];
    $postName = $_POST["postName"] ?? '';
    $postType = $_POST["postType"];
    $activityType = $_POST["activityType"];
    $location = $_POST["location"] ?? '';
    $date = $_POST["date"] ?? '';
    $startTime = $_POST["startTime"] ?? '';
    $endTime = $_POST["endTime"] ?? '';
    $limit = $_POST["limit"] ?? '';

    // ✅ ตั้งค่าภาษาให้รองรับไทย
    setlocale(LC_TIME, 'th_TH.utf8');

    // ✅ แปลงวันที่ให้เป็น "2 มีนาคม 2568"
    $dateObj = DateTime::createFromFormat("Y-m-d", $date);
    if ($dateObj) {
        $dateThai = $dateObj->format("j") . " " . $dateObj->format("F") . " " . ($dateObj->format("Y") + 543);
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

    // ✅ เวลาปัจจุบัน
    date_default_timezone_set("Asia/Bangkok");
    $editTime = date("Y-m-d H:i:s");

    // ✅ ดึงข้อมูลโพสต์เดิมจาก Firebase
    $ref_table = "post";
    $postRef = $database->getReference($ref_table)->getChild($postID);
    $existingPost = $postRef->getValue();

    // ✅ ตรวจสอบไฟล์อัปโหลด
    $imageUrls = isset($existingPost['images']) ? json_decode($existingPost['images'], true) : [];
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

    // ✅ เตรียมข้อมูลใหม่
    $updateData = [
        'description' => $description,
        'images' => json_encode($imageUrls),
        'postName' => $postName,
        'postType' => $postType,
        'activityType' => $activityType,
        'location' => $location,
        'date' => $dateThai,
        'startTime' => $startTime,
        'endTime' => $endTime,
        'limit' => $limit,
        'lastEdited' => $editTime,
    ];

    // ✅ อัปเดตโพสต์ใน Firebase
    $postRef->update($updateData);

    // ตรวจสอบว่าโพสต์ถูกอัปเดตสำเร็จหรือไม่
    if ($postRef) {
        $_SESSION['status'] = "อัปเดตโพสต์สำเร็จ";
        header('Location: home-page.php');
        exit();
    } else {
        $_SESSION['status'] = "อัปเดตโพสต์ล้มเหลว";
        header('Location: edit-post.php?postID=' . $postID);
        exit();
    }
} else {
    echo "Invalid request.";
}
?>
