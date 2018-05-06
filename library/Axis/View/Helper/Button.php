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
 * @package     Axis_View
 * @subpackage  Axis_View_Helper
 * @copyright   Copyright 2008-2012 Axis
 * @license     GNU Public License V3.0
 */

/**
 *
 * @category    Axis
 * @package     Axis_View
 * @subpackage  Axis_View_Helper
 * @author      Axis Core Team <core@axiscommerce.com>
 */
class Axis_View_Helper_Button
{
    /**
     * Generates and output button code
     *
     * @param string $title
     * @param array $attributes
     * @param string $type link|button
     * @return string
     */
    public function button($title, array $attributes, $type = 'link')
    {
        $span = '<span class="right"><span class="left"><span class="icon">' . $title . '</span></span></span>';

        $attribute = '';
        foreach ($attributes as $attr => $value) {
            $attribute .= "{$attr}=\"{$value}\" ";
        }

        if (!isset($attributes['class'])) {
            $attribute .= 'class="button" ';
        }

        if ($type == 'link') {
            if (!isset($attributes['title'])) {
                $attribute .= "title=\"{$title}\" ";
            }
            return "<a {$attribute}>{$span}</a>";
        } else {
            return "<button {$attribute}>{$span}</button>";
        }
    }
}
