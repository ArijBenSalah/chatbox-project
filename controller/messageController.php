<?php
require_once(__DIR__ . "/../config.php");

class messageController {

    public function listeMessages($idsender, $idreceiver) {
        $db = config::getConnexion();
        $stmt = $db->prepare("
            SELECT 
                m.id AS message_id,
                m.content,
                m.created_at AS message_created_at,
                c.idsender,
                c.idreciever
            FROM messages m
            JOIN chatbox c ON m.idChatbox = c.idChatbox
            WHERE (c.idsender = :idsender AND c.idreciever = :idreceiver)
               OR (c.idsender = :idreceiver AND c.idreciever = :idsender)
            ORDER BY m.created_at ASC
        ");
        $stmt->bindParam(':idsender', $idsender, PDO::PARAM_INT);
        $stmt->bindParam(':idreceiver', $idreceiver, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addMessage($content, $idChatbox) {
        $db = config::getConnexion();
        $stmt = $db->prepare("INSERT INTO messages (content, created_at, idChatbox)
                              VALUES (:content, NOW(), :idChatbox)");
        $stmt->bindValue(':content', $content, PDO::PARAM_STR);
        $stmt->bindValue(':idChatbox', $idChatbox, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getMessageById($id) {
        $db = config::getConnexion();
        $stmt = $db->prepare("SELECT * FROM messages WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateMessage($id, $content) {
        $db = config::getConnexion();
        $stmt = $db->prepare("UPDATE messages SET content = :content WHERE id = :id");
        $stmt->bindValue(':content', $content, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteMessage($id) {
        $db = config::getConnexion();
        $stmt = $db->prepare("DELETE FROM messages WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
