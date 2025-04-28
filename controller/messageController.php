<?php
require_once(__DIR__ . "/../config.php");

class messageController {

    public function listeMessages($idsender, $idreceiver) {
        $db = config::getConnexion();
        $req = $db->prepare("
            SELECT 
                m.id AS message_id,
                m.content,
                m.created_at AS message_created_at,
                c.idsender,
                sender.username AS sender_name,
                sender.photo AS sender_photo,
                c.idreciever,
                receiver.username AS receiver_name,
                receiver.photo AS receiver_photo
            FROM messages m
            INNER JOIN chatbox c ON m.idChatbox = c.idChatbox
            INNER JOIN users sender ON c.idsender = sender.id
            INNER JOIN users receiver ON c.idreciever = receiver.id
            WHERE (c.idsender = :idsender AND c.idreciever = :idreceiver)
               OR (c.idsender = :idreceiver AND c.idreciever = :idsender)
            ORDER BY m.created_at ASC
        ");
        
        $req->bindParam(':idsender', $idsender, PDO::PARAM_INT);
        $req->bindParam(':idreceiver', $idreceiver, PDO::PARAM_INT);
        
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function addMessage($content, $idChatbox) {
        $db = config::getConnexion();
    
    
        if (empty($content)) {
            throw new Exception('Le contenu du message est vide.');
        }
        if (empty($idChatbox)) {
            throw new Exception('Le chatbox ID est vide.');
        }

        $req = $db->prepare("INSERT INTO messages (content, created_at, idChatbox) 
                             VALUES (:content, NOW(), :idChatbox)");
        $req->bindValue(':content', $content, PDO::PARAM_STR);
        $req->bindValue(':idChatbox', $idChatbox, PDO::PARAM_INT);
        return $req->execute();
    }
    
    public function deleteMessage($id) {
        $db = config::getConnexion();
        $req = $db->prepare("DELETE FROM messages WHERE id = :id");
        $req->bindValue(':id', $id, PDO::PARAM_INT);
        return $req->execute();
    }

    public function updateMessage($id,$content) {
        $db = config::getConnexion();
        

        if (empty($content)) {
            throw new Exception('Le contenu du message est vide.');
        }

        $req = $db->prepare("UPDATE messages 
                             SET content = :content, created_at = NOW() 
                             WHERE id = :id");
        $req->bindValue(':content', $content, PDO::PARAM_STR);
        $req->bindValue(':id', $id, PDO::PARAM_INT);
        return $req->execute();
    }

    public function getMessageById($id) {
        $db = config::getConnexion();
        $req = $db->prepare("SELECT * FROM messages WHERE id = :id");
        $req->bindValue(':id', $id, PDO::PARAM_INT);
        $req->execute();
        return $req->fetch(PDO::FETCH_ASSOC);
    }
}
?>
