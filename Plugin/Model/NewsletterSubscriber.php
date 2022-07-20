<?php

namespace Maatoo\Maatoo\Plugin\Model;

use Maatoo\Maatoo\Model\Config\Config;
use Maatoo\Maatoo\Adapter\AdapterInterface;
use Maatoo\Maatoo\Model\StoreConfigManager;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Newsletter\Controller\Subscriber\NewAction;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Newsletter\Model\Subscriber;

class NewsletterSubscriber
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var StoreConfigManager $storeManager
     */
    private StoreConfigManager $storeManager;

    /**
     * @var AdapterInterface $adapter
     */
    private AdapterInterface $adapter;

    /**
     * @var CookieManagerInterface
     */
    private CookieManagerInterface $cookieManager;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Config $config,
        StoreConfigManager $storeManager,
        AdapterInterface $adapter,
        CookieManagerInterface $cookieManager)
    {
        $this->scopeConfig = $scopeConfig;
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->adapter = $adapter;
        $this->cookieManager = $cookieManager;
    }

    public function beforeSendConfirmationSuccessEmail(Subscriber $subject)
    {
        if ($this->config->isNewsletterConfirmationEmailDisabled()) {
            $subject->setImportMode(true);
        }
    }

    /**
     * @param NewAction $action
     * @param $result
     *
     * @return mixed
     */
    public function afterSubscribe(Subscriber $subject, $result, $email)
    {
        /** @var StoreInterface $store */
        foreach ($this->storeManager->getStores() as $store) {

            $leadId = $this->cookieManager->getCookie('mtc_id');
            $data = [
                'email' => $email,
            ];

            $data['tags'] = $this->storeManager->getTags($store);
            $this->adapter->makeRequest('contacts/' . $leadId . '/edit', $data, 'PATCH');
        }
        
        return $result;
    }

}