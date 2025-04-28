<?php

class Message {
    private $content;
    private $created_at;
    private $idChatbox;
    
    public function __construct($content, $created_at, $idChatbox) {
        $this->content = $content;
        $this->created_at = $created_at;
        $this->idChatbox = $idChatbox;
    }
    
    public function getContent() {
        return $this->content;
    }
    
    public function getIdChatbox() {
        return $this->idChatbox;
    }
    
}
?>
