<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait WebApiResponser {

    protected function errorResponse($message, $code) {
        
        $errorMessage = json_encode(["error" => $message, "code" => $code], JSON_PRETTY_PRINT);
        echo "<pre>";
        return $errorMessage;
    }

    protected function retrieveDataByName($breedName, $client) {

        $cachedData = Cache::remember("BreedName.{$breedName}", 60*24*30, function () use ($client){
            $response = $client->request('GET', '/v1/breeds/search', [
                'headers' => [
                'Accept' => 'application/json',
                'x-api-key' => 'bc4a0606-d00e-4dc0-b210-5b3e1bb5ca2a',
                ]]);

            $data = json_decode($response->getBody());

            $data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            if($response->getStatusCode()==200) {
                if($response->getHeader('content-length')[0] == 2) {
                    return $this->errorResponse('Not Found', 404);
                }
                echo "<pre>";
                return $data;
            }

            return $this->errorResponse('Unexpected error. Please, try again later', 500);
        });

        echo "<pre>";
        return $cachedData;
    }

    protected function retrieveDataById($id, $client) {

        $cachedData = Cache::remember("BreedId.{$id}", 60*24*30, function () use ($id, $client){

            $response = $client->request('GET', '/v1/images/search', [
                'headers' => [
                    'Accept' => 'application/json',
                    'x-api-key' => 'bc4a0606-d00e-4dc0-b210-5b3e1bb5ca2a',
                ]]);

            $data = json_decode($response->getBody());

            $data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            if($response->getStatusCode()==200) {
                if($response->getHeader('content-length')[0] == 2 || strlen($id) != 4) {
                    return $this->errorResponse('Not Found', 404);
                }
                echo "<pre>";
                return $data;
            }

            return $this->errorResponse('Unexpected error. Please, try again later', 500);
        });

        echo "<pre>";
        return $cachedData; 
    }
}