<?php
require 'dbconfig.php'; // เชื่อมต่อ Firebase
use Kreait\Firebase\Auth;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['userId']) && isset($_POST['status'])) {
    $userId = $_POST['userId']; // Firebase UID
    $status = $_POST['status']; // "enable" หรือ "disable"

    try {
        if ($status === 'enable') {
            $auth->updateUser($userId, ['disabled' => false]);
            $newStatus = false; // ปลดล็อก
        } elseif ($status === 'disable') {
            $auth->updateUser($userId, ['disabled' => true]);
            $newStatus = true; // ล็อกบัญชี
        } else {
            throw new Exception("Invalid status value");
        }

        echo json_encode(['success' => true, 'newStatus' => $newStatus]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>
