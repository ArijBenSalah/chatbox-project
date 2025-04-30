<?php
require_once(__DIR__ . "/../config.php");

class userController {

    public function getOtherUsers($username, $search = '')
    {
        $db = config::getConnexion();
        try {
            if (!empty($search)) {
                $sql = "SELECT * FROM users 
                        WHERE username != :username 
                        AND (username LIKE :search OR nom LIKE :search OR prenom LIKE :search)";
                $stmt = $db->prepare($sql);
                $searchWildcard = "%" . $search . "%";
                $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                $stmt->bindParam(':search', $searchWildcard, PDO::PARAM_STR);
            } else {
                $sql = "SELECT * FROM users WHERE username != :username";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
            return [];
        }
    }

    public function getUserById($id)
    {
        $db = config::getConnexion();
        try {
            $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
            return null;
        }
    }

}
