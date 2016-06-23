<?php

namespace App\Http\Controllers;

use App\Api\QuintypeClient;
use App\Http\Controllers\Controller;

class QuintypeController extends Controller
{
    public function __construct()
    {
        $this->client = new QuintypeClient(config("quintype.api-host"));
    }

    public function toView($args) {
        return array_merge([
            "config" => $this->client->config()
        ], $args);
    }
}