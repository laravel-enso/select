<?php

namespace LaravelEnso\Select\app\Traits;

use Illuminate\Http\Request;
use LaravelEnso\Select\app\Classes\OptionsBuilder as Builder;

trait OptionsBuilder
{
    public function options(Request $request)
    {
        return new Builder(
            method_exists($this, 'query')
                ? $this->query($request)
                : $this->model::query(),
            $this->trackBy ?? 'id',
            $this->queryAttributes ?? ['name']
        );
    }
}
