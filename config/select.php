<?php

use LaravelEnso\Filters\Enums\ComparisonOperators;
use LaravelEnso\Filters\Enums\SearchModes;

return [
    /*
    |--------------------------------------------------------------------------
    | Query attributes
    |--------------------------------------------------------------------------
    | The default trackBy attribute needed by the fronted component. You
    | can override it by specifying a differnt value when using the
    | VueSelect / EnsoSelect in your template.
    */

    'trackBy' => 'id',

    /*
    |--------------------------------------------------------------------------
    | Query attributes
    |--------------------------------------------------------------------------
    | The default query attributes used for every select. You can override
    | it by adding a protected $queryAttributes property in the local
    | Options controller.
    */

    'queryAttributes' => ['name'],

    /*
    |--------------------------------------------------------------------------
    | SQL comparison operator
    |--------------------------------------------------------------------------
    | The comparison operator will be the default used for every select.
    | Possible values for comparison operator: LIKE, ILIKE
    */

    'comparisonOperator' => ComparisonOperators::Like,

    /*
    |--------------------------------------------------------------------------
    | Search Mode
    |--------------------------------------------------------------------------
    | Controls the global way in which wildcards are used in the query.
    | Can be customized for each select. Possible values for search mode:
    | SearchModes::Full, SearchModes::StartsWith, SearchModes::EndsWith
    */

    'searchMode' => SearchModes::Full,

    /*
    |--------------------------------------------------------------------------
    | Sort By Options
    |--------------------------------------------------------------------------
    | The sort by options used for every select. 
    | Possible options are SORT_REGULAR, SORT_NUMERIC, SORT_STRING, 
    | SORT_NATURAL, SORT_FLAG_CASE, ...
    | Ex : Case-insensitive sorting : SORT_NATURAL|SORT_FLAG_CASE
    | @link https://php.net/manual/en/array.constants.php
    */

    'sortByOptions' => SORT_REGULAR,
];
