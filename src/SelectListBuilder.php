<?php

namespace LaravelEnso\Select;

class SelectListBuilder
{

    private $attribute; // 'name' => optional
    private $pivotParams; // ['column' => 'table'] => optional
    private $class; // 'App\Model' => required
    private $query;

    public function __construct($class, $attribute, $pivotParams)
    {
        $this->class       = $class;
        $this->attribute   = $attribute;
        $this->pivotParams = $pivotParams;
        $this->query       = $class::query();
    }

    public function getOptionsList()
    {
        $ids = (array) request('selected');

        if (request('customParams')) {

            $this->processPivotParams();
        }

        $models   = $this->query->where($this->attribute, 'like', '%' . request('query') . '%')
                        ->orderBy($this->attribute)->limit(10)->get();
        $selected = $this->query->whereIn('id', $ids)->get();
        $result   = $models->merge($selected)->pluck('name', 'id');
        $response = static::buildSelectList($result);

        return $response;
    }

    private function processPivotParams()
    {
        $customParams = json_decode(request('customParams'));

        foreach ($customParams as $key => $value) {

            if (!in_array($key, array_keys($this->pivotParams))) {

                $this->query = $this->query->where($key, $value);
            } else {

                $this->query = $this->query->whereHas($this->pivotParams[$key], function ($query) use ($value) {

                    $this->query->whereId($value);
                });
            }
        }
    }

    public static function buildSelectList($data)
    {
        $response = [];

        foreach ($data as $key => $value) {

            $response[] = [

                'key'   => $key,
                'value' => $value,
            ];
        }

        return json_encode($response);
    }
}
