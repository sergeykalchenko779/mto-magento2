<?php
declare(strict_types=1);

namespace Maatoo\Maatoo\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
//use Magento\Framework\View\Helper\SecureHtmlRenderer;

class Button extends Field
{
    /**
     * Template path
     *
     * @var string
     */
    protected $_template = 'Maatoo_Maatoo::system/config/testconnection.phtml';

    public function __construct(
        Context $context,
        array $data = [], $secureRenderer = null
    )
    {
        parent::__construct($context, $data);
    }

    /**
     * Set template to itself
     *
     * @return $this
     * @since 100.1.0
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        return $this;
    }

    /**
     * Unset some non-related element parameters
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @since 100.1.0
     */
    public function render(AbstractElement $element)
    {
        $element = clone $element;
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Get the button and scripts contents
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @since 100.1.0
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $this->addData(
            [
                'button_label' => __($originalData['button_label']),
                'html_id' => $element->getHtmlId(),
                'ajax_url' => $this->_urlBuilder->getUrl('maatoo_maatoo/connector/testconnection'),
                'field_mapping' => str_replace('"', '\\"', json_encode($this->_getFieldMapping()))
            ]
        );
        return $this->_toHtml();
    }

    public function getAjaxUrl()
    {
        return $this->getUrl('maatoo_maatoo/connector/ajaxvalidation');
    }

    public function getButtonHtml()
    {
        $dataBtn = ['id' => 'test_connect', 'label' => __('Test connection'),];
        $button = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')->setData($dataBtn);
        return $button->toHtml();
    }

    /**
     * Returns configuration fields required to perform the ping request
     *
     * @return array
     * @since 100.1.0
     */
    protected function _getFieldMapping()
    {
        $_fieldMapping = [
            'active' => 'maatoo_general_active',
            'api_url' => 'maatoo_general_url',
            'api_username' => 'maatoo_general_user',
            'api_password' => 'maatoo_general_password'
        ];
        return $_fieldMapping;
    }
}
