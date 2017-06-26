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

## Documentation
For detailed information about the usage and the behaviour of the data tables visit our wiki.\
https://github.com/hamburgscleanest/data-tables/wiki

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
