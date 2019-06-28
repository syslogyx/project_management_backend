<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

class ProjectDocumentTransformer extends TransformerAbstract {

    public function transform(\App\ProjectDocument $model) {
        return [
            'id' => $model->id,
            'file_name' => $model->file_name,       
        ];
    }

}
