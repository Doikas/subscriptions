<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Subscription;
use App\Models\CustomerMapping;
use App\Models\ServiceMapping;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SubscriptionImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Extract subscription data from your source file
        $customerId = $row['customer_id'];
        $serviceId = $row['service_id'];
        $subsstartdate = $row['subs_startdate'];
        $subsexpdate = $row['subs_expdate'];
        $subsdomain = $row['subs_domain'];
        $subsprice = $row['subs_price'];
        $subsnotes = $row['subs_notes'];        // Map other columns as needed
        
        $mappingcust = CustomerMapping::where('old_customer_id', $customerId)->first();
        $newCustomerId = $mappingcust ? $mappingcust->new_customer_id : null;
        $mappingserv = ServiceMapping::where('old_service_id', $serviceId)->first();
        $newServiceId = $mappingserv ? $mappingserv->new_service_id : null;


        
        return new Subscription([
            'customer_id' => $newCustomerId, // Use the user ID from the mapping
            'service_id' => $newServiceId,
            'start_date' => $subsstartdate,
            'expired_date' => $subsexpdate,
            'domain' => $subsdomain,
            'price' => $subsprice,
            'notes' =>$subsnotes,
            // Map other columns as needed
        ]);
    }
}

