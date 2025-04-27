<?php
require_once(__DIR__ . "/../../controller/chatboxcontroller.php");
require_once(__DIR__ . "/../../model/message.php");
require_once(__DIR__ . "/../../config.php");
require_once(__DIR__ . "/../../controller/messageController.php");

$messagesController = new messageController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['messageInput'])) {
    // Récupérer et nettoyer le message
    $messageContent = trim($_POST['messageInput']);

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
    $message = new Message(
        null,           // sender_id (à compléter si besoin)
        null,           // receiver_id (à compléter si besoin)
        $messageContent,
        new DateTime()  // Date actuelle
    );

    // Enregistrer le message
    if ($messagesController->addMessage($message)) {
        header("Location: contact.php?success=1");
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
