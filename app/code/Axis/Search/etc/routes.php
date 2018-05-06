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
 * @package     Axis_Search
 * @copyright   Copyright 2008-2012 Axis
 * @license     GNU Public License V3.0
 */

$router->addRoute('search', new Axis_Controller_Router_Route_Front(
    'search',
    array(
        'module'     => 'Axis_Search',
        'controller' => 'index',
        'action'     => 'index'
    )
));

$router->addRoute('search_result', new Axis_Controller_Router_Route_Front(
    'search/result/*',
    array(
        'module'     => 'Axis_Search',
        'controller' => 'index',
        'action'     => 'result'
    )
));

$router->addRoute('admin/axis/search', new Axis_Controller_Router_Route_Back(
    'search/:action/*',
    array(
        'module'     => 'Axis_Search',
        'controller' => 'index',
        'action'     => 'index'
    )
), 'admin/axis/admin');
