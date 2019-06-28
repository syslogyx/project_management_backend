<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Http\Transformers\ProjectDocumentTransformer;
use App\ProjectDocument;
use Illuminate\Http\Request;

class ProjectDocumentController extends BaseController
{

    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {

            $file = $request->file('file');

            $path = $request->file->store('uploads/' . date('Y') . '/' . date('m'));

            $posted_data["title"] = $request->input("title");
            $posted_data["file_name"] = $path;
            $posted_data["path"] = $path;
            $posted_data["description"] = $request->input("description");
            $posted_data["type"] = $request->input("type");
            $posted_data["project_id"] = $request->input("project_id");
            $posted_data["created_by"] = 1;
            $posted_data["updated_by"] = 1;

            $projectDocument = new ProjectDocument();

            if ($projectDocument->validate($posted_data)) {
                $model = ProjectDocument::create($posted_data);
                return $this->response->item($model, new ProjectDocumentTransformer())->setStatusCode(200);
            } else {
                throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to add project document.', $projectDocument->errors());
            }
        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to upload file, File is missing!');
        }
    }

}
