<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

class ProjectPocTransformer extends TransformerAbstract {

    public function transform(\App\ProjectPoc $type) {
        return [
            'id' => $type->id,
            'name' => $type->name,
            'mobile_primary' => $type->mobile_primary,
            'mobile_secondary' => $type->mobile_secondary,
            'email_personal' => $type->email_personal,
            'email_official' => $type->email_official,
            'address' => $type->address,
            'project' => $type->project
        ];
    }

}


