<?php
namespace Osynapsy\Bcl4\DataGrid;

use Osynapsy\Html\Tag;

/**
 * Description of DataGridBuilder
 *
 * @author Pietro Celeste <p.celeste@osynapsy.net>
 */
class DataGridBuilder
{
    protected static $grid;

    public static function build(DataGrid $grid)
    {
        self::$grid = $grid;
        $executionTime = microtime(true);
        $title = $grid->getTitle();
        $columns = $grid->getColumns();
        $paginator= $grid->getPaginator();
        $emptyMessage = $grid->getEmptyMessage();
        $dataset = empty($paginator) ? $grid->getDataset() : self::loadDataset($paginator) ?? [];
        $strOrderBy = empty($paginator) ? '' : $paginator->getOrderBy();
        $minRows = $grid->getRowMinimum();
        $container = new Tag('dummy');
        if (!empty($title)) {
            $container->add(self::buildTitle($title));
        }
        if ($grid->showHeader()) {
            $container->add(self::buildColumnHeader($columns, $strOrderBy));
        }
        $container->add(self::buildBody($columns, $dataset, $emptyMessage, $grid->getInEditing(), $minRows ?? 0));
        if (!empty($paginator)) {
            $container->add(self::buildPagination($grid, $paginator, microtime(true) - $executionTime));
        }
        return $container;
    }

    protected static function loadDataset($paginator)
    {
        try {
            return $paginator->loadData(null, true);
        } catch (\Exception $e) {
            return [['error' => str_replace(PHP_EOL,'<br>', $e->getMessage())]];
        }
    }

    /**
     * Internal method for build a Datagrid column head.
     *
     * @return Tag
     */
    private static function buildColumnHeader(&$columns, $strOrderBy)
    {
        $orderByFields = explode(',', $strOrderBy);
        $header = new Tag('div', null, 'd-none d-sm-block hidden-xs');
        $tr = $header->add(new Tag('div', null, 'row bcl-datagrid-thead'));
        foreach($columns as $column) {
            $th = DataGridColumnThBuilder::build($column, $orderByFields);
            if (empty($th)) {
                continue;
            }
            $tr->add($th);
        }
        return $header;
    }

    /**
     * Internal method for build Datagrid body.
     *
     * @return Tag
     */
    protected static function buildBody($columns, $dataset, $emptyMessage, $fncEditing, $minRows = 0)
    {
        $body = new Tag('div');
        $body->attribute('class','bcl-datagrid-body bg-white');
        $i = 0;
        if (empty($dataset)) {
            $body->add(self::emptyRowFactory($emptyMessage));
            $i++;
        } else {
            foreach ($dataset as $row) {
                self::$grid->execAction(DataGrid::HOOK_BEFORE_ADD_ROW, $row, $body);
                $body->add(self::bodyRowFactory($columns, $row, 'row bcl-datagrid-body-row', $fncEditing($row)));
                self::$grid->execAction(DataGrid::HOOK_AFTER_ADD_ROW, $row, $body);
                $i++;
            }
        }
        if (!empty($minRows)) {
            self::buildPlaceholderRows($body, $i, $minRows);
        }
        return $body;
    }

    protected static function buildPlaceholderRows($body, $i, $minRows)
    {
        for ($i; $i < $minRows; $i++) {
            $body->add(self::emptyRowFactory('&nbsp;'));
        }
        $i++;
    }

    /**
     * Internal metod for build empty message.
     *
     * @param string $message
     * @return Void
     */
    protected static function emptyRowFactory($message)
    {
        return '<div class="row"><div class="col-lg-12 text-center bcl-datagrid-td">'.$message.'</div></div>';
    }

    /**
     * Internal method for build a Datagrid row
     *
     * @param type $row
     * @return Tag
     */
    private static function bodyRowFactory($columns, $record, $class, $inEditing)
    {
        $tr = new DataGridRow('div', null, $class);
        $commands = [];
        foreach ($columns as $column) {
            //$cell = $column->buildTd($tr, $record ?? []);
            $cell = DataGridColumnTdBuilder::build($column, $tr, is_array($record) ? $record : [], $inEditing);
            if ($column->type !== DataGridColumn::FIELD_TYPE_COMMAND) {
                $tr->add($cell);
                continue;
            }
            $commands[] = $cell;
        }
        if (!empty($commands)) {
            $tr->add(self::buildCellCommands($commands));
        }
        if (!empty($record['_url_detail'])) {
            $tr->attribute('data-url-detail', $record['_url_detail']);
        }
        return $tr;
    }

    protected static function buildCellCommands($commands)
    {
        $cell = null;
        foreach ($commands as $i => $command) {
            if (empty($i)) {
                $cell = $command;
                continue;
            }
            $cell->add($command->child(0));
        }
        return $cell;
    }

    /**
     * Build Datagrid pagination
     *
     * @return Tag
     */
    private static function buildPagination(DataGrid $grid, $pagination, $executionTime = 0)
    {
        $row = new Tag('div', null, 'd-flex justify-content-end mt-1');
        if ($grid->showExecutionTime()) {
            $row->add(sprintf('<small class="p-2 mr-auto">Tempo di esecuzione : %s sec</small>', $executionTime));
        }
        $row->add(new Tag('div', null, 'pt-1 pl-2'))->add($pagination)->setPosition('end');
        return $row;
    }

    private static function buildTitle($title)
    {
        $tr = new Tag('div', null, 'row bcl-datagrid-title');
        $tr->add(new Tag('div', null, 'col-lg-12'))->add($title);
        return $tr;
    }
}
