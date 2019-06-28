<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

class ClientTransformer extends TransformerAbstract {

    public function transform(\App\Client $client) {
        return [
            'id' => $client->id,
            'name' => $client->name,
            'project_id' => $client->project_id,
            'address' => $client->address,
            'city' => $client->city,
            'pincode' => $client->pincode,
            'state' => $client->state,
            'country' => $client->country,
            'mobile' => $client->mobile,
            'tel_number' => $client->tel_number,
            'email' => $client->email,
            'business' => $client->business,
            'type' => $client->type,
            'project' => $client->project
        ];
    }

}
