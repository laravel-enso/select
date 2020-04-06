<?php

namespace LaravelEnso\Select\App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use LaravelEnso\Select\App\Services\Options;

trait OptionsBuilder
{
    public function __invoke(Request $request)
    {
        return $this->response($request);
    }

    private function response(Request $request)
    {
        $query = method_exists($this, 'query') ? $this->query($request) : App::make($this->model)::query();

        return (new Options($query))
            ->when($request->has('trackBy'), fn ($options) => $options->trackBy($request->get('trackBy')))
            ->when($request->has('searchMode'), fn ($options) => $options->searchMode($request->get('searchMode')))
            ->when(isset($this->queryAttributes), fn ($options) => $options->queryAttributes($this->queryAttributes))
            ->when(isset($this->resource), fn ($options) => $options->resource($this->resource))
            ->when(isset($this->appends), fn ($options) => $options->appends($this->appends));
    }
}
