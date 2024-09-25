<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Customer;
use App\Models\CustomerMapping; // Mapping model if you created it
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\Log;

class CustomerImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Extract user data from your source file
        $oldCustomerId = $row['old_customer_id'];
        $firstname = $row['firstname'];
        $lastname = $row['lastname'];
        $email = $row['email'];
        $pronunciation = $row['pronunciation'];
        
        // Find the new user ID based on the mapping (if created)
        $mapping = CustomerMapping::where('old_customer_id', $oldCustomerId)->first();
        $newCustomerId = $mapping ? $mapping->new_customer_id : null;
        
        $customer = new Customer([
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'pronunciation' => $pronunciation,
            // Map other columns as needed
        ]);
        $customer->save();
        $customernewid = $customer->id;
        if (!$mapping) {
            // Create a new mapping record
            $mapping = new CustomerMapping();
            $mapping->old_customer_id = $oldCustomerId;
            $mapping->new_customer_id = $customernewid;
            $mapping->save();
        }

        // Retrieve the new customer ID (if it exists)
        // If a new customer ID is available, assign it


        return $customer;
    }
}
