<?php

namespace PI\Employee\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class CORSResponse extends JsonResponse
{

    /**
     * CORSResponse constructor.
     * @param null $data
     * @param int $status
     */
    public function __construct($data = null, int $status = 200)
    {
        $headers = [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'DNT, X-User-Token, Keep-Alive, User-Agent, X-Requested-With, If-Modified-Since, Cache-Control, Content-Type',
            'Access-Control-Max-Age' => 1728000,
        ];

        parent::__construct($data, $status, $headers, false);
    }

}
