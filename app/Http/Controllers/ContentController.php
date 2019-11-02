<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;


class ContentController extends WebApiController
{
    public function getContentByName() {

        if (empty(request()->all())){
            return $this->errorResponse('Bad Request', 400);
        }
        if(!request()->has('name')){
            return $this->errorResponse('Validation Failed', 422);
        }
        $breedName = request()->name;
        
        $client= new Client([
            'base_uri' => 'https://api.thecatapi.com',
            'query' => ['q' => isset($breedName) ? $breedName : $breedName=null],
        ]);

        return $this->retrieveDataByName($client);
    }
    

    public function getContentById($id){

        $client= new Client([
           'base_uri' => 'https://api.thecatapi.com',
           'query' => ['breed_ids' => $id],
        ]);

        return $this->retrieveDataById($id, $client);
   }
}
