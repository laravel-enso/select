<?php

namespace LaravelEnso\Select\App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use LaravelEnso\Select\App\Services\Options;

trait OptionsBuilder
{
    public function __invoke(Request $request)
    {
        return (new Options(
            method_exists($this, 'query')
                ? $this->query($request)
                : $this->model::query(),
            $request->get('trackBy', Config::get('enso.select.trackBy')),
            $this->queryAttributes ?? Config::get('enso.select.queryAttributes')
        ))->searchMode($request->get('searchMode', Config::get('enso.select.searchMode')))
            ->resource($this->resource ?? null)
            ->appends($this->appends ?? null);
    }
}
