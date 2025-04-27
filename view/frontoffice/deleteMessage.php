<?php
require_once(__DIR__ . "/../../controller/messageController.php");
require_once(__DIR__ . "/../../config.php");

session_start();

// Verify the request method and ID parameter
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['csrf_token'])) {
    try {
        // Verify CSRF token
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            header("HTTP/1.1 403 Forbidden");
            exit("Invalid CSRF token");
        }

        $messageController = new messageController();
        $messageId = (int)$_POST['id'];
        
        
        $message = $messageController->getMessageById($messageId);
        
        if (!$message || $message['sender_id'] != ($_SESSION['user_id'] ?? null)) {
            header("HTTP/1.1 403 Forbidden");
            exit("You don't have permission to delete this message");
        }
        
        // Delete the message
        $result = $messageController->deleteMessage($messageId);
        
        if ($result) {
            $_SESSION['success'] = "Message deleted successfully";
            header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'contact.php'));
            exit();
        } else {
            throw new Exception("Failed to delete message");
        }
        
    } catch (Exception $e) {
        // Log the error
        error_log("Error deleting message: " . $e->getMessage());
        
        // Redirect with error message
        $_SESSION['error'] = "An error occurred while deleting the message";
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'contact.php'));
        exit();
    }
} else {
    header("HTTP/1.1 400 Bad Request");
    exit("Invalid request");
}
?>