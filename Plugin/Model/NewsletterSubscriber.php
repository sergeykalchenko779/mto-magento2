<?php

namespace Maatoo\Maatoo\Plugin\Model;

use Maatoo\Maatoo\Model\Config\Config;

class NewsletterSubscriber
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Maatoo\Maatoo\Model\Config\Config $config)
    {
        $this->scopeConfig = $scopeConfig;
        $this->config = $config;
    }

    public function beforeSendConfirmationSuccessEmail(\Magento\Newsletter\Model\Subscriber $subject)
    {
        //$url = $this->scopeConfig->getValue(self::MAATOO_URL_PATH, $this->scopeStore);
        if ($this->config->isNewsletterConfirmationEmailEnabled()) {
            $subject->setImportMode(true);
        }
    }
}