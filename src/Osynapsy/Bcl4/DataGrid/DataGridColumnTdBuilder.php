<?php

/*
 * This file is part of the Osynapsy package.
 *
 * (c) Pietro Celeste <p.celeste@osynapsy.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Osynapsy\Bcl4\DataGrid;

use Osynapsy\Html\Tag;

/**
 * Description of DataGridColumnTdBuilder
 *
 * @author Pietro Celeste <p.celeste@osynapsy.net>
 */
class DataGridColumnTdBuilder
{
    /**
     * Build a body cell of DataGrid component
     *
     * @param Tag $tr
     * @param type $record
     * @return Tag
     */
    public static function build(DataGridColumn $col, Tag $tr, array $record, $inEditing)
    {
        if (is_callable($col->field)) {
            $col->function = $col->field;
            $value = null;
        } elseif (!array_key_exists($col->field, $record)) {
            $value = '<label class="label label-warning">No data found</label>';
        } else {
            $value = $record[$col->field];
        }
        $td = new Tag('div', null, 'bcl-datagrid-td');
        $td->add(self::valueFormatting($col, $value, $td, $record, $tr, $inEditing));
        return $td;
    }

    /**
     * Format a value of cell for correct visualization
     *
     * @param DataGridColumn $col
     * @param string $value to format.
     * @param object $cell container of value
     * @param type $rec record which contains value.
     * @param type $tr row container object
     * @return string
     */
    public static function valueFormatting($col, $value, &$cell, $rec, &$tr, $inEditing)
    {
        $fnc = $col->function;
        if (!empty($fnc)) {
            $value = $fnc($value, $rec, $cell, $tr);
        }
        if ($inEditing && !empty($col->getControl()[0])) {
            return self::controlFactory($col, $value);
        }
        switch($col->type) {
            case DataGridColumn::FIELD_TYPE_CHECKBOX:
                if (empty($value)) {
                    break;
                }
                $value = self::buildCheckBox($col, $value);
                break;
            case DataGridColumn::FIELD_TYPE_DATE_EU:
                $datetime = \DateTime::createFromFormat('Y-m-d', $value);
                $value = $datetime === false ? $value : $datetime->format('d/m/Y');
                $col->addClassTd(['text-center']);
                break;
            case DataGridColumn::FIELD_TYPE_INTEGER:
                $col->addClassTd(['text-right']);
                break;
            case DataGridColumn::FIELD_TYPE_EMPTY:
                $value = '&nbsp;';
                break;
            case DataGridColumn::FIELD_TYPE_EURO:
            case DataGridColumn::FIELD_TYPE_MONEY:
            case DataGridColumn::FIELD_TYPE_DOLLAR;
                $value = self::formatCurrencyValue($value, $col->type);
                $col->addClassTd(['text-right']);
                break;
            case DataGridColumn::FIELD_TYPE_COMMAND:
                $col->addClassTd(['cmd-row']);
                break;
            case DataGridColumn::FIELD_TYPE_STRING:
                $value = strval($value);
                break;
        }
        if ($col->classTd) {
            $cell->addClass(implode(' ', $col->classTd));
        }
        return ($value != '0' && empty($value)) ? '&nbsp;' : $value;
    }

    private static function formatCurrencyValue($rawValue, $type)
    {
        $value = '';
        switch($type){
            case DataGridColumn::FIELD_TYPE_EURO:
                $value = '&euro; ';
                break;
            case DataGridColumn::FIELD_TYPE_DOLLAR;
                $value = '$ ';
                break;
        }
        if (!empty($rawValue) && is_numeric($rawValue)) {
            $value .= number_format($rawValue, 2, ',', '.');
        } else {
            $value = $rawValue;
        }
        return $value;
    }

    private static function buildCheckBox($col, $value)
    {
        $class = $col->parentId.''.$col->field;
        $checkbox = new Tag('input');
        $checkbox->attributes([
            'type' => 'checkbox',
            'name' => $class.'['.$value.']',
            'class' => 'grid-check',
            'value' => $value
        ]);
        if (!empty($_POST[$class]) && !empty($_POST[$class][$value])) {
            $checkbox->attribute('checked','checked');
        }
        return $checkbox->get();
    }

    protected static function controlFactory($col, $value)
    {
        list($class, $dataset) = $col->getControl();
        $component = new $class($col->field.'_edit');
        $component->setDataset($dataset);
        $component->setValue($value);
        return $component;
    }
}
