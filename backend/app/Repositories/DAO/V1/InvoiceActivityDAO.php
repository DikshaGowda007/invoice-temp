<?php

namespace App\Repositories\DAO\V1;

class InvoiceActivityDAO
{
    private ?int $invoiceId = null;

    private ?int $userId = null;

    private ?string $type = null;

    private ?string $description = null;

    public function toArray(): array
    {
        $collection = [];

        if (isset($this->invoiceId)) {
            $collection['invoice_id'] = $this->invoiceId;
        }
        if (isset($this->userId)) {
            $collection['user_id'] = $this->userId;
        }
        if (isset($this->type)) {
            $collection['type'] = $this->type;
        }
        if (isset($this->description)) {
            $collection['description'] = $this->description;
        }

        return $collection;
    }

    /**
     * Get the value of invoiceId
     */
    public function getInvoiceId(): ?int
    {
        return $this->invoiceId;
    }

    /**
     * Set the value of invoiceId
     */
    public function setInvoiceId(?int $invoiceId): self
    {
        $this->invoiceId = $invoiceId;

        return $this;
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
     * Get the value of type
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Set the value of type
     */
    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the value of description
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the value of description
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
