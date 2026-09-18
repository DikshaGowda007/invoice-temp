<?php

namespace App\Modules\V1\Client\Bo\Add;

class DetailsBo
{
    private ?string $name = null;

    private ?string $email = null;

    private ?string $phone = null;

    private ?string $address = null;

    private ?string $notes = null;

    public function toArray(): array
    {
        $collection = [];

        if (isset($this->name)) {
            $collection['name'] = $this->name;
        }
        if (isset($this->email)) {
            $collection['email'] = $this->email;
        }
        if (isset($this->phone)) {
            $collection['phone'] = $this->phone;
        }
        if (isset($this->address)) {
            $collection['address'] = $this->address;
        }
        if (isset($this->notes)) {
            $collection['notes'] = $this->notes;
        }

        return $collection;
    }

    /**
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of phone
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set the value of phone
     *
     * @return self
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get the value of address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set the value of address
     *
     * @return self
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get the value of notes
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set the value of notes
     *
     * @return self
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }
}
