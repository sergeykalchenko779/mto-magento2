<?php
declare(strict_types=1);

namespace Maatoo\Maatoo\Model\Client;

use Magento\Framework\Encryption\EncryptorInterface;
use Maatoo\Maatoo\MaatooApi;
use Maatoo\Maatoo\Auth\ApiAuth;

class ClientResolver
{
    /**
     * @var EncryptorInterface
     */
    private $encryptor;
    /**
     * @var \Maatoo\Maatoo\Model\Config\Config
     */
    private $config;

    public function __construct(
        EncryptorInterface $encryptor,
        \Maatoo\Maatoo\Model\Config\Config $config
    )
    {
        $this->encryptor = $encryptor;
        $this->config = $config;
    }

    public function validate($apiUsername, $apiPassword)
    {
        if ($apiUsername && $apiPassword) {
            $apiPassword = $this->encryptor->decrypt($apiPassword);
            $settings = array(
                'userName'   => $apiUsername,
                'password'   => $apiPassword
            );

            $initAuth    = new ApiAuth();
            $auth        = $initAuth->newAuth($settings, 'BasicAuth');
            $result      = $auth->isAuthorized();
            if(!$result) {
                return $result;
            }
            return $auth;
        }

        return false;
    }

    /**
     * @param $password
     * @return string
     */
    public function getPassword($password) {
        return $this->encryptor->decrypt($password);
    }
}
