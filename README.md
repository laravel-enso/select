<!--h-->
# Select
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/c6799b0705d34fdab5cd100e7cfe6312)](https://www.codacy.com/app/laravel-enso/Select?utm_source=github.com&utm_medium=referral&utm_content=laravel-enso/Select&utm_campaign=badger)
[![StyleCI](https://styleci.io/repos/85489940/shield?branch=master)](https://styleci.io/repos/85489940)
[![License](https://poser.pugx.org/laravel-enso/select/license)](https://https://packagist.org/packages/laravel-enso/select)
[![Total Downloads](https://poser.pugx.org/laravel-enso/select/downloads)](https://packagist.org/packages/laravel-enso/select)
[![Latest Stable Version](https://poser.pugx.org/laravel-enso/select/version)](https://packagist.org/packages/laravel-enso/select)
<!--/h-->

[Vue-multiselect](https://github.com/monterail/vue-multiselect) data builder with server-side data fetching capability and a VueJS component

[![Watch the demo](https://laravel-enso.github.io/select/screenshots/bulma_031.png)](https://laravel-enso.github.io/select/videos/bulma_demo_01.webm)

<sup>click on the photo to view a short demo in compatible browsers</sup>

### Features

- VueJS select component, integrated with Vue-multiselect
- permits getting the select options via ajax calls or takes them directly, as a parameter
- when getting the data via ajax, it can take various parameters for results filtering
- for the back-end, the packages comes with a trait for easy retrieval and formatting of the data 
as expected by the VueJS component
- CSS styling is in line with the [Laravel Enso](https://github.com/laravel-enso/Enso) style

### Installation Steps

1. The VueJS component is already included in the Enso install and should not require any additional installation steps

2. Use the `SelectListBuilder` trait in your desired Controller

3. Define a `getOptionList` route for the desired Controller (and permissions as required)

4. Declare inside your controller the `$selectSourceClass` variable as shown below:
	
	`protected $selectSourceClass = Model::class`
	
	where `Model::class` will be the Model from which the builder will extract the list of options
	
	By default it will use the `name` field for the select list option label and the `id` for the key.
	If you need another field from the model you can customize it by adding the `protected $selectAttribute = 'customAtrribute'` variable.

5. In your page/component add:

    ```
    <vue-select 
        source="/routeToController"        
        :selected="selectedOption"
        :params="params"
        :pivot-params="pivotParams"
        multiple
        :custom-params="customParams">
    </vue-select>
    ```

### Options

#### VueSelect VueJS component options 

In order to work the component needs a data source. The data source can be either an route for server-side, OR a formatted object.
In conclusion the component requires one of the two options `source` or `options` presented below:

- `value` - the selected option(s). Can be a single value or an Array if the select is used as a multi-select (optional)
- `source` - string, path to use when getting the select options **only for server-side**. 
The route for your controller, as the `getOptionList` suffix will be added under the hood
- `options` - object, list of options, **only where you don't need server-side**. Options must be properly formatted
- `keyMap` - 'number'/'string', flag that makes handling truthy evaluations easier depending on the type of the keys | default 'number' | (optional)  
- `disabled` - boolean, flag that sets the element as disabled | default false | (optional)
- `multiple` - boolean, flag that makes the element work as a multiselect | if omitted, the select acts as single select | (optional)
- `params` - object, attributes from the same table/model used for filtering results in server-side mode. 
Format: params: { 'fieldName': 'fieldValue' } | default null | (optional)
- `pivotParams` - object, attributes from linked tables/models used for filtering results in server-side mode. 
Format: pivotParams: { 'table': {'attribute':value} } | default null | (optional)
- `customParams` - object, can be anything. 
Using customParams implies that you rewrite the 'getOptionList' method from the SelectListBuilder Trait. 
- `placeholder` - custom placeholder when no option is selected | default 'Please choose' | (optional)
- `labels` - object, the labels used inside the component | default { selected: 'Selected', select: 'Press enter to select', deselect: 'Press enter to deselect', noResult: 'No Elements Found' } | (optional)

Note: `keyMap` might be deprecated in the future as it exists mostly because vue-multiselect doesn't handle zero (0) keys as expected.

#### SelectListBuilder trait options

- `$selectClass`, string, the fully qualified namespace of the class that we're querying on, in order to get the select options | default null | required
- `$selectAttributes`, string/array, the attribute / list of attributes we're searching in, when getting the select options | default 'name' | (optional) 
- `$displayAttribute`, string, the attributes that we're going to be using for the label of each option | default 'name' | (optional)
- `$selectQuery`, QueryBuilder, the query that we're using when querying for options | default null | (optional)

Note: If a query is given, it's going to get used, if it's not given, a query will be constructed, using the given class and other values.

### Publishes

Does not publish any files, as the VueJS component is bundled in the [VueComponents](https://github.com/laravel-enso/VueComponents) package

### Notes

You cannot use model computed attributes to display attributes when using the server-side mode of the select.

You can have the server-side route permissions generated automatically when creating permissions for a resource controller, from the System/permissions menu.

The [Laravel Enso Core](https://github.com/laravel-enso/Core) package comes with this package included.

<!--h-->
### Contributions

are welcome. Pull requests are great, but issues are good too.

### License

This package is released under the MIT license.
<!--/h-->