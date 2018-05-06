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
 * @package     Axis_Admin
 * @subpackage  Axis_Admin_Model
 * @copyright   Copyright 2008-2012 Axis
 * @license     GNU Public License V3.0
 */

/**
 * 
 * @category    Axis
 * @package     Axis_Admin
 * @subpackage  Axis_Admin_Model
 * @author      Axis Core Team <core@axiscommerce.com>
 */
class Axis_Admin_Model_Acl_Rule extends Axis_Db_Table
{
    protected $_name = 'admin_acl_rule';

    /**
     *
     * @param string $oldResource
     * @param string $newResource
     * @return Axis_Admin_Model_Acl_Rule 
     */
    public function rename($oldResource, $newResource)
    {
        $rowset = $this->select()
           ->where('resource_id = ?', $oldResource)
           ->fetchRowset();

        foreach ($rowset as $row) {
           $row->resource_id = $newResource;
           $row->save();
        }

        return $this;
   }
}