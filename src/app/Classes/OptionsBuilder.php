<?php

namespace LaravelEnso\Select\app\Classes;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class OptionsBuilder
{
    private $queryAttributes;
    private $label;
    private $query;
    private $data;
    private $request;
    private $selected;

    public function __construct(Builder $query, array $queryAttributes, string $label, Request $request)
    {
        $this->queryAttributes = $queryAttributes;
        $this->label = $label;
        $this->query = $query;
        $this->request = $request;
    }

    public function data()
    {
        $this->run();

        return $this->data;
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
        if (!$this->request->filled('params')) {
            return $this;
        }

        collect(json_decode($this->request->get('params')))
            ->each(function ($value, $column) {
                $this->query->where($column, $value);
            });

        return $this;
    }

    private function setPivotParams()
    {
        if (!$this->request->filled('pivotParams')) {
            return $this;
        }

        collect(json_decode($this->request->get('pivotParams')))
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
        $value = (array) $this->request->get('value');
        $this->selected = $query->whereIn('id', $value)->get();

        return $this;
    }

    private function query()
    {
        $this->query->where(function ($query) {
            collect($this->queryAttributes)->each(function ($attribute) use ($query) {
                $query->orWhere($attribute, 'like', '%'.$this->request->get('query').'%');
            });
        })
            ->orderBy(collect($this->queryAttributes)->first());

        return $this;
    }

    private function limit()
    {
        $value = (array) $this->request->get('value');
        $limit = $this->request->get('limit') - count($value);
        $this->query->limit($limit);

        return $this;
    }

    private function get()
    {
        $this->data = $this->selected->merge($this->query->get());
    }
}
