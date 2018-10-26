<?php

namespace LaravelEnso\Select\app\Classes;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Responsable;

class OptionsBuilder implements Responsable
{
    private const Limit = 100;

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
        if (! $this->request->has('params')) {
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
        if (! $this->request->has('pivotParams')) {
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
        if (! $this->request->filled('query')) {
            return $this;
        }

        $this->query->where(function ($query) {
            collect($this->queryAttributes)
                ->each(function ($attribute) use ($query) {
                    $this->isNested($attribute)
                        ? $this->where($query, $attribute)
                        : $query->orWhere(
                            $attribute, 'like', '%'.$this->request->get('query').'%'
                        );
                });
        });

        return $this;
    }

    private function where($query, $attribute)
    {
        if (! $this->isNested($attribute)) {
            $query->where($attribute, 'like', '%'.$this->request->get('query').'%');

            return;
        }

        $attributes = collect(explode('.', $attribute));

        $query->orWhere(function ($query) use ($attributes) {
            $query->whereHas($attributes->shift(), function ($query) use ($attributes) {
                $this->where($query, $attributes->implode('.'));
            });
        });
    }

    private function order()
    {
        $attribute = collect($this->queryAttributes)->first();

        if (! $this->isNested($attribute)) {
            $this->query->orderBy($attribute);
        }

        return $this;
    }

    private function limit()
    {
        $limit = $this->request->get('limit')
            ?? self::Limit - count($this->value);

        $this->query->limit($limit);

        return $this;
    }

    private function get()
    {
        $this->data = $this->selected
            ->merge($this->query->get());
    }

    private function isNested($attribute)
    {
        return Str::contains($attribute, '.');
    }
}
