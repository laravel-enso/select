<?php

namespace LaravelEnso\Select\app\Classes;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class OptionsBuilder
{
    private $queryAttributes;
    private $query;
    private $data;
    private $request;
    private $selected;
    private $trackBy;

    public function __construct(Builder $query, string $trackBy, array $queryAttributes, Request $request)
    {
        $this->queryAttributes = $queryAttributes;
        $this->query = $query;
        $this->request = $request;
        $this->trackBy = $trackBy;
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
            ->search()
            ->order()
            ->limit()
            ->get();
    }

    private function setParams()
    {
        if (!$this->request->has('params')) {
            return $this;
        }

        collect(json_decode($this->request->get('params')))
            ->each(function ($value, $column) {
                $this->query->whereIn($column, (array) $value);
            });

        return $this;
    }

    private function setPivotParams()
    {
        if (!$this->request->has('pivotParams')) {
            return $this;
        }

        collect(json_decode($this->request->get('pivotParams')))
            ->each(function ($param, $table) {
                $this->query = $this->query->whereHas($table, function ($query) use ($param) {
                    $query->whereIn('id', (array) $param->id);
                });
            });

        return $this;
    }

    private function setSelected()
    {
        $query = clone $this->query;
        $value = (array) $this->request->get('value');
        $this->selected = $query->whereIn($this->trackBy, $value)->get();

        return $this;
    }

    private function search()
    {
        if (!$this->request->filled('query')) {
            return $this;
        }

        $this->query->where(function ($query) {
            collect($this->queryAttributes)
                ->each(function ($attribute) use ($query) {
                    $query->orWhere($attribute, 'like', '%'.$this->request->get('query').'%');
                });
        });

        return $this;
    }

    private function order()
    {
        $this->query
            ->orderBy(collect($this->queryAttributes)
            ->first());

        return $this;
    }

    private function limit()
    {
        $value = (array) $this->request->get('value');
        $limit = $this->request->get('optionsLimit') - count($value);
        $this->query->limit($limit);

        return $this;
    }

    private function get()
    {
        $this->data = $this->selected->merge($this->query->get());
    }
}
