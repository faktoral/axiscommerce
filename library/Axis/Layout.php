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
 * @package     Axis_Layout
 * @copyright   Copyright 2008-2012 Axis
 * @license     GNU Public License V3.0
 */

/**
 *
 * @category    Axis
 * @package     Axis_Layout
 * @author      Axis Core Team <core@axiscommerce.com>
 */
class Axis_Layout extends Zend_Layout
{
    const DEFAULT_LAYOUT   = 'default_3columns';

    /**
     * Blocks assignment
     *
     * @var array
     */
    protected $_assignments;

    /**
     *
     * @var string
     */
    protected $_layout = null;

    /**
     * Static method for initialization with MVC support
     *
     * @param  string|array|Zend_Config $options
     * @return Zend_Layout
     */
    public static function startMvc($options = null)
    {
        if (null === self::$_mvcInstance) {
            self::$_mvcInstance = new self($options, true);
        }

        if (is_string($options)) {
            self::$_mvcInstance->setLayoutPath($options);
        } elseif (is_array($options) || $options instanceof Zend_Config) {
            self::$_mvcInstance->setOptions($options);
        }

        return self::$_mvcInstance;
    }

    /**
     * @param array $assignments
     * @return Axis_Layout
     */
    public function setAssigments(array $assignments)
    {
        $this->_assignments = $assignments;
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getBlocks($container)
    {
        return isset($this->_assignments[$container]) ?
            $this->_assignments[$container] : array();
    }

    public function __get($key)
    {
        if (Axis_Area::isBackend()) {
            return parent::__get($key);
        }
        $before = $after = '';
        foreach ($this->getBlocks($key) as $blockId => $_config) {
            $blockContent = $this->_getBlockContent($_config);
            if ($_config['sort_order'] < 0) {
                $before .= $blockContent;
            } else {
                $after .= $blockContent;
            }
        }
        return $before . parent::__get($key) . $after;
    }

    private function _getBlockContent(array $conf)
    {
        if (!$conf['box_show']) {
            return '';
        }

        $block = $this->getView()->box(
            "{$conf['box_namespace']}_{$conf['box_module']}/{$conf['box_name']}",
            $conf
        );

        if ($block) {
            return $block->render();
        }
        return '';
    }

    /**
     * Render layout
     *
     * Sets internal script path as last path on script path stack, assigns
     * layout variables to view, determines layout name using inflector, and
     * renders layout view script.
     *
     * $name will be passed to the inflector as the key 'script'.
     *
     * @param  mixed $name
     * @return mixed
     */
    public function render($name = null)
    {
        if (null === $name) {
            $name = $this->getLayout();
        }

        if ($this->inflectorEnabled() && (null !== ($inflector = $this->getInflector())))
        {
            $name = $this->_inflector->filter(array('script' => $name));
        }

        $view = $this->getView();

//         if (null !== ($path = $this->getViewScriptPath())) {
//             if (method_exists($view, 'addScriptPath')) {
//                 $view->addScriptPath($path);
//             } else {
//                 $view->setScriptPath($path);
//             }
//         } elseif (null !== ($path = $this->getViewBasePath())) {
//             $view->addBasePath($path, $this->_viewBasePrefix);
//         }

        return $view->render($name);
    }
}