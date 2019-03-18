# Select

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/c6799b0705d34fdab5cd100e7cfe6312)](https://www.codacy.com/app/laravel-enso/Select?utm_source=github.com&utm_medium=referral&utm_content=laravel-enso/Select&utm_campaign=badger)
[![StyleCI](https://styleci.io/repos/85489940/shield?branch=master)](https://styleci.io/repos/85489940)
[![License](https://poser.pugx.org/laravel-enso/select/license)](https://packagist.org/packages/laravel-enso/select)
[![Total Downloads](https://poser.pugx.org/laravel-enso/select/downloads)](https://packagist.org/packages/laravel-enso/select)
[![Latest Stable Version](https://poser.pugx.org/laravel-enso/select/version)](https://packagist.org/packages/laravel-enso/select)

Single and multi-select server-side option list builder

This package can work independently of the [Enso](https://github.com/laravel-enso/Enso) ecosystem.

The front end assets that utilize this api are present in the [select](https://github.com/enso-ui/select) package.

For live examples and demos, you may visit [laravel-enso.com](https://www.laravel-enso.com)

[![Watch the demo](https://laravel-enso.github.io/select/screenshots/bulma_031.png)](https://laravel-enso.github.io/select/videos/bulma_demo_01.mp4)

<sup>click on the photo to view a short demo in compatible browsers</sup>

## Installation

Comes pre-installed in Enso.

To install outside of Enso:

1. install the package: `composer require laravel-enso/select`
2. install the front end api implementation: `yarn add @enso-ui/select`

## Features

- a standalone component with minimal dependencies
- the select options can be retrieved via ajax calls or, given directly, via a parameter
- when getting the data via ajax, the component can take various parameters for results filtering
- for the back-end, the package comes with a trait for easy retrieval and formatting of the data 
- can filter the option list dynamically even based on the model’s one-to-many / many-to-many relationships
- can search in multiple attributes of a model, and the attribute(s) may be nested
- can specify the attribute used as label for the select options
- can be used to create a new 'tag' if no suitable result is found (soon)
- can use the arrow keys to navigate the list of results and Enter to select/deselect 
- is as small as can be, without skimping on features

### Configuration & Usage

Be sure to check out the full documentation for this package available at [docs.laravel-enso.com](https://docs.laravel-enso.com/backend/select.html)

### Contributions

are welcome. Pull requests are great, but issues are good too.

### License

This package is released under the MIT license.
