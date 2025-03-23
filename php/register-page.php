<?php
session_start();
include('../config/dbconfig.php');

// ตรวจสอบว่า session มีข้อมูลผู้ใช้หรือไม่
if (!isset($_SESSION['verified_user_id'])) {
    echo "ผู้ใช้งานยังไม่ได้เข้าสู่ระบบ.";
    exit;
}

$uid = $_SESSION['verified_user_id'];  // User ID จาก session
$ref_table_register = "register";      // ตารางการลงทะเบียนกิจกรรม
$ref_table_post = "post";              // ตารางกิจกรรม

// ตรวจสอบว่า postID ถูกส่งมาจาก URL หรือไม่
if (isset($_GET['postID'])) {
    $postID = $_GET['postID'];  // รับค่า postID จาก URL

    // ดึงข้อมูลกิจกรรมจาก Firebase Realtime Database
    $postDetails = $database->getReference('post/' . $postID)->getValue();

    if (!empty($postDetails)) {
        $postName = htmlspecialchars($postDetails['postName']);
        $date = htmlspecialchars($postDetails['date']);
        $location = htmlspecialchars($postDetails['location']);
        $startTime = htmlspecialchars($postDetails['startTime']);
        $endTime = htmlspecialchars($postDetails['endTime']);
    } else {
        echo "<p>ไม่พบข้อมูลกิจกรรมนี้.</p>";
        exit;
    }
} else {
    echo "<p>ไม่มีข้อมูลกิจกรรม.</p>";
    exit;
}

try {
    $user = $auth->getUser($uid);
    $displayName = isset($user->displayName) ? $user->displayName : 'ไม่ระบุชื่อ';
    $email = $user->email;
    $phoneNumber = isset($user->phoneNumber) ? $user->phoneNumber : 'ไม่ระบุหมายเลขโทรศัพท์';
    $customClaims = isset($user->customClaims) ? $user->customClaims : [];
    $userRoles = isset($customClaims['userRoles']) ? $customClaims['userRoles'] : 'ไม่ระบุบทบาท';

    if ($userRoles === 'User') {
        $userRoles = 'ผู้ใช้ทั่วไป';
    } else if ($userRoles === 'Admin') {
        $userRoles = 'แอดมิน';
    } else if ($userRoles === 'department') {
        $userRoles = 'สาขาวิชา';
    } else {
        $userRoles = 'ไม่มีบทบาท';
    }
} catch (Exception $e) {
    echo "User not found: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/top_style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <title>ใบรายชื่อผู้เข้าร่วมกิจกรรม</title>
</head>

<body>
    <h4>ใบรายชื่อผู้ลงทะเบียนเข้าร่วมกิจกรรม <?= $postName; ?></h4>
    <h4>วันที่ <?= $date; ?> ณ <?= $location; ?></h4>
    <h4>เวลา <?= $startTime; ?> ถึง <?= $endTime; ?> น.</h4>

    <?php if ($userRoles === "แอดมิน") { ?>
    <a href="print-regpaper.php?postID=<?= $postID ?>" class="download-button">ดาวน์โหลดใบรายชื่อ</a>
    <?php } ?>
    
    <div class="register-table-container">
        <table>
            <thead>
                <tr>
                    <th>ลำดับ</th>
                    <th>รหัสนิสิต</th>
                    <th>ชื่อ-นามสกุล</th>
                    <th>สาขา</th>
                    <th>หมายเหตุ</th>
                    <?php if ($userRoles === "แอดมิน") echo "<th>รหัสเช็คอิน</th>"; ?>
                    <th>สถานะเช็คอิน</th>
                    <th>ใส่รหัสเช็คอิน</th>
                    <th>เกียรติบัตร</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $registrants = $database->getReference($ref_table_register)
                    ->orderByChild("postID")
                    ->equalTo($postID)
                    ->getValue();

                if (!empty($registrants)) {
                    $i = 1;
                    foreach ($registrants as $registrant) {
                        $userID = $registrant['userID'];

                        try {
                            $userDetails = $auth->getUser($userID);
                            if (!empty($userDetails)) {
                                $displayName = htmlspecialchars($userDetails->displayName);
                                $claims = $userDetails->customClaims;
                                $studentID = isset($claims['studentID']) ? htmlspecialchars($claims['studentID']) : 'ไม่ระบุ';
                                $major = isset($claims['major']) ? htmlspecialchars($claims['major']) : 'ไม่ระบุ';
                                $checkInCode = isset($registrant['checkInCode']) ? htmlspecialchars($registrant['checkInCode']) : 'ไม่มีรหัสเช็คอิน';
                                $statusCheck = isset($registrant['statusCheck']) ? htmlspecialchars($registrant['statusCheck']) : '';

                                if ($statusCheck === 'true') {
                                    $statusCheck = 'เช็คอินเรียบร้อย';
                                } else if ($statusCheck === 'false') {
                                    $statusCheck = 'ยังไม่เช็คอิน';
                                } else {
                                    $statusCheck = 'ไม่ระบุ';
                                }

                                echo "<tr>";
                                echo "<td>" . $i++ . "</td>";
                                echo "<td>" . $studentID . "</td>";
                                echo "<td>" . $displayName . "</td>";
                                echo "<td>" . $major . "</td>";
                                echo "<td></td>";
                                if ($userRoles === "แอดมิน") {
                                    echo "<td>" . $checkInCode . "</td>";
                                }
                                echo "<td>" . $statusCheck . "</td>";

                                if ($userID === $uid) {
                                    echo "<td>
                                            <form method='POST' action='checkIn.php' class='check-in-form'>
                                                <input type='hidden' name='userID' value='$userID'>
                                                <input type='hidden' name='postID' value='$postID'>
                                                <input type='text' name='checkInCode' class='check-in-input' placeholder='ใส่รหัสเช็คอิน' required>
                                                <button type='submit' class='check-in-button'>ยืนยัน</button>
                                            </form>
                                        </td>";
                                } else {
                                    echo "<td>-</td>";
                                }

                                if ($statusCheck === 'เช็คอินเรียบร้อย' && $userID === $uid) {
                                    echo "<td>
                                        <form method='POST' action='certificate-page.php'>
                                            <input type='hidden' name='userID' value='$userID'>
                                            <input type='hidden' name='postID' value='$postID'>
                                            <button type='submit' class='check-in-button'>เกียรติบัตร</button>
                                        </form>
                                    </td>";
                                } else {
                                    echo "<td>-</td>";
                                }

                                echo "</tr>";
                            }
                        } catch (Exception $e) {
                            echo "<tr><td colspan='7'>ไม่สามารถดึงข้อมูลผู้ใช้ได้</td></tr>";
                        }
                    }
                } else {
                    echo "<tr><td colspan='7'>ยังไม่มีผู้ลงทะเบียน</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>