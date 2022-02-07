<?php
declare(strict_types=1);

namespace Maatoo\Maatoo\Block;

use Magento\Framework\View\Element\Template;
use Maatoo\Maatoo\Model\Config\Config;

class Tracked extends Template
{
    /**
     * @var Config
     */
    private $configMto;

    public function __construct(
        Config $configMto,
        Template\Context $context,
        array $data = []
    )
    {
        $this->configMto = $configMto;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getConversionJsSrc()
    {
        return $this->configMto->getMaatooUrl();
    }
}
