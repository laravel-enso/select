<?php

namespace LaravelEnso\Select\app\Classes;

class SelectListBuilder
{
    private $selectAttributes;
    private $displayAttribute;
    private $class;
    private $query;
    private $result;
    private $selected;

    public function __construct($class, $selectAttributes, $displayAttribute, $query = null)
    {
        $this->class = $class;
        $this->selectAttributes = $selectAttributes;
        $this->displayAttribute = $displayAttribute;
        $this->query = $query ?: $this->class::query();
    }

    public function getOptionList()
    {
        $this->run();

        return $this->result->pluck($this->displayAttribute, 'id');
    }

    private function run()
    {
        $this->processParams();
        $this->processPivotParams();
        $this->setSelected();
        $this->processQuery();
        $this->setResult();
    }

    private function processParams()
    {
        if (!request()->filled('params')) {
            return false;
        }

        $params = json_decode(request('params'));

        foreach ($params as $column => $value) {
            $this->query->where($column, $value);
        }
    }

    private function processPivotParams()
    {
        if (!request()->filled('pivotParams')) {
            return false;
        }

        $pivotParams = json_decode(request('pivotParams'));

        foreach ($pivotParams as $table => $param) {
            $this->query = $this->query->whereHas($table, function ($query) use ($param) {
                $query->whereId($param->id);
            });
        }
    }

    private function setSelected()
    {
        $query = clone $this->query;
        $selected = (array) request('value');
        $this->selected = $query->whereIn('id', $selected)->get();
    }

    private function processQuery()
    {
        $this->query->where(function ($query) {
            collect($this->selectAttributes)->each(function ($attribute) use ($query) {
                $query->orWhere($attribute, 'like', '%'.request('query').'%');
            });
        });
    }

    private function setResult()
    {
        $this->result = $this->selected->merge(
            $this->query->orderBy(collect($this->selectAttributes)->first())
                ->limit(20)->get()
            )->reduce(function ($collector, $model) {
                return $collector->push(
                    collect($model->toArray())
                        ->only(['id', $this->displayAttribute])
                );
            }, collect());
    }
}
