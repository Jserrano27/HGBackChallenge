<?php

namespace App\Traits;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

trait WebApiResponser {

    protected function errorResponse($message, $code) {
        
        $errorMessage = json_encode(["error" => $message, "code" => $code], JSON_PRETTY_PRINT);
        echo "<pre>";
        return $errorMessage;
    }

    protected function paginateResults($data) {
        
        $rules = [
            'per_page' => 'integer|min:2|max:100',
        ];

        Validator::validate(request()->all(), $rules);

        $perPage = 5;
        if(request()->has('per_page')) {
            $perPage = request()->per_page;
        }
        
        $total = count($data);
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $items = array_slice($data, ($currentPage - 1) * $perPage, $perPage);

        $paginated = new LengthAwarePaginator($items, $total, $perPage, $currentPage, [
           'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        $paginated->appends(request()->all());

        return $paginated;
    }

    protected function retrieveDataByName($client) {
        $queryParams = request()->query();
        $fullUrl = request()->fullUrlWithQuery($queryParams);

        $cachedData = Cache::remember($fullUrl, 60*24*30, function () use ($client){
            $response = $client->request('GET', '/v1/breeds/search', [
                'headers' => [
                'Accept' => 'application/json',
                'x-api-key' => 'bc4a0606-d00e-4dc0-b210-5b3e1bb5ca2a',
                ]]);

            $data = json_decode($response->getBody());

            if($response->getStatusCode()==200) {
                if($response->getHeader('content-length')[0] == 2) {
                    return $this->errorResponse('Not Found', 404);
                }
                $data = $this->paginateResults($data);
                $data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
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