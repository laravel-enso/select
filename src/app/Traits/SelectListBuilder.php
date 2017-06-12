<?php

namespace LaravelEnso\Select\app\Traits;

use LaravelEnso\Select\app\Classes\SelectListBuilder as ListBuilder;

trait SelectListBuilder
{
    public function getOptionsList()
    {
        $attribute = isset($this->selectAttribute) ? $this->selectAttribute : 'name';
        $selectListBuilder = new ListBuilder($this->selectSourceClass, $attribute);

        return $selectListBuilder->getOptionsList();
    }

    public function buildSelectList($data)
    {
        return ListBuilder::buildSelectList($data);
    }
}
