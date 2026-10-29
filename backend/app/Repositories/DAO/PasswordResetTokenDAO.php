<?php

namespace App\Repositories\DAO;

class PasswordResetTokenDAO
{
    private ?string $email = null;

    private ?string $token = null;

    private ?string $createdAt = null;

    private ?int $isDeleted = null;

    public function toArray(): array
    {
        $collection = [];

        if (isset($this->email)) {
            $collection['email'] = $this->email;
        }
        if (isset($this->token)) {
            $collection['token'] = $this->token;
        }
        if (isset($this->createdAt)) {
            $collection['created_at'] = $this->createdAt;
        }
        if (isset($this->isDeleted)) {
            $collection['is_deleted'] = $this->isDeleted;
        }

        return $collection;
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
     * Get the value of token
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * Set the value of token
     */
    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get the value of createdAt
     */
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     */
    public function setCreatedAt(?string $createdAt): self
    {
        $this->createdAt = $createdAt;

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
