<?php
require_once(__DIR__ . "/../../config.php");
require_once(__DIR__ . "/../../controller/messageController.php");

header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'error' => 'Only POST requests are allowed']);
    exit;
}

// Get the raw POST data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validate input
if (!isset($data['messageId']) || !isset($data['content'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

$messageId = $data['messageId'];
$newContent = trim($data['content']);

// Validate content
if (empty($newContent)) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'error' => 'Message content cannot be empty']);
    exit;
}

// Sanitize content (prevent XSS)
$newContent = htmlspecialchars($newContent, ENT_QUOTES, 'UTF-8');

try {
    $messageController = new messageController();
    
    // Verify message exists and belongs to current user
    $message = $messageController->getMessageById($messageId);
    
    if (!$message) {
        http_response_code(404); // Not Found
        echo json_encode(['success' => false, 'error' => 'Message not found']);
        exit;
    }
    
    
    
    // Update the message
    $success = $messageController->updateMessage($messageId, $newContent);
    
    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['success' => false, 'error' => 'Failed to update message']);
    }
    
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}