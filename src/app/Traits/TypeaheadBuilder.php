<?php

namespace LaravelEnso\Select\app\Traits;

use Illuminate\Http\Request;
use LaravelEnso\Helpers\app\Classes\Obj;
use LaravelEnso\Select\app\Services\Options;

trait TypeaheadBuilder
{
    public function __invoke(Request $request)
    {
        $payload = $this->convert($request);

        return (new Options(
            method_exists($this, 'query')
                ? $this->query($payload)
                : $this->model::query(),
            $request->get('trackBy') ?? config('enso.select.trackBy'),
            $this->queryAttributes ?? config('enso.select.queryAttributes'),
            $this->resource ?? null
        ))->toResponse($payload);
    }

    private function convert($request)
    {
        return (new Obj())
            ->set('customParams', $this->reEncode('custom', $request->get('params')))
            ->set('pivotParams', $this->reEncode('pivot', $request->get('params')))
            ->set('query', $request->get('query'))
            ->set('paginate', $request->get('paginate'));
    }

    private function reEncode(string $name, string $json)
    {
        return json_encode(json_decode($json)->$name);
    }
}
