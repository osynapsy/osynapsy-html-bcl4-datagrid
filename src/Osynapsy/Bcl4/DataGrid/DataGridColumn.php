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

/**
 * Description of DataGridColumn
 *
 * @author Pietro Celeste <p.celeste@osynapsy.net>
 */
class DataGridColumn
{
    const FIELD_TYPE_DATE_EU = 'date';
    const FIELD_TYPE_MONEY = 'money';
    const FIELD_TYPE_EMPTY  = 'empty';
    const FIELD_TYPE_EURO  = 'euro';
    const FIELD_TYPE_INTEGER = 'integer';
    const FIELD_TYPE_DOLLAR  = 'dollar';
    const FIELD_TYPE_CHECKBOX = 'check';
    const FIELD_TYPE_STRING = 'string';
    const FIELD_TYPE_COMMAND = 'commands';

    private $properties = [
        'dimension' => [
            'xs' => 12,
            'sm' => 12,
            'md' => 2,
            'lg' => 2,
            'xl' => 2
        ],
        'type' => 'string',
        'function' => null,
        'class' => null,
        'classTd' => [],
        'label' => '&nbsp;',
    ];
    public $parentId;
    protected $control;
    protected $controlDataset = [];

    public function __construct($label, $field, $class = '', $type = 'string', callable $function = null, $fieldOrderBy = null)
    {
        $this->properties['label'] = $label;
        $this->properties['field'] = $field;
        $this->properties['type'] = $type;
        $this->properties['class'] = $class;
        $this->properties['function'] = $function;
        $this->properties['fieldOrderBy'] = empty($fieldOrderBy) ? $field : $fieldOrderBy;
        $this->addClassTd([$class]);
    }

    public function setParent($id)
    {
        $this->parentId = $id;
    }

    public function addClassTd(array $class)
    {
        $this->properties['classTd'] = array_merge($this->properties['classTd'], $class);
    }

    public function setClass($class)
    {
        $this->properties['class'] = $class;
        $this->properties['classTd'] = [$class];
    }

    public function setFunction(callable $fnc)
    {
        $this->properties['function'] = $fnc;
    }

    public function setEditControl($control, $dataset)
    {
        if (!empty($dataset)) {
            $this->controlDataset = $dataset;
        }
        return $this->control = $control;
    }

    public function getControl()
    {
        return [$this->control, $this->controlDataset];
    }

    public function __get($name)
    {
        return $this->properties[$name];
    }

    public function __set($name, $value)
    {
        $this->properties[$name] = $value;
    }
}
