<?php

namespace LaravelEnso\Select\app\Classes;

use Illuminate\Database\Eloquent\Builder;

class OptionsBuilder
{
    private $queryAttributes;
    private $label;
    private $query;
    private $data;
    private $value;
    private $selected;

    public function __construct(Builder $query, array $queryAttributes, string $label, $value)
    {
        $this->queryAttributes = $queryAttributes;
        $this->label = $label;
        $this->query = $query;
        $this->value = (array) $value;
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
            ->setSelected()
            ->query()
            ->limit()
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

    private function setSelected()
    {
        $query = clone $this->query;

        $this->selected = $query->whereIn('id', $this->value)->get();

        return $this;
    }

    private function query()
    {
        $this->query->where(function ($query) {
            collect($this->queryAttributes)->each(function ($attribute) use ($query) {
                $query->orWhere($attribute, 'like', '%'.request('query').'%');
            });
        })->whereIn('id', (array) request('value'), 'or')
        ->orderBy(collect($this->queryAttributes)->first());

        return $this;
    }

    private function limit()
    {
        $limit = request()->get('limit') - count($this->value);

        $this->query->limit($limit);

        return $this;
    }

    private function get()
    {
        $this->data = $this->selected->merge($this->query->get())
            ->reduce(function ($collector, $model) {
                return $collector->push(
                    collect($model->toArray())
                        ->only(['id', $this->label])
                );
            }, collect());
    }
}
