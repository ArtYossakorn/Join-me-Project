<?php
session_start();

include '../config/dbconfig.php';

// Ensure data is passed from the previous page
if (!isset($_POST['userID']) || !isset($_POST['postID'])) {
    echo "ไม่สามารถโหลดข้อมูลได้";
    exit;
}

// Retrieve data from POST
$userID = $_POST['userID'];
$postID = $_POST['postID'];

// Assuming you have fetched the necessary user and post data from Firebase or another source
// Example: Fetch user data (you can use your existing Firebase methods here)
try {
    $userDetails = $auth->getUser($userID); // Assuming you use Firebase Auth
    $displayName = isset($userDetails->displayName) ? htmlspecialchars($userDetails->displayName) : 'ไม่ระบุชื่อ';
    // You can fetch more data as needed, like major, studentID, etc.
} catch (Exception $e) {
    echo "ไม่พบผู้ใช้: " . $e->getMessage();
    exit();
}

// You may also need to fetch additional data related to the activity, like post details.
$postDetails = $database->getReference('post/' . $postID)->getValue();
$postName = isset($postDetails['postName']) ? htmlspecialchars($postDetails['postName']) : 'ไม่พบข้อมูลกิจกรรม';
$date = isset($postDetails['date']) ? htmlspecialchars($postDetails['date']) : 'ไม่พบข้อมูลกิจกรรม';
$location = isset($postDetails['location']) ? htmlspecialchars($postDetails['location']) : 'ไม่พบข้อมูลกิจกรรม';
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบรายชื่อผู้เข้าร่วมกิจกรรม</title>
    <link rel="stylesheet" href="../css/certificate_style.css">
</head>

<body>
    <div class="a4-container" id="certificate">
        <div class="a4-page">
            <div class="text-content">
                <p>สาขาวิทยาการคอมพิวเตอร์</p>
                <p>คณะวิทยาการสารสนเทศ มหาวิทยาลัยสารสนเทศ</p>
            </div>
            <div class="small-text">
                <p>ขอมอบเกียรติบัตรนี้ไว้ให้เพื่อแสดงว่า</p>
            </div>
            <div class="name-text">
                <h4> <?= $displayName ?></h4>
            </div>
            <div class="text-content1">
                <p>ได้ผ่านการเข้าร่วมกิจกรรม <?= $postName ?></p>
                <p>ณ <?= $location ?> วันที่ <?= $date ?></p>
            </div>

            <div class="img">
                <img src="../img/cer2.png" alt="">
            </div>

            <div class="line">
                _______________________
            </div>
            <div class="Tname">
                <p>(ผศ.ดร.ฉัตรเกล้า เจริญผล)</p>
            </div>

            <div class="position">
                <p>ผู้ช่วยศาสตราจารย์</p>
            </div>

        </div>
        <button id="downloadPdf">ดาวน์โหลด PDF</button>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        document.getElementById("downloadPdf").addEventListener("click", function() {
            const {
                jsPDF
            } = window.jspdf;
            const pdf = new jsPDF('l', 'mm', 'a4');

            html2canvas(document.querySelector(".a4-page")).then(canvas => {
                const imgData = canvas.toDataURL("image/png");
                const imgWidth = 297;
                const imgHeight = (canvas.height * imgWidth) / canvas.width;
                pdf.addImage(imgData, "PNG", 0, 0, imgWidth, imgHeight);
                pdf.save("certificate.pdf");
            });
        });
    </script>
</body>

</html>