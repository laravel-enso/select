<?php

namespace LaravelEnso\Select\app\Traits;

use LaravelEnso\Select\app\Classes\SelectListBuilder;

trait SelectListBuilderTrait
{
    public function getOptionsList()
    {
        $attribute = isset($this->selectAttribute) ? $this->selectAttribute : 'name';
        $pivotParams = isset($this->selectPivotParams) ? $this->selectPivotParams : [];
        $selectListBuilder = new SelectListBuilder($this->selectSourceClass, $attribute, $pivotParams);

        return $selectListBuilder->getOptionsList();
    }

    public function buildSelectList($data)
    {
        return SelectListBuilder::buildSelectList($data);
    }
}
