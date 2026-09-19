<?php

namespace App\Modules\V1\Invoice\Bo\Add;

class DetailsBo
{
    private ?int $clientId = null;

    private ?string $issueDate = null;

    private ?string $dueDate = null;

    private ?float $taxRate = null;

    private ?float $discountAmount = null;

    private ?string $notes = null;

    public function toArray(): array
    {
        $collection = [];

        if (isset($this->clientId)) {
            $collection['client_id'] = $this->clientId;
        }
        if (isset($this->issueDate)) {
            $collection['issue_date'] = $this->issueDate;
        }
        if (isset($this->dueDate)) {
            $collection['due_date'] = $this->dueDate;
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
     * Get the value of issueDate
     */
    public function getIssueDate(): ?string
    {
        return $this->issueDate;
    }

    /**
     * Set the value of issueDate
     */
    public function setIssueDate(?string $issueDate): self
    {
        $this->issueDate = $issueDate;

        return $this;
    }

    /**
     * Get the value of dueDate
     */
    public function getDueDate(): ?string
    {
        return $this->dueDate;
    }

    /**
     * Set the value of dueDate
     */
    public function setDueDate(?string $dueDate): self
    {
        $this->dueDate = $dueDate;

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
