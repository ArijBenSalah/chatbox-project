<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session for authentication
session_start();

require_once(__DIR__ . "/../../config.php");
require_once(__DIR__ . "/../../controller/messageController.php");

// Set JSON header for potential AJAX responses
header('Content-Type: application/json');

try {
    // Verify request method
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception('Invalid request method', 405);
    }

 
    // Validate message ID
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception('Message ID required', 400);
    }

    $messageId = (int)$_GET['id'];
    if ($messageId <= 0) {
        throw new Exception('Invalid message ID', 400);
    }

    $messageController = new messageController();
    
    // Verify message exists and belongs to user
    $message = $messageController->getMessageById($messageId);
    
    if (!$message) {
        throw new Exception('Message not found', 404);
    }
    
  
    
   

    // Delete the message
    $success = $messageController->deleteMessage($messageId);
    
    if (!$success) {
        throw new Exception('Failed to delete message', 500);
    }

    // Success response (redirect back or return JSON)
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        echo json_encode(['success' => true]);
        exit;
    } else {
        header("Location: " . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'contact.php') . "?success=message_deleted");
        exit;
    }

} catch (Exception $e) {
    // Error handling
    $statusCode = $e->getCode() ?: 500;
    http_response_code($statusCode);
    
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        echo json_encode([
            'error' => $e->getMessage(),
            'code' => $statusCode
        ]);
    } else {
        $errorMessages = [
            400 => 'invalid_id',
            401 => 'unauthorized',
            403 => 'forbidden',
            404 => 'message_not_found',
            500 => 'delete_failed'
        ];
        $errorCode = $errorMessages[$statusCode] ?? 'server_error';
        header("Location: " . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'contact.php') . "?error=" . $errorCode);
    }
    exit;
}