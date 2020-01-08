<?php

namespace LaravelEnso\Select\App\Traits;

use Illuminate\Http\Request;
use LaravelEnso\Select\App\Services\Options;

trait TypeaheadBuilder
{
    public function __invoke(Request $request)
    {
        $this->convert($request);

        return (new Options(
            method_exists($this, 'query')
                ? $this->query($request)
                : $this->model::query(),
            $request->get('trackBy') ?? config('enso.select.trackBy'),
            $this->queryAttributes ?? config('enso.select.queryAttributes')
        ))->resource($this->resource ?? null)
        ->appends($this->appends ?? null);
    }

    private function convert(Request $request)
    {
        $params = json_decode($request->get('params'));

        $request->replace(['params' => json_encode(optional($params)->params)])
            ->merge([
                'pivotParams' => json_encode(optional($params)->pivot),
                'customParams' => json_encode(optional($params)->custom),
                'query' => $request->get('query'),
                'paginate' => $request->get('paginate'),
            ]);
    }
}
