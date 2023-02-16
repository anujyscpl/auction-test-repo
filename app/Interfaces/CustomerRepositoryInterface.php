<?php

namespace App\Interfaces;


/**
 * interface CustomerRepositoryInterface.
 */
interface CustomerRepositoryInterface {

    public function createUser(array $data);

    public function validateCredentials(array $credentials);
}
