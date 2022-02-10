<?php

namespace Maatoo\Maatoo\Plugin\Controller;

use Maatoo\Maatoo\Adapter\AdapterInterface;
use Maatoo\Maatoo\Model\StoreConfigManager;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Newsletter\Controller\Subscriber\NewAction;
use Magento\Store\Api\Data\StoreInterface;

class NewActionPlugin
{
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

    /**
     * @param StoreConfigManager $storeManager
     * @param AdapterInterface $adapter
     * @param CookieManagerInterface $cookieManager
     */
    public function __construct(
        StoreConfigManager $storeManager,
        AdapterInterface $adapter,
        CookieManagerInterface $cookieManager
    ) {
        $this->storeManager = $storeManager;
        $this->adapter = $adapter;
        $this->cookieManager = $cookieManager;
    }

    /**
     * @param NewAction $action
     * @param $result
     *
     * @return mixed
     */
    public function afterExecute(NewAction $action, $result)
    {
        $request = $action->getRequest();
        if ($request->isPost() && $request->getPost('email')) {
            $email = (string)$request->getPost('email');

            /** @var StoreInterface $store */
            foreach ($this->storeManager->getStores() as $store) {

                $leadId = $this->cookieManager->getCookie('mtc_id');
                $data = [
                    'email' => $email,
                ];

                $data['tags'] = $this->storeManager->getTags($store);
                $this->adapter->makeRequest('contacts/' . $leadId . '/edit', $data, 'PATCH');
            }
        }

        return $result;
    }
}
