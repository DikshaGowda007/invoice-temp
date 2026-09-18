<?php

namespace App\Repositories\DAO\V1;

class ClientDAO
{
    private ?int $userId = null;

    private ?string $name = null;

    private ?string $email = null;

    private ?string $phone = null;

    private ?string $address = null;

    private ?string $notes = null;

    private ?int $isDeleted = null;

    public function toArray(): array
    {
        $collection = [];

        if (isset($this->userId)) {
            $collection['user_id'] = $this->userId;
        }
        if (isset($this->name)) {
            $collection['name'] = $this->name;
        }
        if (isset($this->email)) {
            $collection['email'] = $this->email;
        }
        if (isset($this->phone)) {
            $collection['phone'] = $this->phone;
        }
        if (isset($this->address)) {
            $collection['address'] = $this->address;
        }
        if (isset($this->notes)) {
            $collection['notes'] = $this->notes;
        }
        if (isset($this->isDeleted)) {
            $collection['is_deleted'] = $this->isDeleted;
        }

        return $collection;
    }

    /**
     * Get the value of userId
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * Set the value of userId
     */
    public function setUserId(?int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get the value of name
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the value of name
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of email
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set the value of email
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of phone
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * Set the value of phone
     */
    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get the value of address
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * Set the value of address
     */
    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get the value of notes
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * Set the value of notes
     */
    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get the value of isDeleted
     */
    public function getIsDeleted(): ?int
    {
        return $this->isDeleted;
    }

    /**
     * Set the value of isDeleted
     */
    public function setIsDeleted(?int $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }
}
