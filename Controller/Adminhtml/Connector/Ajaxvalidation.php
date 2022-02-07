<?php
declare(strict_types=1);

namespace Maatoo\Maatoo\Controller\Adminhtml\Connector;

use Magento\Framework\Encryption\EncryptorInterface;
use Maatoo\Maatoo\Model\Client\ClientResolver;
use Maatoo\Maatoo\Auth\ApiAuth;

class Ajaxvalidation extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;
    /**
     * @var \Maatoo\Maatoo\Model\Config\Config
     */
    private $config;

    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @var ClientResolver
     */
    private $clientResolver;

    /**
     * @var \Maatoo\Maatoo\Adapter\AdapterInterface
     */
    private $adapter;

    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Maatoo\Maatoo\Model\Config\Config $config,
        EncryptorInterface $encryptor,
        ClientResolver $clientResolver,
        \Magento\Backend\App\Action\Context $context,
        \Maatoo\Maatoo\Adapter\AdapterInterface $adapter
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->config = $config;
        $this->clientResolver = $clientResolver;
        $this->adapter = $adapter;
        parent::__construct($context);
    }

    /**
     * Validate api user.
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $apiUsername = $params['api_username'];
        // @codingStandardsIgnoreLine
        $apiPassword = $this->config->getMaatooPassword();
        //validate api
        if ($this->config->isModuleEnable()) {
            $result = $this->clientResolver->validate($apiUsername, $apiPassword);

            $parameters = [];
            $result = $this->adapter->makeRequest('stores', $parameters, 'GET');
            $responseData['success'] = true;
            //validation failed
            if (!$result) {
                $responseData['success'] = false;
                $responseData['message'] = 'Authorization has been denied for this request.';
            }

            $this->getResponse()->representJson($this->jsonHelper->jsonEncode($responseData));
        }
    }
}
