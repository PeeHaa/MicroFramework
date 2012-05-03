<?php

class MFW_Model
{
    protected $table;

    protected function getTableColumns()
    {
        return implode(', ', $this->getTableColumnsList());
    }

    protected function getTableColumnsList()
    {
        return array();
    }
}