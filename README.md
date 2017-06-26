# hamburgscleanest/data-tables

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Data tables whose behavior and appearance can be extended by components.
For example sorting, paginating or filtering the table. 
No JavaScript is required.


## Install

Via Composer

``` bash
$ composer require hamburgscleanest/data-tables
```

### Laravel < 5.5.x

Add the service provider to your providers array
``` php
    'providers' => [
                
                ...
           
            DataTablesServiceProvider::class,
        ],
```

### Laravel >= 5.5.x 

`Automatic Package Discovery`
Everything is automatically registered for you when using Laravel 5.5.x or later.

## Usage

### Creating a simple table

``` php
    /** @var \hamburgscleanest\DataTables\Models\DataTable $dataTable */
    $dataTable = DataTable::model(User::class, ['created_at', 'name']);
```

### Alternative way to specify the columns

``` php
    /** @var \hamburgscleanest\DataTables\Models\DataTable $dataTable */
    $dataTable = DataTable::model(User::class)->columns(['created_at', 'name']);
```

### Rendering the table in your view

``` php
    ...
    
    {!! $dataTable->render() !!}
    
    ...
```

## Extending behaviour

You can extend the behaviour of the table via data components.
Just create a new component and add it to the table.

All added components can be accessed directly on the DataTable class via their classnames.
Optionally you can pass a name for better identification or using multiple components of the same class.

**Example 1 - Adding a component without a name:**

```php
$dataTable->addComponent(new DataComponent());
```

Render it in the view: `$dataTable->component->render()`

**Example 2 - Adding a component with a name:**

```php
$dataTable->addComponent(new Component(), 'my shiny component')
```

Render it in the view: `$dataTable->myshinycomponent->render()`

### Adding pagination

``` php

    /** @var Paginator $paginator */
    $paginator = new Paginator();
    
    $dataTable->addComponent($paginator);
```

### Rendering the pagination in your view (page links)

#### Render via DataTable property
Passing all of your components to your blade view via variables can be pretty wiry. 
To keep your code clean use the DataTables properties.
``` php   
    ...
    
    {!! $dataTable->paginator->render() !!}
    
    ...
```

#### Render via passed variable

Of course you can pass your components to your view like always 
via `view('my_view', ['paginator' => $paginator]);` and render it in your blade file.
``` php   
    ...
    
    {!! $paginator->render() !!}
    
    ...
```

## Altering appearance

### Modifying table headers, e.g. translating headers

``` php   
    ...
    
     $dataTable->formatHeaders(new TranslateHeader(trans('my.translations')));
    
    ...
```

### Formatting columns

Format columns via column formatters. 
For example you could format a column containing a date with the "DateColumn" formatter.

``` php   
    ...
    
     $dataTable->formatColumn('created_at', new DateColumn('d.m.Y'));
    
    ...
```

It is also possible to define the formatters in the data table constructor.

``` php   
    ...
    
     DataTable::model(User::class, ['created_at' => new DateColumn('d.m.Y')]);
    
    ...
```

Or even the "columns" function.

``` php   
    ...
    
     DataTable::model(User::class)->columns(['created_at' => new DateColumn('d.m.Y')]);
    
    ...
```

## Combine components and formatters

You can combine any number of components and formatters to get your desired results. Mix all the data components, header formatters and column formatters you need.

For example: Add sorting to the table with the "Sorter" data component and make your table headers sortable with the "SortableHeader" header formatter.

``` php   
    ...
    
     $dataTable
        ->addComponent(new Sorter)
        ->formatHeaders(new SortableHeader(['name']));
    
    ...
```

## Query relations

Access column values from "hasOne" and "belongsTo" relationships.

``` php       
     DataTable::model(TestModel::class)->columns(['relation.name'])->with(['relation']);
```

Or access column values from "hasMany" and "belongsToMany" relationships by using an aggregate function.
If you omit the aggregate function, the value of the first related record is returned.

``` php       
     DataTable::model(TestModel::class)->columns(['COUNT(relation.id)'])->with(['relation']);
```

## Cache the table data

You can cache the retrieved table data for a certain amount of minutes.
It is automatically stored with the driver defined in your .env file.

Of course you can write your own caching implementation. Just implement the `hamburgscleanest\DataTables\Models\Cache\Cache` interface.

``` php       
     DataTable::model(TestModel::class, $columns, new SimpleCache($minutes));
     // or
     $dataTable->cache(new SimpleCache(1440));
```

## Changes

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email chroma91@gmail.com instead of using the issue tracker.

## Credits

- [Timo Prüße][link-author]
- [Andre Biel][link-andre]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/hamburgscleanest/data-tables.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/hamburgscleanest/data-tables/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/hamburgscleanest/data-tables.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/hamburgscleanest/data-tables.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/hamburgscleanest/data-tables.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/hamburgscleanest/data-tables
[link-travis]: https://travis-ci.org/hamburgscleanest/data-tables
[link-scrutinizer]: https://scrutinizer-ci.com/g/hamburgscleanest/data-tables/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/hamburgscleanest/data-tables
[link-downloads]: https://packagist.org/packages/hamburgscleanest/data-tables
[link-author]: https://github.com/Chroma91
[link-andre]: https://github.com/karllson
[link-contributors]: ../../contributors
