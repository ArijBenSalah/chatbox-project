<?php
require_once(__DIR__ . "/../../controller/messageController.php");
require_once(__DIR__ . "/../../model/message.php");
require_once(__DIR__ . "/../../config.php");

session_start();
header('Content-Type: application/json');

// ✅ CSRF check
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    exit;
}

// ✅ Parameter check
if (!isset($_POST['id']) || !isset($_POST['content'])) {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit;
}

$messageController = new messageController();
$id = (int) $_POST['id'];
$content = trim($_POST['content']);

try {
    // ✅ Get the existing message
    $original = $messageController->getMessageById($id);

    if (!$original || $original['sender_id'] != ($_SESSION['user_id'] ?? null)) {
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit;
    }

    // ✅ Create a new Message object
    $message = new Message(
        $original['sender_id'],      // sender_id
        $original['receiver_id'],    // receiver_id
        $content,                    // ✅ Use the correct content variable!
        new DateTime()
    );
    $message->setId($id);

    // ✅ Update the message
    $success = $messageController->updateMessage($message);

    echo json_encode(['success' => $success]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
