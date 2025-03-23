<?php
include '../config/dbconfig.php';

// Check if postName is passed in the URL
if (isset($_GET['postName'])) {
    $postName = urldecode($_GET['postName']);
} else {
    echo "ไม่มีข้อมูลกิจกรรมที่ต้องการดาวน์โหลด";
    exit;
}

// Fetch the data for the specified postName
$registerRef = $database->getReference("register")->getValue();

// Filter registrations by postName
$registrations = [];
foreach ($registerRef as $registration) {
    if ($registration['postName'] === $postName) {
        $registrations[] = $registration;
    }
}

// Check if there are registrations to download
if (empty($registrations)) {
    echo "ไม่มีข้อมูลสำหรับกิจกรรมนี้";
    exit;
}

// Create a CSV file from the registration data
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="activity_' . $postName . '.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Add CSV headers
fputcsv($output, ['รหัสนักศึกษา', 'ชื่อ-นามสกุล', 'สาขา', 'รหัสเช็คอิน']);

// Add data rows
foreach ($registrations as $registration) {
    fputcsv($output, [
        $registration['studentID'],
        $registration['displayName'],
        $registration['major'],
        $registration['codeCheckIn']
    ]);
}

// Close output stream
fclose($output);
exit;
?>
