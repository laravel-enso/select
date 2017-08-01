<?php

namespace LaravelEnso\Select\app\Classes;

class SelectListBuilder
{
    private $attribute;
    private $class;
    private $query;
    private $result;

    public function __construct($class, $attribute, $query = null)
    {
        $this->class = $class;
        $this->attribute = $attribute;
        $this->query = $query ?: $this->class::query();
        $this->run();
    }

    public function getOptionsList()
    {
        return $this->result;
    }

    private function run()
    {
        $this->processParams();
        $this->processPivotParams();
        $this->setResult();
    }

    private function processParams()
    {
        if (!request()->has('params')) {
            return false;
        }

        $params = json_decode(request('params'));

        foreach ($params as $column => $value) {
            $this->query->where($column, $value);
        }
    }

    private function processPivotParams()
    {
        if (!request()->has('pivotParams')) {
            return false;
        }

        $pivotParams = json_decode(request('pivotParams'));

        foreach ($pivotParams as $table => $param) {
            $this->query = $this->query->whereHas($table, function ($query) use ($param) {
                $query->whereId($param->id);
            });
        }
    }

    private function setResult()
    {
        $ids = (array) request('selected');
        $models = $this->query->where($this->attribute, 'like', '%'.request('query').'%')
                        ->orderBy($this->attribute)->limit(10)->get();
        $selected = $this->class::whereIn('id', $ids)->get();
        $this->result = $this->buildSelectList($models->merge($selected)->pluck('name', 'id'));
    }

    public static function buildSelectList($data)
    {
        $response = collect();

        foreach ($data as $key => $value) {
            $response->push([
                'key'   => $key,
                'value' => $value,
            ]);
        }

        return $response;
    }
}
