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
    private $appends;

    public function __construct(Builder $query, array $queryAttributes, string $label, Request $request,
        array $appends = [])
    {
        $this->queryAttributes = $queryAttributes;
        $this->label = $label;
        $this->query = $query;
        $this->request = $request;
        $this->appends = $appends;
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
            ->get()
            ->setAppends();
    }

    private function setParams()
    {
        if (!$this->request->has('params')) {
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
        if (!$this->request->has('pivotParams')) {
            return $this;
        }

        collect(json_decode($this->request->get('pivotParams')))
            ->each(function ($param, $table) {
                $this->query = $this->query->whereHas($table, function ($query) use ($param) {
                    if (is_array($param->id)) {
                        $query->whereIn('id', $param->id);

                        return;
                    }

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
        $limit = $this->request->get('optionsLimit') - count($value);
        $this->query->limit($limit);

        return $this;
    }

    private function get()
    {
        $this->data = $this->selected->merge($this->query->get());

        return $this;
    }

    private function setAppends()
    {
        if (!$this->appends) {
            return $this;
        }

        $this->data->each->setAppends($this->appends);

        return $this;
    }
}
