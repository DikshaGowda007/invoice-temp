<?php

namespace App\Repositories\DAO\V1;

class RecurringInvoiceDAO
{
    private ?int $userId = null;

    private ?int $clientId = null;

    private ?string $frequency = null;

    private ?string $status = null;

    private ?string $nextRunAt = null;

    private ?string $lastGeneratedAt = null;

    private ?float $subtotal = null;

    private ?float $taxRate = null;

    private ?float $taxAmount = null;

    private ?float $discountAmount = null;

    private ?float $total = null;

    private ?string $notes = null;

    private ?int $isDeleted = null;

    public function toArray(): array
    {
        $collection = [];

        if (isset($this->userId)) {
            $collection['user_id'] = $this->userId;
        }
        if (isset($this->clientId)) {
            $collection['client_id'] = $this->clientId;
        }
        if (isset($this->frequency)) {
            $collection['frequency'] = $this->frequency;
        }
        if (isset($this->status)) {
            $collection['status'] = $this->status;
        }
        if (isset($this->nextRunAt)) {
            $collection['next_run_at'] = $this->nextRunAt;
        }
        if (isset($this->lastGeneratedAt)) {
            $collection['last_generated_at'] = $this->lastGeneratedAt;
        }
        if (isset($this->subtotal)) {
            $collection['subtotal'] = $this->subtotal;
        }
        if (isset($this->taxRate)) {
            $collection['tax_rate'] = $this->taxRate;
        }
        if (isset($this->taxAmount)) {
            $collection['tax_amount'] = $this->taxAmount;
        }
        if (isset($this->discountAmount)) {
            $collection['discount_amount'] = $this->discountAmount;
        }
        if (isset($this->total)) {
            $collection['total'] = $this->total;
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
     * Get the value of clientId
     */
    public function getClientId(): ?int
    {
        return $this->clientId;
    }

    /**
     * Set the value of clientId
     */
    public function setClientId(?int $clientId): self
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * Get the value of frequency
     */
    public function getFrequency(): ?string
    {
        return $this->frequency;
    }

    /**
     * Set the value of frequency
     */
    public function setFrequency(?string $frequency): self
    {
        $this->frequency = $frequency;

        return $this;
    }

    /**
     * Get the value of status
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Set the value of status
     */
    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the value of nextRunAt
     */
    public function getNextRunAt(): ?string
    {
        return $this->nextRunAt;
    }

    /**
     * Set the value of nextRunAt
     */
    public function setNextRunAt(?string $nextRunAt): self
    {
        $this->nextRunAt = $nextRunAt;

        return $this;
    }

    /**
     * Get the value of lastGeneratedAt
     */
    public function getLastGeneratedAt(): ?string
    {
        return $this->lastGeneratedAt;
    }

    /**
     * Set the value of lastGeneratedAt
     */
    public function setLastGeneratedAt(?string $lastGeneratedAt): self
    {
        $this->lastGeneratedAt = $lastGeneratedAt;

        return $this;
    }

    /**
     * Get the value of subtotal
     */
    public function getSubtotal(): ?float
    {
        return $this->subtotal;
    }

    /**
     * Set the value of subtotal
     */
    public function setSubtotal(?float $subtotal): self
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    /**
     * Get the value of taxRate
     */
    public function getTaxRate(): ?float
    {
        return $this->taxRate;
    }

    /**
     * Set the value of taxRate
     */
    public function setTaxRate(?float $taxRate): self
    {
        $this->taxRate = $taxRate;

        return $this;
    }

    /**
     * Get the value of taxAmount
     */
    public function getTaxAmount(): ?float
    {
        return $this->taxAmount;
    }

    /**
     * Set the value of taxAmount
     */
    public function setTaxAmount(?float $taxAmount): self
    {
        $this->taxAmount = $taxAmount;

        return $this;
    }

    /**
     * Get the value of discountAmount
     */
    public function getDiscountAmount(): ?float
    {
        return $this->discountAmount;
    }

    /**
     * Set the value of discountAmount
     */
    public function setDiscountAmount(?float $discountAmount): self
    {
        $this->discountAmount = $discountAmount;

        return $this;
    }

    /**
     * Get the value of total
     */
    public function getTotal(): ?float
    {
        return $this->total;
    }

    /**
     * Set the value of total
     */
    public function setTotal(?float $total): self
    {
        $this->total = $total;

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
