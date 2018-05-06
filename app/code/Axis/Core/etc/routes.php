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
 * @package     Axis_Core
 * @copyright   Copyright 2008-2012 Axis
 * @license     GNU Public License V3.0
 */

$router->addRoute('core', new Axis_Controller_Router_Route_Front(
    ':controller/:action/*',
    array(
        'module'     => 'Axis_Core',
        'controller' => 'index',
        'action'     => 'index'
    )
));

$router->addRoute('admin/axis/core', new Axis_Controller_Router_Route_Back(
    'core/:controller/:action/*',
    array(
        'module'     => 'Axis_Core',
        'controller' => 'index',
        'action'     => 'index'
    )
), 'admin/axis/admin');

$router->addRoute('sandbox', new Axis_Controller_Router_Route_Front(
    'sandbox',
    array(
        'module'     => 'Axis_Core',
        'controller' => 'sandbox',
        'action'     => 'index'
    )
));
