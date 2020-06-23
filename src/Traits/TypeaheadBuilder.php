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
        $params = json_decode($request->get('params'));

        $request->replace([
            'query' => $request->get('query'),
            'paginate' => $request->get('paginate'),
            'params' => json_encode(optional($params)->params),
            'searchMode' => $request->get('searchMode'),
            'pivotParams' => json_encode(optional($params)->pivot),
            'customParams' => json_encode(optional($params)->custom),
        ]);
    }
}
