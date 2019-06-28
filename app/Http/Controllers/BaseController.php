<?php

namespace App\Http\Controllers;

use Dingo\Api\Routing\Helpers;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

// use GuzzleHttp\Message\Response;

class BaseController extends Controller
{

    use Helpers;

    public function dispatchResponse($statusCode = 200, $msg = "", $data = null)
    {
        $response = [];
        $response["status_code"] = $statusCode;
        $response["message"] = $msg;
        $response["data"] = $data;
//        $resource = new Item($data, $transformer);
        //        $resource = new Collection($data, $transformer);

//        $this->pp($resource->transform());
        //        die();
        //        $response["data"] = $resource->getData();

        return new Response($response, $statusCode);
    }

    public function pp($data)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }

    public function pv($data)
    {
        echo "<pre>";
        var_dump($data);
        echo "</pre>";
    }

    //Function to call other source API by GET method
    public function getOtherSourceResponce($url)
    {
        $client = new Client();

        $response = $client->get($url);

        $body = (string) $response->getBody();

        $trimmedBody = preg_replace(
            '/
              ^
              [\pZ\p{Cc}\x{feff}]+
              |
              [\pZ\p{Cc}\x{feff}]+$
             /ux',
            '',
            $body
        );
        // Decode the response
        $responseData = json_decode($trimmedBody, true);

        if (isset($responseData["data"])) {
            $data = $responseData["data"];
        } else {
            $data = null;
        }
        return $data;
    }

}
