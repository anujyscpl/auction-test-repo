<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\LoginUserRequest;
use App\Http\Requests\User\RegisterUserRequest;
use App\Repositories\CustomerRepository;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    use ResponseTrait;
    public CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param RegisterUserRequest $request
     * @return JsonResponse
     */
    public function register(RegisterUserRequest $request): JsonResponse
    {
        $input = $request->only(['first_name', 'last_name', 'phone_number', 'email', 'password', 'confirm_password', 'address', 'landmark', 'city', 'state', 'postcode', 'country']);


        if ($user = $this->customerRepository->createUser($input)) {

            return $this->responseSuccess($user);
        }

        return $this->responseError('Unable to create user');
    }


    /**
     * Create a new controller instance.
     *
     * @param LoginUserRequest $request
     * @return JsonResponse
     */

    public function login(LoginUserRequest $request): JsonResponse
    {

        $credentials = $request->only('email', 'password');

        if ($user = $this->customerRepository->validateCredentials($credentials)) {
            return $this->responseSuccess($user,'Logged in successfully');
        }

        return $this->responseError('Invalid credentials');
    }

}
