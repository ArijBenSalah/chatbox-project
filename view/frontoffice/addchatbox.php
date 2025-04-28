<?php
require_once '../controller/chatboxcontroller.php'; // chemin correct si ton controller est dans un dossier "controller"

if (isset($_POST['name']) && isset($_POST['created_by'])) {
    $chatboxController = new chatboxcontroller();
    $chatboxController->addChatbox($_POST['name'], $_POST['created_by']);
    echo "✅ Chatbox ajoutée avec succès !";
} else {
    echo "❗ Veuillez remplir tous les champs.";
}
?>
