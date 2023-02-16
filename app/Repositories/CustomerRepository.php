<?php

namespace App\Repositories;

use App\Interfaces\CustomerRepositoryInterface;
use App\Models\Address;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Webkul\Customer\Models\Customer;

class CustomerRepository implements CustomerRepositoryInterface
{
    /**
     * @param array $data
     * @return bool|array
     */
    public function createUser(array $data): bool|array
    {
        try {
            DB::beginTransaction();

            $user = Customer::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => isset($data['password']) ? password_hash($data['password'], PASSWORD_BCRYPT) : null,
                'is_verified' => 1,
                'phone' => $data['phone_number']
            ]);

            if (!$user) {
                DB::rollBack();
                return false;
            }

            $user_address = Address::create([
                'customer_id' => $user->id,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'address1' => $data['address'],
                'address2' => $data['landmark'],
                'city' => $data['city'],
                'state' => $data['state'],
                'postcode' => $data['postcode'],
                'country' => $data['country'],
                'email' => $data['email'],
                'phone' => $data['phone_number']
            ]);

            if (!$user_address) {
                DB::rollBack();
                return false;
            }

            DB::commit();
            return $user->toArray();

        }catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            return false;
        }
    }

    /**
     * @param array $credentials
     * @return bool|array
     */
    public function validateCredentials(array $credentials): bool|array
    {
        $user = Customer::where('email', $credentials['email'])->first();
        if (!$user) {
            return false;
        }

        $check = Hash::check($credentials['password'], $user->password);
        if (!$check) {
            return false;
        }

        $user->update(['api_token' => $user->createToken($credentials['password'])->plainTextToken,]);
        $user->save();

        return $user->toArray();
    }

}
