<?php

namespace LaravelEnso\Select\app\Classes;

use Illuminate\Database\Eloquent\Builder;

class OptionsBuilder
{
    private $queryAttributes;
    private $label;
    private $query;
    private $data;

    public function __construct(Builder $query, array $queryAttributes, string $label)
    {
        $this->queryAttributes = $queryAttributes;
        $this->label = $label;
        $this->query = $query;
    }

    public function data()
    {
        $this->run();

        return $this->data
            ->pluck($this->label, 'id');
    }

    private function run()
    {
        $this->setParams()
            ->setPivotParams()
            ->query()
            ->get();
    }

    private function setParams()
    {
        if (!request()->filled('params')) {
            return $this;
        }

        collect(json_decode(request('params')))
            ->each(function ($value, $column) {
                $this->query->where($column, $value);
            });

        return $this;
    }

    private function setPivotParams()
    {
        if (!request()->filled('pivotParams')) {
            return $this;
        }

        collect(json_decode(request('pivotParams')))
            ->each(function ($param, $table) {
                $this->query = $this->query->whereHas($table, function ($query) use ($param) {
                    $query->whereId($param->id);
                });
            });

        return $this;
    }

    private function query()
    {
        $this->query->where(function ($query) {
            collect($this->queryAttributes)->each(function ($attribute) use ($query) {
                $query->orWhere($attribute, 'like', '%'.request('query').'%');
            });
        })->whereIn('id', (array) request('value'), 'or')
        ->orderBy(collect($this->queryAttributes)->first())
        ->limit(10);

        return $this;
    }

    private function get()
    {
        $this->data = $this->query->get()
            ->reduce(function ($collector, $model) {
                return $collector->push(
                    collect($model->toArray())
                        ->only(['id', $this->label])
                );
            }, collect());
    }
}
