<?php

namespace Maatoo\Maatoo\Model\Checkout;

class ConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
    private $configMaatoo;

    public function __construct(
        \Maatoo\Maatoo\Model\Config\Config $config
    ) {
        $this->configMaatoo = $config;
    }

    public function getConfig()
    {
        $config['maatoo']['opt_in'] = $this->configMaatoo->getOptIn();
        $config['maatoo']['opt_in_text'] = $this->configMaatoo->getOptInText();
        return $config;
    }

}
