<?php
require_once(__DIR__ . "/../../config.php");
require_once(__DIR__ . "/../../controller/messageController.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Only POST requests are allowed']);
    exit;
}

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!isset($data['messageId']) || !isset($data['content'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

$messageId = (int)$data['messageId'];
$newContent = trim($data['content']);

if (empty($newContent)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Message content cannot be empty']);
    exit;
}

try {
    $controller = new messageController();
    $message = $controller->getMessageById($messageId);
    if (!$message) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Message not found']);
        exit;
    }

    $controller->updateMessage($messageId, $newContent);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
