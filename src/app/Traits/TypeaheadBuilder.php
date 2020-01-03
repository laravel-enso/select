<?php

namespace LaravelEnso\Select\app\Traits;

use Illuminate\Http\Request;
use LaravelEnso\Helpers\app\Classes\Obj;
use LaravelEnso\Select\app\Services\Options;

trait TypeaheadBuilder
{
    protected $request;

    public function __invoke(Request $request)
    {
        $this->request = $this->convertRequest($request);

        return (new Options(
            method_exists($this, 'query')
                ? $this->query($this->request)
                : $this->model::query(),
            $request->get('trackBy') ?? config('enso.select.trackBy'),
            $this->queryAttributes ?? config('enso.select.queryAttributes')
        ))->resource($this->resource ?? null)
        ->appends($this->appends ?? null)
        ->toResponse($this->request);
    }

    private function convertRequest(Request $request) //TODO review
    {
        $params = json_decode($request->get('params'));

        return new Obj([
            'customParams' => json_encode(optional($params)->custom),
            'pivotParams' => json_encode(optional($params)->pivot),
            'query' => $request->get('query'),
            'paginate' => $request->get('paginate'),
        ]);
    }
}
