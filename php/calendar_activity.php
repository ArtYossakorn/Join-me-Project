<?php
session_start();
include('../config/dbconfig.php');

if (!isset($_SESSION['verified_user_id'])) {
    echo "No user logged in.";
    exit;
}

$uid = $_SESSION['verified_user_id'];

$ref_table_post = 'post';
$fetchPosts = $database->getReference($ref_table_post)->getValue();

$events = [];

function convertThaiDateToISO($thaiDate, $time = "00:00")
{
    // รายชื่อเดือนภาษาไทย
    $thaiMonths = [
        'มกราคม' => 'January',
        'กุมภาพันธ์' => 'February',
        'มีนาคม' => 'March',
        'เมษายน' => 'April',
        'พฤษภาคม' => 'May',
        'มิถุนายน' => 'June',
        'กรกฎาคม' => 'July',
        'สิงหาคม' => 'August',
        'กันยายน' => 'September',
        'ตุลาคม' => 'October',
        'พฤศจิกายน' => 'November',
        'ธันวาคม' => 'December'
    ];

    // ใช้ Regex แยก วัน เดือน พ.ศ.
    if (preg_match('/(\d{1,2})\s(\S+)\s(\d{4})/', $thaiDate, $matches)) {
        $day = $matches[1];
        $monthThai = $matches[2];
        $yearThai = $matches[3];

        // แปลง พ.ศ. -> ค.ศ.
        $yearAD = $yearThai - 543;

        // แปลงชื่อเดือน
        if (isset($thaiMonths[$monthThai])) {
            $monthEN = $thaiMonths[$monthThai];

            // สร้างวันที่ใหม่ในรูปแบบ `Y-m-d H:i`
            $dateString = "$day $monthEN $yearAD $time";
            $dateTime = new DateTime($dateString, new DateTimeZone('Asia/Bangkok'));

            return $dateTime->format('Y-m-d\TH:i:sP'); // ISO Format
        }
    }
    return null;
}

if ($fetchPosts) {
    foreach ($fetchPosts as $key => $row) {
        if (!isset($row['date']) || empty($row['date'])) {
            continue; // ข้ามโพสต์ที่ไม่มีวันที่
        }

        $rawDate = $row['date'];
        $rawStartTime = isset($row['startTime']) ? $row['startTime'] : '00:00';
        $rawEndTime = isset($row['endTime']) ? $row['endTime'] : '23:59';

        // แปลงวันที่จากภาษาไทยเป็น ISO 8601
        $startDateTimeISO = convertThaiDateToISO($rawDate, $rawStartTime);
        $endDateTimeISO = convertThaiDateToISO($rawDate, $rawEndTime);

        if ($startDateTimeISO && $endDateTimeISO) {
            $events[] = [
                'title' => $row['postName'],
                'start' => $startDateTimeISO,
                'end' => $endDateTimeISO,
                'location' => $row['location'] ?? 'ไม่ระบุ',
                'postID' => $key,
                'rawDate' => $rawDate
            ];
        }
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ปฏิทินกิจกรรม</title>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
    <link rel="stylesheet" href="../css/calendar_style.css">
</head>

<body>
    <h1 style="text-align: center;">ปฏิทินกิจกรรม</h1>
    <div id="calendar"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const events = <?php echo json_encode($events); ?>;
            console.log("📅 Events Data:", events); // ตรวจสอบค่าที่ส่งจาก PHP

            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'th',
                timeZone: 'Asia/Bangkok',
                events: events,
                eventClick: function(info) {
                    window.location.href = 'post-detail.php?postID=' + info.event.extendedProps.postID;
                },
                initialView: 'dayGridMonth',
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: false
                },
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,dayGridWeek,dayGridDay'
                },
            });

            calendar.render();
        });
    </script>
</body>

</html>