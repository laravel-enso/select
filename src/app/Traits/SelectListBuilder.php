<?php

namespace LaravelEnso\Select\app\Traits;

use LaravelEnso\Select\app\Classes\SelectListBuilder as Builder;

trait SelectListBuilder
{
    public function getOptionList()
    {
        $class = $this->selectSourceClass ?? null;
        $selectAttributes = $this->selectAttributes ?? 'name';
        $displayAttribute = $this->displayAttribute ?? 'name';
        $query = $this->selectQuery ?? null;

        $builder = new Builder($class, $selectAttributes, $displayAttribute, $query);

        return $builder->data();
    }
}
