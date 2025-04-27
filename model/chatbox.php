<?php

class Room {
    private ?int $idChatbox;
    private ?string $name;
    private ?string $created_by;
    private ?DateTime $created_at;

    public function __construct( ?string $name, ?int $created_by, ?DateTime $created_at) {
        $this->name = $name;
        $this->created_by = $created_by;
        $this->created_at = $created_at;
    }

    

    public function getName(): ?string { return $this->name; }
    public function setName(?string $name): void { $this->name = $name; }

  

    public function getCreatedBy(): ?int { return $this->created_by; }
    public function setCreatedBy(?int $created_by): void { $this->created_by = $created_by; }

    public function getCreatedAt(): ?DateTime { return $this->created_at; }
    public function setCreatedAt(?DateTime $created_at): void { $this->created_at = $created_at; }
}
?>
