<?php

namespace LaravelEnso\Select\App\Services;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Options implements Responsable
{
    private const Limit = 100;

    private Builder $query;
    private string $trackBy;
    private Collection $queryAttributes;
    private Request $request;
    private Collection $selected;
    private array $value;
    private ?string $orderBy;
    private ?string $resource;
    private ?array $appends;

    public function __construct(Builder $query, string $trackBy, array $queryAttributes)
    {
        $this->query = $query;
        $this->trackBy = $trackBy;
        $this->queryAttributes = new Collection($queryAttributes);
    }

    public function toResponse($request)
    {
        $this->request = $request;

        return $this->resource
            ? $this->resource::collection($this->data())
            : $this->data();
    }

    public function resource(?string $resource): self
    {
        $this->resource = $resource;

        return $this;
    }

    public function appends(?array $appends): self
    {
        $this->appends = $appends;

        return $this;
    }

    private function data(): Collection
    {
        return $this->init()
            ->applyParams()
            ->applyPivotParams()
            ->selected()
            ->search()
            ->order()
            ->limit()
            ->get();
    }

    private function init(): self
    {
        $this->value = $this->request->has('value')
            ? (array) $this->request->get('value')
            : [];

        $attribute = $this->queryAttributes->first();
        $this->orderBy = $this->isNested($attribute) ? null : $attribute;

        return $this;
    }

    private function applyParams(): self
    {
        $this->params()->each(fn ($value, $column) => $this->query
            ->when($value === null, fn ($query) => $query->whereNull($column))
            ->when($value !== null, fn ($query) => $query->whereIn($column, (array) $value)));

        return $this;
    }

    private function applyPivotParams(): self
    {
        $this->pivotParams()->each(fn ($param, $relation) => $this->query
            ->whereHas($relation, fn ($query) => (new Collection($param))
                ->each(fn ($value, $attribute) => $query
                    ->whereIn($attribute, (array) $value))));

        return $this;
    }

    private function selected(): self
    {
        $this->selected = (clone $this->query)
            ->whereIn($this->trackBy, $this->value)
            ->get();

        return $this;
    }

    private function search(): self
    {
        $this->searchArguments()
            ->each(fn ($argument) => $this->query->where(
                fn ($query) => $this->matchArgument($query, $argument)
            ));

        return $this;
    }

    private function matchArgument(Builder $query, string $argument): void
    {
        $this->queryAttributes->each(fn ($attribute) => $query->orWhere(
            fn ($query) => $this->matchAttribute($query, $attribute, $argument)
        ));
    }

    private function matchAttribute(Builder $query, string $attribute, string $argument)
    {
        $nested = $this->isNested($attribute);

        $query->when($nested, fn ($query) => $this->matchSegments($query, $attribute, $argument))
            ->when(! $nested, fn ($query) => $query->where(
                $attribute,
                config('enso.select.comparisonOperator'),
                '%'.$argument.'%'
            ));
    }

    private function matchSegments(Builder $query, string $attribute, string $argument)
    {
        $attributes = (new Collection(explode('.', $attribute)));

        $query->whereHas(
            $attributes->shift(),
            fn ($query) => $this->matchAttribute(
                $query,
                $attributes->implode('.'),
                $argument
            )
        );
    }

    private function order(): self
    {
        $this->query->when(
            $this->orderBy !== null,
            fn ($query) => $query->orderBy($this->orderBy)
        );

        return $this;
    }

    private function limit(): self
    {
        $limit = $this->request->get('paginate')
            ?? self::Limit;

        $this->query->limit($limit);

        return $this;
    }

    private function get(): Collection
    {
        return $this->query->whereNotIn($this->trackBy, $this->value)
            ->get()
            ->merge($this->selected)
            ->when($this->orderBy !== null, fn ($results) => $results->sortBy($this->orderBy))
            ->values()
            ->when($this->appends, fn ($results) => $results->each
                ->setAppends($this->appends));
    }

    private function params(): Collection
    {
        return new Collection(json_decode($this->request->get('params')));
    }

    private function pivotParams(): Collection
    {
        return new Collection(json_decode($this->request->get('pivotParams')));
    }

    private function searchArguments(): Collection
    {
        return (new Collection(explode(' ', $this->request->get('query'))))->filter();
    }

    private function isNested($attribute): bool
    {
        return Str::contains($attribute, '.');
    }
}
