<?php

namespace App\Modules\Auth\Register\Bo;

class RegisterBo
{
    private ?string $firstName = null;

    private ?string $lastName = null;

    private ?string $email = null;

    private ?string $password = null;

    public function toArray(): array
    {
        $collection = [];

        if (isset($this->firstName)) {
            $collection['first_name'] = $this->firstName;
        }
        if (isset($this->lastName)) {
            $collection['last_name'] = $this->lastName;
        }
        if (isset($this->email)) {
            $collection['email'] = $this->email;
        }
        if (isset($this->password)) {
            $collection['password'] = $this->password;
        }

        return $collection;
    }

    /**
     * Get the value of firstName
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set the value of firstName
     *
     * @return self
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get the value of lastName
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set the value of lastName
     *
     * @return self
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

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
     * Get the value of password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return self
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }
}
