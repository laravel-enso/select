<?php

namespace LaravelEnso\Select\App\Traits;

use Illuminate\Http\Request;
use LaravelEnso\Select\App\Services\Options;

trait OptionsBuilder
{
    public function __invoke(Request $request)
    {
        return (new Options(
            method_exists($this, 'query')
                ? $this->query($request)
                : $this->model::query(),
            $request->get('trackBy') ?? config('enso.select.trackBy'),
            $this->queryAttributes ?? config('enso.select.queryAttributes')
        ))->resource($this->resource ?? null)
        ->appends($this->appends ?? null);
    }
}
