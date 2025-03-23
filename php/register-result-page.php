<?php
session_start();

include('../config/dbconfig.php');

// ตรวจสอบว่า session มีข้อมูลผู้ใช้หรือไม่
if (!isset($_SESSION['verified_user_id'])) {
    echo "ผู้ใช้งานยังไม่ได้เข้าสู่ระบบ.";
    exit;
}

$uid = $_SESSION['verified_user_id'];  // User ID จาก session
$ref_table_post = "post";  // ชื่อตารางโพสต์ใน Firebase

// ดึงข้อมูลโพสต์ที่เป็นกิจกรรม (postType = 'กิจกรรม')
$posts = $database->getReference($ref_table_post)
    ->orderByChild("postType")
    ->equalTo("กิจกรรม")  // ฟิลด์ postType ต้องเป็น "กิจกรรม"
    ->getValue();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/top_style.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <title>หน้าหลัก</title>
</head>

<body>

    <?php
    include '../includes/menu.php';
    ?>

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
                <i class="uil uil-file-landscape-alt"></i>
                <span class="text">หน้ารวมกิจกรรม</span>
            </div>
        </div>

        <div class="register-row">
            <?php

            date_default_timezone_set("Asia/Bangkok"); // ตั้งค่าโซนเวลาไทย

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

            // ดึงวันที่ปัจจุบัน
            $day = date("j"); // วันที่
            $month = $thaiMonths[date("F")]; // แปลงชื่อเดือนเป็นภาษาไทย
            $year = date("Y") + 543; // แปลงปี ค.ศ. เป็น พ.ศ.

            $currentDateThai = "$day $month $year"; // รูปแบบวันที่ไทย
            $currentTime = date("H:i"); // เวลาปัจจุบัน

            // ตรวจสอบว่าโพสต์ที่เป็นกิจกรรมมีหรือไม่
            if (!empty($posts)) {
                // ใช้ array_reverse เพื่อให้โพสต์ล่าสุดอยู่ด้านบน
                $posts = array_reverse($posts);

                foreach ($posts as $postID => $post) {  // ใช้ $postID เป็นคีย์หลักของโพสต์
                    if (isset($post['postName'], $post['description'], $post['location'], $post['date'], $post['startTime'], $post['endTime'])) {
                        $postName = htmlspecialchars($post['postName']);
                        $description = htmlspecialchars($post['description']);
                        $location = htmlspecialchars($post['location']);
                        $date = htmlspecialchars($post['date']);
                        $startTime = htmlspecialchars($post['startTime']);
                        $endTime = htmlspecialchars($post['endTime']);

                        // ตรวจสอบว่าวันนี้ตรงกับวันที่ของกิจกรรมและเวลาปัจจุบันเกิน endTime หรือไม่
                        $status = ($currentDateThai == $date && $currentTime >= $endTime) ? "เสร็จสิ้น" : "กำลังดำเนินการ";
            ?>
                        <div class="register-column">
                            <div class="card-header">
                                <h3 class="card-title">กิจกรรม<?= $postName; ?></h3>
                            </div>
                            <div class="card-body">
                                <p><strong>รายละเอียด:</strong> <?= $description; ?></p>
                                <p><strong>สถานที่:</strong> <?= $location; ?></p>
                                <p><strong>วันที่:</strong> <?= $date; ?></p>
                                <p><strong>เวลา:</strong> <?= $startTime; ?> ถึง <?= $endTime; ?></p>
                                <br>
                                <p><strong>สถานะ:</strong> <?= $status; ?></p>
                            </div>
                            <div class="card-footer">
                                <a href="register-page.php?postID=<?= $postID ?>" class="btn btn-primary">ดูรายชื่อผู้เข้าร่วม</a>
                            </div>
                        </div>
            <?php
                    }
                }
            } else {
                echo "<p>ยังไม่มีกิจกรรมที่ลงทะเบียน.</p>";
            }
            ?>
        </div>

    </section>

    <script src="../script/script_sidebar.js"></script>
</body>

</html>