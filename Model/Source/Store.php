<?php

namespace Maatoo\Maatoo\Model\Source;

class Store implements \Magento\Shipping\Model\Carrier\Source\GenericInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    private $request;

    /**
     * StoreConfigManager constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Request\Http $request
    )
    {
        $this->storeManager = $storeManager;
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];

        foreach ($this->storeManager->getStores(false) as $store) {
            if($this->request->get('website') == $store->getWebsiteId()) {
                $options[] =
                    [
                        'value' => $store->getId(),
                        'label' => $store->getName()
                    ];
            }
        }

        return $options;
    }
}
