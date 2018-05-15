<?php

namespace LaravelEnso\Select\app\Classes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Responsable;

class OptionsBuilder implements Responsable
{
    private $queryAttributes;
    private $query;
    private $data;
    private $request;
    private $selected;
    private $trackBy;
    private $value;

    public function __construct(Builder $query, string $trackBy, array $queryAttributes)
    {
        $this->queryAttributes = $queryAttributes;
        $this->query = $query;
        $this->trackBy = $trackBy;
    }

    public function toResponse($request)
    {
        $this->request = $request;

        $this->run();

        return $this->data;
    }

    private function run()
    {
        $this->setValue()
            ->setParams()
            ->setPivotParams()
            ->setSelected()
            ->search()
            ->order()
            ->limit()
            ->get();
    }

    private function setValue()
    {
        $this->value = $this->request->has('value')
            ? (array) $this->request->get('value')
            : [];

        return $this;
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
                $this->query->whereHas($table, function ($query) use ($param) {
                    $query->whereIn('id', (array) $param->id);
                });
            });

        return $this;
    }

    private function setSelected()
    {
        $query = clone $this->query;

        $this->selected = $query->whereIn($this->trackBy, $this->value)->get();

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
        $limit = $this->request->get('optionsLimit') - count($this->value);
        $this->query->limit($limit);

        return $this;
    }

    private function get()
    {
        $this->data = $this->selected
            ->merge($this->query->get());
    }
}
