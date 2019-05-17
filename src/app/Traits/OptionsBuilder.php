<?php

namespace LaravelEnso\Select\app\Traits;

use Illuminate\Http\Request;
use LaravelEnso\Select\app\Services\Options;

trait OptionsBuilder
{
    public function __invoke(Request $request)
    {
        return new Options(
            method_exists($this, 'query')
                ? $this->query($request)
                : $this->model::query(),
            $request->get('trackBy') ?? config('enso.select.trackBy'),
            $this->queryAttributes ?? config('enso.select.queryAttributes'),
            $this->resource ?? null
        );
    }
}
