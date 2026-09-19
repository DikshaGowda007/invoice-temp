<?php

namespace App\Modules\V1\Invoice\Bo\LineItem\Add;

class DetailsBo
{
    private ?int $invoiceId = null;

    private ?string $description = null;

    private ?float $quantity = null;

    private ?float $unitPrice = null;

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
}
