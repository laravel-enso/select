# Select
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/c6799b0705d34fdab5cd100e7cfe6312)](https://www.codacy.com/app/laravel-enso/Select?utm_source=github.com&utm_medium=referral&utm_content=laravel-enso/Select&utm_campaign=badger)
[![StyleCI](https://styleci.io/repos/85489940/shield?branch=master)](https://styleci.io/repos/85489940)
[![License](https://poser.pugx.org/laravel-enso/select/license)](https://packagist.org/packages/laravel-enso/select)
[![Total Downloads](https://poser.pugx.org/laravel-enso/select/downloads)](https://packagist.org/packages/laravel-enso/select)
[![Latest Stable Version](https://poser.pugx.org/laravel-enso/select/version)](https://packagist.org/packages/laravel-enso/select)

Bulma styled single and multi-select VueJS component with a server-side option list builder

[![Watch the demo](https://laravel-enso.github.io/select/screenshots/bulma_031.png)](https://laravel-enso.github.io/select/videos/bulma_demo_01.mp4)

<sup>click on the photo to view a short demo in compatible browsers</sup>

### Features

- a standalone component with minimal dependencies
- minimal CSS styling that matches the beautiful [Bulma](https://bulma.io/) forms design
- the select options can be retrieved via ajax calls or, given directly, via a parameter
- when getting the data via ajax, the component can take various parameters for results filtering
- for the back-end, the package comes with a trait for easy retrieval and formatting of the data 
as expected by the VueJS component
- can filter the option list dynamically even based on the model’s one-to-many / many-to-many relationships
- can search in multiple attributes of a model
- can specify the attribute used as label for the select options
- can be used to create a new 'tag' if no suitable result is found (soon)
- can use the arrow keys to navigate the list of results and Enter to select/deselect 
- is as small as can be, without skimping on features

### Configuration & Usage

Be sure to check out the full documentation for this package available at [docs.laravel-enso.com](https://docs.laravel-enso.com/packages/select.html)

### Contributions

are welcome. Pull requests are great, but issues are good too.

### License

This package is released under the MIT license.
