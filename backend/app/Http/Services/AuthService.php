<?php

namespace App\Http\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AuthService
{
    private int $userId;

    private string $firstName;

    private string $lastName;

    private string $email;

    public function __construct(Request $request)
    {
        $this->initializeUserData($request);
    }

    private function initializeUserData(Request $request): void
    {
        $jwtUser = collect($request->attributes->get('jwtUser'));

        $this->userId = $jwtUser->get('loggedin_user_id');
        $this->firstName = $jwtUser->get('loggedin_user_first_name');
        $this->lastName = $jwtUser->get('loggedin_user_last_name');
        $this->email = $jwtUser->get('loggedin_user_email');
    }

    public function getData(): Collection
    {
        $fields = collect([]);

        if (isset($this->userId)) {
            $fields->put('userId', $this->userId);
        }
        if (isset($this->firstName)) {
            $fields->put('firstName', $this->firstName);
        }
        if (isset($this->lastName)) {
            $fields->put('lastName', $this->lastName);
        }
        if (isset($this->email)) {
            $fields->put('email', $this->email);
        }

        return $fields;
    }
}
