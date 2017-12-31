<?php

namespace LaravelEnso\Select\app\Traits;

use LaravelEnso\Select\app\Classes\SelectListBuilder as ListBuilder;

trait SelectListBuilder
{
    public function getOptionList()
    {
        $selectClass = $this->selectSourceClass ?? null;
        $selectAttributes = $this->selectAttributes ?? 'name';
        $displayAttribute = $this->displayAttribute ?? 'name';
        $selectQuery = $this->selectQuery ?? null;

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
