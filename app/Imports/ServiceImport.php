<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Service;
use App\Models\ServiceMapping;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ServiceImport implements ToModel, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        // Extract user data from your source file
        $oldServiceId = $row['old_service_id'];
        $servname = $row['serv_name'];
        $servslug = $row['serv_slug'];
        $servdescri = $row['serv_descri'];
        $servexpir = $row['serv_expir'];
        
        // Find the new user ID based on the mapping (if created)
        $mapping = ServiceMapping::where('old_service_id', $oldServiceId)->first();
        $newServiceId = $mapping ? $mapping->new_service_id : null;

        $service = new Service([
            'name' => $servname,
            'slug' => $servslug,
            'description' => $servdescri,
            'expiration' => $servexpir,
        ]);
        $service->save();
        $servicenewid= $service->id;
        if (!$mapping) {
            // Create a new mapping record
            $mapping = new ServiceMapping();
            $mapping->old_service_id = $oldServiceId;
            $mapping->new_service_id = $servicenewid;
            $mapping->save();
        }

        // Retrieve the new customer ID (if it exists)
        // If a new customer ID is available, assign it


        return $service;
        
    
    }
}
