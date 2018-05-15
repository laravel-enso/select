<?php

namespace LaravelEnso\Select\app\Traits;

use LaravelEnso\Select\app\Classes\OptionsBuilder as Builder;

trait OptionsBuilder
{
    public function options()
    {
        return new Builder(
            method_exists($this, 'query')
                ? $this->query()
                : $this->model::query(),
            $this->trackBy ?? 'id',
            $this->queryAttributes ?? ['name']
        );
    }
}
