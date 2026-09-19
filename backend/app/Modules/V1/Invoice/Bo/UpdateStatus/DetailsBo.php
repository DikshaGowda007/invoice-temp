<?php

namespace App\Modules\V1\Invoice\Bo\UpdateStatus;

class DetailsBo
{
    private ?int $id = null;

    private ?string $status = null;

    private ?string $notes = null;

    public function toArray(): array
    {
        $collection = [];

        if (isset($this->status)) {
            $collection['status'] = $this->status;
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
