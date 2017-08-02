<?php

namespace LaravelEnso\Select\app\Traits;

use LaravelEnso\Select\app\Classes\SelectListBuilder as ListBuilder;

trait SelectListBuilder
{
    public function getOptionsList()
    {
        $selectAttributes = isset($this->selectAttributes) ? $this->selectAttributes : 'name';
        $displayAttribute = isset($this->displayAttribute) ? $this->displayAttribute : 'name';
        $selectListBuilder = new ListBuilder($this->selectSourceClass, $selectAttributes, $displayAttribute);

        return $selectListBuilder->getOptionsList();
    }

    public function buildSelectList($data)
    {
        return ListBuilder::buildSelectList($data);
    }
}
