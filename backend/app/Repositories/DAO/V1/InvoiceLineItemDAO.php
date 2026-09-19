<?php

namespace App\Repositories\DAO\V1;

class InvoiceLineItemDAO
{
    private ?int $invoiceId = null;

    private ?string $description = null;

    private ?float $quantity = null;

    private ?float $unitPrice = null;

    private ?float $lineTotal = null;

    private ?int $isDeleted = null;

    public function toArray(): array
    {
        $collection = [];

        if (isset($this->invoiceId)) {
            $collection['invoice_id'] = $this->invoiceId;
        }
        if (isset($this->description)) {
            $collection['description'] = $this->description;
        }
        if (isset($this->quantity)) {
            $collection['quantity'] = $this->quantity;
        }
        if (isset($this->unitPrice)) {
            $collection['unit_price'] = $this->unitPrice;
        }
        if (isset($this->lineTotal)) {
            $collection['line_total'] = $this->lineTotal;
        }
        if (isset($this->isDeleted)) {
            $collection['is_deleted'] = $this->isDeleted;
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

    /**
     * Get the value of quantity
     */
    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    /**
     * Set the value of quantity
     */
    public function setQuantity(?float $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get the value of unitPrice
     */
    public function getUnitPrice(): ?float
    {
        return $this->unitPrice;
    }

    /**
     * Set the value of unitPrice
     */
    public function setUnitPrice(?float $unitPrice): self
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    /**
     * Get the value of lineTotal
     */
    public function getLineTotal(): ?float
    {
        return $this->lineTotal;
    }

    /**
     * Set the value of lineTotal
     */
    public function setLineTotal(?float $lineTotal): self
    {
        $this->lineTotal = $lineTotal;

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
