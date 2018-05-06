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
 * @package     Axis_Account
 * @copyright   Copyright 2008-2012 Axis
 * @license     GNU Public License V3.0
 */

$config = array(
    'Axis_Account' => array(
        'package'  => 'Axis_Account',
        'name'     => 'Account',
        'version'  => '0.1.6',
        'required' => 1,
        'events'   => array(
            'account_customer_register_success' => array(
                'notify_email' => array(
                    'type'   => 'model',
                    'model'  => 'account/observer',
                    'method' => 'notifyCustomerRegistration'
                ),
            ),
            'account_box_navigation_prepare' => array(
                'prepare_menu' => array(
                    'type'   => 'model',
                    'model'  => 'account/observer',
                    'method' => 'prepareAccountNavigationBox'
                )
            ),
            'admin_box_navigation_prepare' => array(
                'prepare_menu' => array(
                    'type'   => 'model',
                    'model'  => 'account/observer',
                    'method' => 'prepareAdminNavigationBox'
                )
            ),
            'checkout_place_order_after' => array(
                'create_account' => array(
                    'type'   => 'model',
                    'model'  => 'account/observer',
                    'method' => 'saveCustomerAfterPlaceOrder'
                )
            )
        )
    )
);
