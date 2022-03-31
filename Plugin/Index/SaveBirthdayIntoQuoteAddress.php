<?php

namespace Maatoo\Maatoo\Plugin\Index;


use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Api\ShippingInformationManagementInterface;
use Magento\Quote\Model\Quote\Address;

class SaveBirthdayIntoQuoteAddress
{
    /**
     * Save birthday into billing and shipping addresses in quote_address table
     *
     * @param ShippingInformationManagementInterface $subject
     * @param $cartId
     * @param ShippingInformationInterface $addressInformation
     *
     * @return void
     */
    public function beforeSaveAddressInformation(ShippingInformationManagementInterface $subject, $cartId, ShippingInformationInterface $addressInformation): void
    {
        $shippingAddress = $addressInformation->getShippingAddress();
        $billingAddress = $addressInformation->getBillingAddress();
        $shippingAddressExtensionAttributes = $shippingAddress->getExtensionAttributes();

        if ($shippingAddressExtensionAttributes) {
            $shippingAddressBirthday = $shippingAddressExtensionAttributes->getBirthday();

            if ($shippingAddressBirthday) {
                $shippingAddress->setBirthday($shippingAddressBirthday);

                if ($billingAddress) {
                    $billingAddress->setBirthday($shippingAddressBirthday);
                }
            }
        }
    }
}
