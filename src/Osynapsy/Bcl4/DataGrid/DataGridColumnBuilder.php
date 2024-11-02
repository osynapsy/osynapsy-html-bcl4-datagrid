<?php
namespace Osynapsy\Bcl4\DataGrid;

use Osynapsy\Html\Tag;

/**
 * Description of DataGridColumnBuilder
 *
 * @author Pietro Celeste <p.celeste@osynapsy.net>
 */
class DataGridColumnBuilder
{
    public static function buildTh(DataGridColumn $col, $orderedFields)
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

    public static function buildTd(DataGridColumn $column)
    {

    }
}
