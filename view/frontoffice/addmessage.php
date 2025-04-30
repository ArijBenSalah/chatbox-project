<?php
require_once(__DIR__ . "/../../config.php");
require_once(__DIR__ . "/../../controller/messageController.php");

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $senderId = $_POST['senderId'];
    $receiverId = $_POST['idreciever'];
    $content = trim($_POST['messageInput']);
    $filePath = null;

    // Handle file upload
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === 0) {
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = basename($_FILES['attachment']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetPath)) {
            $filePath = 'uploads/' . $fileName;
        }
    }

    $controller = new messageController();
    $controller->addMessage($content, $senderId, $receiverId, $filePath);

    header("Location: contact.php?user_id=$receiverId");
    exit;
}
?>
