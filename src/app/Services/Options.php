<?php

namespace LaravelEnso\Select\app\Services;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Options implements Responsable
{
    private const Limit = 100;

    private $queryAttributes;
    private $query;
    private $request;
    private $selected;
    private $trackBy;
    private $value;
    private $resource;

    public function __construct(Builder $query, string $trackBy, array $queryAttributes, string $resource = null)
    {
        $this->query = $query;
        $this->trackBy = $trackBy;
        $this->queryAttributes = $queryAttributes;
        $this->resource = $resource;
    }

    public function toResponse($request)
    {
        $this->request = $request;

        return $this->resource
            ? $this->resource::collection($this->data())
            : $this->data();
    }

    private function data()
    {
        return $this->setValue()
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
                return $value === null
                    ? $this->query->whereNull($column)
                    : $this->query->whereIn($column, (array) $value);
            });

        return $this;
    }

    private function setPivotParams()
    {
        if (! $this->request->has('pivotParams')) {
            return $this;
        }

        collect(json_decode($this->request->get('pivotParams')))
            ->each(function ($param, $relation) {
                $this->query->whereHas($relation, function ($query) use ($param) {
                    collect($param)->each(
                        function ($value, $attribute) use ($query) {
                            $query->whereIn($attribute, (array) $value);
                        }
                    );
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
            $this->searchArguments()->each(function ($argument) use ($query) {
                $this->match($query, $argument);
            });
        });

        return $this;
    }

    private function match($query, $argument)
    {
        $query->where(function ($query) use ($argument) {
            collect($this->queryAttributes)->each(function ($attribute) use ($query, $argument) {
                return $this->isNested($attribute)
                    ? $this->whereHasRelation($query, $attribute, $argument)
                    : $query->orWhere(
                        $attribute,
                        config('enso.select.comparisonOperator'),
                        '%'.$argument.'%'
                    );
            });
        });
    }

    private function searchArguments()
    {
        return collect(explode(' ', $this->request->get('query')));
    }

    private function whereHasRelation($query, $attribute, $argument)
    {
        if (! $this->isNested($attribute)) {
            $query->where(
                $attribute,
                config('enso.select.comparisonOperator'),
                '%'.$argument.'%'
            );

            return;
        }

        $attributes = collect(explode('.', $attribute));

        $query->orWhere(function ($query) use ($attributes, $argument) {
            $query->whereHas($attributes->shift(), function ($query) use ($attributes, $argument) {
                $this->whereHasRelation($query, $attributes->implode('.'), $argument);
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
        $limit = $this->request->get('paginate')
            ?? self::Limit - count($this->value);

        $this->query->limit($limit);

        return $this;
    }

    private function get()
    {
        return $this->selected
            ->merge($this->query->get());
    }

    private function isNested($attribute)
    {
        return Str::contains($attribute, '.');
    }
}
