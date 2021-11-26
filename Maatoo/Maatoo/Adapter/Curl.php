<?php

namespace Maatoo\Maatoo\Adapter;


class Curl implements AdapterInterface
{
    private $config;

    private $auth;

    private $clientResolver;

    private $logger;

    public function __construct(
        \Maatoo\Maatoo\Model\Config\Config $config,
        \Maatoo\Maatoo\Auth\BasicAuth $auth,
        \Maatoo\Maatoo\Model\Client\ClientResolver $clientResolver,
        \Maatoo\Maatoo\Logger\Logger $logger
    )
    {
        $this->config = $config;
        $this->auth = $auth;
        $this->clientResolver = $clientResolver;
        $this->logger = $logger;
    }

    private function getUrl($endpoint)
    {
        $url = $this->config->getMaatooUrl() . 'api/';
        return $url . $endpoint;
    }

    public function makeRequest(string $endpoint, array $parameters = [], $method = 'GET', array $settings = [])
    {
        $url = $this->getUrl($endpoint);
        $username = $this->config->getMaatooUser();
        $password = $this->clientResolver->getPassword($this->config->getMaatooPassword());
        $this->auth->setup($username, $password);
        $this->logger->info('url: ' . $url);
        $this->logger->info('parameters: ' . json_encode($parameters));
        $result = [];
        try {
            $result = $this->auth->makeRequest($url, $parameters, $method, $settings);
            $this->logger->info('result: ' . json_encode($result));
        } catch (\Exception $e) {
            $this->logger->info('error: ' . $e->getMessage());
        }
        return $result;
    }
}
