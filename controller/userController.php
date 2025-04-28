<?php

require_once(__DIR__ . "/../config.php");


class userController
{
    public function ListeUser()
    {
        $db = config::getConnexion();
        $sql = "SELECT * FROM users";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
    }
    public function getUserById($id)
    {
        $db = config::getConnexion();
        $sql = "SELECT * FROM users WHERE id = :id";
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}   


?>