# hamburgscleanest/data-tables

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Data tables whose behavior and appearance can be extended by components.
For example sorting, pagination or searching the table. 
No JavaScript is required.


## Install

Via Composer

``` bash
$ composer require hamburgscleanest/data-tables
```

Add the service provider to your providers array
``` php
    'providers' => [
                
                ...
           
            DataTablesServiceProvider::class,
        ],
```

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
Just create a new Component and add it to the table.

### Adding pagination

``` php

    /** @var Paginator $paginator */
    $paginator = new Paginator();
    
    $dataTable->addComponent($paginator);
```

### Rendering the pagination in your view (page links)

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

Add sorting to the table via a data component and make your table headers sortable with a header formatter.

``` php   
    ...
    
     $dataTable
        ->addComponent(new Sorter)
        ->formatHeaders(new SortableHeader(['name']));
    
    ...
```

## Changes

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

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
