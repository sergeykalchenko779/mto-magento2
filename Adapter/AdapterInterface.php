<?php

namespace Maatoo\Maatoo\Adapter;


interface AdapterInterface
{
    public function makeRequest(string $endpoint, array $parameters = [], $method = 'GET', array $settings = []);
}
