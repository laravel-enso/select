<?php

namespace LaravelEnso\Select\Traits;

use Illuminate\Http\Request;

trait TypeaheadBuilder
{
    use OptionsBuilder;

    public function __invoke(Request $request)
    {
        $this->convert($request);

        return $this->response($request);
    }

    private function convert(Request $request)
    {
        $request->replace([
            'query'        => $request->get('query'),
            'paginate'     => $request->get('paginate'),
            'params'       => $request->get('params')['params'] ?? null,
            'searchMode'   => $request->get('searchMode'),
            'pivotParams'  => $request->get('params')['pivot'] ?? null,
            'customParams' => $request->get('params')['custom'] ?? null,
        ]);
    }
}
