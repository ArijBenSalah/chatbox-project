<?php

class Message {
    private ?int $id;
    private ?int $sender_id;
    private ?int $receiver_id;
    private ?string $content;
    private ?DateTime $created_at;

    // Constructor
    public function __construct( ?int $sender_id, ?int $receiver_id, ?string $content, ?DateTime $created_at) {
        $this->sender_id = $sender_id;
        $this->receiver_id = $receiver_id;
        $this->content = $content;
        $this->created_at = $created_at;
    }

    // Getters and Setters
    public function getId(): ?int {
        return $this->id;
    }

    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function getSenderId(): ?int {
        return $this->sender_id;
    }

    public function setSenderId(?int $sender_id): void {
        $this->sender_id = $sender_id;
    }

    public function getReceiverId(): ?int {
        return $this->receiver_id;
    }

    public function setReceiverId(?int $receiver_id): void {
        $this->receiver_id = $receiver_id;
    }

    public function getContent(): ?string {
        return $this->content;
    }

    public function setContent(?string $content): void {
        $this->content = $content;
    }

    public function getCreatedAt(): ?DateTime {
        return $this->created_at;
    }

    public function setCreatedAt(?DateTime $created_at): void {
        $this->created_at = $created_at;
    }
}

?>
