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
 * @package     Axis_Discount
 * @subpackage  Axis_Discount_Admin_Controller
 * @copyright   Copyright 2008-2012 Axis
 * @license     GNU Public License V3.0
 */

/**
 *
 * @category    Axis
 * @package     Axis_Discount
 * @subpackage  Axis_Discount_Admin_Controller
 * @author      Axis Core Team <core@axiscommerce.com>
 */
class Axis_Discount_Admin_IndexController extends Axis_Admin_Controller_Back
{
    public function indexAction()
    {
        $this->view->pageTitle = Axis::translate('discount')->__(
            'Discounts'
        );
        $this->render();
    }

    public function listAction()
    {
        $filter = $this->_getParam('filter', array());
        $limit  = $this->_getParam('limit', 25); 
        $start  = $this->_getParam('start', 0);
        $order  = $this->_getParam('sort', 'id') . ' '
                . $this->_getParam('dir', 'DESC');
        
        $select = Axis::model('discount/discount')->select('*')
            ->calcFoundRows()
            ->addFilters($filter)
            ->limit($limit, $start)
            ->order($order);

        $displayMode = $this->_getParam('displayMode', 'without-special');
        if ('only-special' == $displayMode) {
            $select->addSpecialFilter();
        } else if ('without-special' == $displayMode) {
            $select->addSpecialFilter(false);
        }

        return $this->_helper->json
            ->setData($select->fetchAll())
            ->setCount($select->foundRows())
            ->sendSuccess()
        ;
    }
  
    public function loadAction()
    {
        $discountId = $this->_getParam('id');
        
        $discount = Axis::single('discount/discount')
            ->find($discountId)
            ->current();

        $data = array(
            'discount' => $discount->toArray(),
            'rule'     => $discount->getRule()
        );
        
        return $this->_helper->json
            ->setData($data)
            ->sendSuccess()
        ;
    }
    
    public function saveAction()
    {
        $_row = $this->_getParam('discount', array());
 
//        $row = Axis::single('discount/discount')->save($_row);
        
        $model = Axis::single('discount/discount');
        $row = false;
        if (isset($_row['id'])) {
            $row = $model->find($_row['id'])->current();
        }
        
        if (!$row) {
            $oldData = null;
            $row = $model->createRow();
        } else {
            $oldData = $row->toArray();
            $oldData['products'] = $row->getApplicableProducts();
        }
        
        $row->setFromArray($_row);
        
        if (empty($row->from_date)) {
            $row->from_date = new Zend_Db_Expr('NULL');
        }
        if (empty($row->to_date)) {
            $row->to_date = new Zend_Db_Expr('NULL');
        }
        if (empty($row->priority)) {
            $row->priority = 125;
        }
        $row->save();
        
        $model = Axis::model('discount/eav');
        $model->delete('discount_id = ' . $row->id);
        
        $dataset = Zend_Json::decode($this->_getParam('rule'));
        foreach ($dataset as $entity => $values) {
            foreach ($values as $value) {
                $model->createRow(array(
                    'discount_id' => $row->id,
                    'entity'      => $entity,
                    'value'       => $value
                ))->save();
            }
        }
        
        Axis::dispatch('discount_save_after', array(
            'old_data' => $oldData,
            'discount' => $row
        ));
        
        Axis::message()->addSuccess(
            Axis::translate('discount')->__(
                "Discount '%s' successefull saved", $row->name
            )
        );

        return $this->_helper->json
            ->setId($row->id)
            ->sendSuccess()
        ;
    }

    public function removeAction()
    {
        $ids = Zend_Json::decode($this->_getParam('data'));
        $model = Axis::model('discount/discount');
        //@todo move event dispatch to delete 
        $discounts = $model->find($ids);
        
        foreach ($discounts as $discount) {
            $discountData = $discount->toArray();
            $discountData['products'] = $discount->getApplicableProducts();
            
            $discount->delete();

            Axis::dispatch('discount_delete_after', array(
                'discount_data' => $discountData
            ));
        }

        return $this->_helper->json->sendSuccess();
    }
}