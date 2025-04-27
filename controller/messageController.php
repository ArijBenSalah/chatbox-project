<?php
require_once(__DIR__ . "/../config.php");

class messageController {
    public function listeMessages() {
        $db = config::getConnexion();
        $req = $db->prepare("SELECT * FROM messages");
        $req->execute(); 
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addMessage($message) {
        $db = config::getConnexion();
        
        // Modified query to handle cases where IDs might be null
        $req = $db->prepare("INSERT INTO messages (content, created_at".
                            ($message->getSenderId() ? ", sender_id" : "").
                            ($message->getReceiverId() ? ", receiver_id" : "").
                            ") VALUES (:content, :created_at".
                            ($message->getSenderId() ? ", :sender_id" : "").
                            ($message->getReceiverId() ? ", :receiver_id" : "").
                            ")");
        
        // Always bind these values
        $req->bindValue(':content', $message->getContent(), PDO::PARAM_STR);
        $req->bindValue(':created_at', date('Y-m-d H:i:s'), PDO::PARAM_STR);
        
        // Conditionally bind IDs if they exist
        if ($message->getSenderId()) {
            $req->bindValue(':sender_id', $message->getSenderId(), PDO::PARAM_INT);
        }
        if ($message->getReceiverId()) {
            $req->bindValue(':receiver_id', $message->getReceiverId(), PDO::PARAM_INT);
        }
        
        return $req->execute();
    }
    public function deleteMessage($id) {
        $db = config::getConnexion();
        $req = $db->prepare("DELETE FROM messages WHERE id = :id");
        $req->bindValue(':id', $id, PDO::PARAM_INT);
        return $req->execute();
    }
    public function updateMessage($message) {
        $db = config::getConnexion();
        $req = $db->prepare("UPDATE messages SET content = :content, created_at = :created_at WHERE id = :id");
        $req->bindValue(':content', $message->getContent(), PDO::PARAM_STR);
        $req->bindValue(':created_at', date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $req->bindValue(':id', $message->getId(), PDO::PARAM_INT);
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