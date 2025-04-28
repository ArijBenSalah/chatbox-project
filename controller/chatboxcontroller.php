<?php
require_once(__DIR__ . "/../config.php");
class chatboxcontroller{
    public function listeChatbox() {
        $db = config::getConnexion(); // Connexion à la base
        $req = $db->prepare("SELECT * FROM chatbox");
        $req->execute(); 
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
    public function addChatbox($idsender , $idreciever) {
        $db = config::getConnexion();
        $req = $db->prepare("INSERT INTO chatbox (idsender, idreciever, created_at) VALUES (:idsender, :idreciever, NOW())");
        $req->bindParam(':idsender', $idsender, PDO::PARAM_INT);
        $req->bindParam(':idreciever', $idreciever, PDO::PARAM_INT); // <-- Correction ici
        $req->execute();
        return $db->lastInsertId();
    }
    
    public function getChatboxByIdsenderAndReciever($id, $idreciever) {
        $db = config::getConnexion(); // Connexion à la base
        $req = $db->prepare("SELECT * FROM chatbox WHERE idsender = :id AND idreciever = :idreciever");
        $req->bindParam(':id', $id, PDO::PARAM_INT);
        $req->bindParam(':idreciever', $idreciever, PDO::PARAM_INT);
        $req->execute();
        return $req->fetch(PDO::FETCH_ASSOC);
    }
}


?>