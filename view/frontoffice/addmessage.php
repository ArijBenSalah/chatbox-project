<?php
require_once(__DIR__ . "/../../controller/chatboxcontroller.php");
require_once(__DIR__ . "/../../model/message.php");
require_once(__DIR__ . "/../../config.php");
require_once(__DIR__ . "/../../controller/messageController.php");

$messagesController = new messageController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['messageInput'])) {
    // Récupérer et nettoyer le message
    $messageContent = trim($_POST['messageInput']);
    $idreciever = $_POST['idreciever']; // ID du destinataire

    // Vérification : message vide
    if (empty($messageContent)) {
        header("Location: contact.php?error=empty");
        exit();
    }

    // Vérification : taille limite (optionnelle)
    if (strlen($messageContent) > 1000) {
        header("Location: contact.php?error=too_long");
        exit();
    }

    // Créer un objet Message
    $chatboxController = new chatboxcontroller();
    $chat = $chatboxController->getChatboxByIdsenderAndReciever(1,$idreciever);  
    if (!$chat) {
        $chatboxController->addChatbox(1, $idreciever); 
        $chat = $chatboxController->getChatboxByIdsenderAndReciever(1,$idreciever);
    }
    if ($messagesController->addMessage($messageContent, $chat['idChatbox'])) {
        header("Location: contact.php?user_id=$idreciever&success=message_sent");
        exit();
    } else {
        header("Location: contact.php?error=insert_failed");
        exit();
    }
} else {
    header("Location: contact.php?error=invalid_request");
    exit();
}
?>
