<?php

namespace App\Modules\V1\Dashboard\Bo\Summary;

class DetailsBo
{
    private ?string $range = null;

    public function toArray(): array
    {
        $collection = [];

        if (isset($this->range)) {
            $collection['range'] = $this->range;
        }

        return $collection;
    }

    /**
     * Get the value of range
     */
    public function getRange()
    {
        return $this->range;
    }

    /**
     * Set the value of range
     *
     * @return self
     */
    public function setRange($range)
    {
        $this->range = $range;

        return $this;
    }
}
