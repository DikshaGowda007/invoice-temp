<?php

namespace App\Modules\V1\RecurringInvoice\Bo\Edit;

class DetailsBo
{
    private ?int $id = null;

    private ?int $clientId = null;

    private ?string $frequency = null;

    private ?string $nextRunAt = null;

    private ?float $taxRate = null;

    private ?float $discountAmount = null;

    private ?string $notes = null;

    public function toArray(): array
    {
        $collection = [];

        if (isset($this->clientId)) {
            $collection['client_id'] = $this->clientId;
        }
        if (isset($this->frequency)) {
            $collection['frequency'] = $this->frequency;
        }
        if (isset($this->nextRunAt)) {
            $collection['next_run_at'] = $this->nextRunAt;
        }
        if (isset($this->taxRate)) {
            $collection['tax_rate'] = $this->taxRate;
        }
        if (isset($this->discountAmount)) {
            $collection['discount_amount'] = $this->discountAmount;
        }
        if (isset($this->notes)) {
            $collection['notes'] = $this->notes;
        }

        return $collection;
    }

    /**
     * Get the value of id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set the value of id
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

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
}
