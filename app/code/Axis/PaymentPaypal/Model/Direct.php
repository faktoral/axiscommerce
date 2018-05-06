<?php
/**
 * Axis
 *
 * This file is part of Axis.
 *
 * Axis is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Axis is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Axis.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category    Axis
 * @package     Axis_PaymentPaypal
 * @subpackage  Axis_PaymentPaypal_Model
 * @copyright   Copyright 2008-2012 Axis
 * @license     GNU Public License V3.0
 */

/**
 *
 * @category    Axis
 * @package     Axis_PaymentPaypal
 * @subpackage  Axis_PaymentPaypal_Model
 * @author      Axis Core Team <core@axiscommerce.com>
 */
class Axis_PaymentPaypal_Model_Direct extends Axis_PaymentPaypal_Model_Abstract
{
    protected $_code = 'Paypal_Direct';
    protected $_title = 'PayPal Direct Payment';

    protected $_buttonSourceDP = 'Axis-DP_us';

    public function init()
    {
        parent::init();

        if ($this->_config->mode == 'Payflow-UK') {
            $this->_buttonSourceDP = 'Axis-DP_uk';
        }
        if ($this->_config->mode == 'Payflow-US') {
            $this->_buttonSourceDP = 'Axis-GW_us';
        }
    }

    public function postProcess(Axis_Sales_Model_Order_Row $order)
    {
        $this->saveCreditCard($order);

        $cc      = $this->getCreditCard();
        $options = $this->getLineItemDetails();
        $billing = $order->getBilling();

        $optionsAll = array_merge(
            $options,
            array(
                'STREET'       => $billing->getStreetAddress(),
                'ZIP'          => $billing->getPostcode(),
                'BUTTONSOURCE' => $this->_buttonSourceDP,
                'CURRENCY'     => $this->getBaseCurrencyCode(),
                'IPADDRESS'    => $_SERVER['REMOTE_ADDR']
            )
        );
        if ($cc->getCcIssueMonth() && $cc->getCcIssueYear()) {
            $optionsAll['CARDSTART'] = $cc->getCcIssueMonth() . substr($cc->getCcIssueYear(), -2);
        }

        $optionsNVP = array(
            'CITY'        => $billing->getCity(),
            'STATE'       => $billing->getZone()->getCode() ?
                $billing->getZone()->getCode() : $billing->getCity(),
            'COUNTRYCODE' => $billing->getCountry()->getIsoCode2(),
            'EXPDATE'     => $cc->getCcExpiresMonth() . $cc->getCcExpiresYear(),
            'PAYMENTACTION' => ($this->_config->paymentAction == 'Authorization') ? 'Authorization' : 'Sale'
        );

        $delivery = $order->getDelivery();
        $optionsShip = array(
            'SHIPTONAME'   => $delivery->getFirstname() . ' ' . $delivery->getLastname(),
            'SHIPTOSTREET' => $delivery->getStreetAddress(),
            'SHIPTOSTREET2'=> $delivery->getSuburb(),
            'SHIPTOCITY'   => $delivery->getCity(),
            'SHIPTOZIP'    => $delivery->getPostcode(),
            'SHIPTOSTATE'  => $delivery->getZone()->getCode() ?
                $delivery->getZone()->getCode() : $delivery->getCity(),
            'SHIPTOCOUNTRYCODE'=> $delivery->getCountry()->getIsoCode2()
        );
        // if these optional parameters are blank, remove them from transaction
        if (isset($optionsShip['SHIPTOSTREET2']) && empty($optionsShip['SHIPTOSTREET2'])) {
            unset($optionsShip['SHIPTOSTREET2']);
        }

        $response = $this->getApi()->DoDirectPayment(
            sprintf('%.2f', $this->getAmountInBaseCurrency($order->order_total)),
            $cc->getCcNumber(),
            $cc->getCcCvv(),
            $cc->getCcExpiresMonth() . $cc->getCcExpiresYear(),
            $billing->getFirstname(),
            $billing->getLastname(),
            $cc->getCcType(),
            $optionsAll,
            array_merge($optionsNVP, $optionsShip)
        );

        if ($response['ACK'] != 'Success') {
            $this->log(
                "Response : " . Zend_Debug::dump($response, null, false)
            );

            foreach ($this->getMessages($response) as $severity => $messages) {
                Axis::message()->batchAdd($messages, $severity);
            }

            throw new Axis_Exception('DoDirectPayment Failure');
        }
    }
}
