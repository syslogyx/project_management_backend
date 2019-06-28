<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

class DomainTransformer extends TransformerAbstract {

    public function transform(\App\Domain $domain) {
        return [
            'id' => $domain->id,
            'name' => $domain->name,
            'alias' => $domain->alias,
        ];
    }
}
