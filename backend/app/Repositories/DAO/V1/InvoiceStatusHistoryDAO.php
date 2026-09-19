<?php

namespace App\Repositories\DAO\V1;

class InvoiceStatusHistoryDAO
{
    private ?int $invoiceId = null;

    private ?string $previousStatus = null;

    private ?string $newStatus = null;

    private ?int $changedBy = null;

    private ?string $notes = null;

    public function toArray(): array
    {
        $collection = [];

        if (isset($this->invoiceId)) {
            $collection['invoice_id'] = $this->invoiceId;
        }
        if (isset($this->previousStatus)) {
            $collection['previous_status'] = $this->previousStatus;
        }
        if (isset($this->newStatus)) {
            $collection['new_status'] = $this->newStatus;
        }
        if (isset($this->changedBy)) {
            $collection['changed_by'] = $this->changedBy;
        }
        if (isset($this->notes)) {
            $collection['notes'] = $this->notes;
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
     * Get the value of previousStatus
     */
    public function getPreviousStatus(): ?string
    {
        return $this->previousStatus;
    }

    /**
     * Set the value of previousStatus
     */
    public function setPreviousStatus(?string $previousStatus): self
    {
        $this->previousStatus = $previousStatus;

        return $this;
    }

    /**
     * Get the value of newStatus
     */
    public function getNewStatus(): ?string
    {
        return $this->newStatus;
    }

    /**
     * Set the value of newStatus
     */
    public function setNewStatus(?string $newStatus): self
    {
        $this->newStatus = $newStatus;

        return $this;
    }

    /**
     * Get the value of changedBy
     */
    public function getChangedBy(): ?int
    {
        return $this->changedBy;
    }

    /**
     * Set the value of changedBy
     */
    public function setChangedBy(?int $changedBy): self
    {
        $this->changedBy = $changedBy;

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
