<?php

namespace LaravelEnso\Select\app\Traits;

use LaravelEnso\Select\app\Classes\SelectListBuilder as ListBuilder;

trait SelectListBuilder
{
    public function getOptionList()
    {
        $selectClass = isset($this->selectSourceClass) ? $this->selectSourceClass : null;
        $selectAttributes = isset($this->selectAttributes) ? $this->selectAttributes : 'name';
        $displayAttribute = isset($this->displayAttribute) ? $this->displayAttribute : 'name';
        $selectQuery = isset($this->selectQuery) ? $this->selectQuery : null;

        $builder = new ListBuilder(
            $selectClass,
            $selectAttributes,
            $displayAttribute,
            $selectQuery
        );

        return $builder->getOptionList();
    }

    public function buildSelectList($data)
    {
        return ListBuilder::buildSelectList($data);
    }
}
