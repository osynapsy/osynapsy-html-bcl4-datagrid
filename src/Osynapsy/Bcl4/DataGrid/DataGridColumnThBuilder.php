<?php
/*
 * This file is part of the Osynapsy Bcl4 Datagrid package.
 *
 * (c) Pietro Celeste <p.celeste@osynapsy.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Osynapsy\Bcl4\DataGrid;

use Osynapsy\Html\Tag;

/**
 * Description of DataGridColumnBuilder
 *
 * @author Pietro Celeste <p.celeste@osynapsy.net>
 */
class DataGridColumnThBuilder
{
    public static function build(DataGridColumn $col, $orderedFields)
    {
        $rawLabel = $col->label;
        if (empty($rawLabel) || $rawLabel[0] === '_' || $col->type === DataGridColumn::FIELD_TYPE_COMMAND) {
            return;
        }
        if ($col->type === DataGridColumn::FIELD_TYPE_CHECKBOX) {
            $rawLabel = self::builCheckBoxLabel($col->parentId, $col->field);
        }
        $th = new Tag('div', null, $col->class . ' bcl-datagrid-th');
        $th->add(new Tag('span'))->add($rawLabel);
        if ($col->type !== DataGridColumn::FIELD_TYPE_CHECKBOX) {
            self::buildThOrderByDummy($th, $col->fieldOrderBy, $orderedFields);
        }
        return $th;
    }

    private static function builCheckBoxLabel($parentId, $field)
    {
        return '<span class="fa fa-check bcl-datagrid-th-check-all" data-field-class="'.$parentId.''.$field.'"></span>';
    }

    public static function buildThOrderByDummy($th, $orderByField, $orderedFields)
    {
        $th->attribute('data-idx', $orderByField)->addClass('bcl-datagrid-th-order-by');
        if (empty($orderedFields)) {
            return;
        }
        foreach ([$orderByField, $orderByField.' DESC'] as $i => $token) {
            $key = array_search($token, $orderedFields);
            if ($key !== false) {
                $icon = ($key + 1).' <i class="fa fa-arrow-'.(empty($i) ? 'up' : 'down').'"></i>';
                $th->add('<span class="bcl-datagrid-th-order-label">'.$icon.' </span>');
            }
        }
    }
}
