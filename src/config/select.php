<?php

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

    'comparisonOperator' => 'LIKE',
];
