<?php
require_once(__DIR__ . "/../config.php");
class chatboxcontroller{
    public function listeChatbox() {
        $db = config::getConnexion(); // Connexion à la base
        $req = $db->prepare("SELECT * FROM chatbox");
        $req->execute(); 
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
    public function addChatbox($sponsor){
        $db = config::getConnexion(); // Connexion à la base
        $req = $db->prepare("INSERT INTO chatbox (name, created_by, created_at) VALUES (:name, :created_by, :created_at)");
    }
}


?>