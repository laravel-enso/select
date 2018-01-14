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

    public function data()
    {
        $this->run();

        return $this->result->pluck($this->displayAttribute, 'id');
    }

    private function run()
    {
        $this->setParams()
            ->setPivotParams()
            ->setSelected()
            ->query()
            ->setResult();
    }

    private function setParams()
    {
        if (!request()->filled('params')) {
            return $this;
        }

        $params = collect(json_decode(request('params')));

        $params->each(function ($value, $column) {
            $this->query->where($column, $value);
        });

        return $this;
    }

    private function setPivotParams()
    {
        if (!request()->filled('pivotParams')) {
            return $this;
        }

        $pivotParams = collect(json_decode(request('pivotParams')));

        $pivotParams->each(function ($param, $table) {
            $this->query = $this->query->whereHas($table, function ($query) use ($param) {
                $query->whereId($param->id);
            });
        });

        return $this;
    }

    private function setSelected()
    {
        $query = clone $this->query;
        $selected = (array) request('value');
        $this->selected = $query->whereIn('id', $selected)->get();

        return $this;
    }

    private function query()
    {
        $this->query->where(function ($query) {
            collect($this->selectAttributes)->each(function ($attribute) use ($query) {
                $query->orWhere($attribute, 'like', '%'.request('query').'%');
            });
        });

        return $this;
    }

    private function setResult()
    {
        $this->result = $this->selected->merge(
            $this->query->orderBy(
                collect($this->selectAttributes)->first()
            )->limit(20)->get()
            )->reduce(function ($collector, $model) {
                return $collector->push(
                    collect($model->toArray())
                        ->only(['id', $this->displayAttribute])
                );
            }, collect());
    }
}
