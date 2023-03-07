# Antispambot
[![Latest Version on Packagist](https://img.shields.io/packagist/v/webnuvola/antispambot.svg?style=flat-square)](https://packagist.org/packages/webnuvola/antispambot)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/webnuvola/antispambot/run-tests.yml?branch=main)](https://github.com/Webnuvola/antispambot/actions?query=branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/webnuvola/antispambot.svg?style=flat-square)](https://packagist.org/packages/webnuvola/antispambot)

This package allows you to use the `antispambot` function outside of WordPress.

## Installation
You can install the package via composer:

```bash
composer require webnuvola/antispambot
```

## Usage
```php
echo antispambot('info@example.com');
```

## Documentation
[WordPress Developer Resources](https://developer.wordpress.org/reference/functions/antispambot/)

## Testing
```bash
composer test
```

## Credits
- [WordPress](https://wordpress.org/)
- [Fabio Cagliero](https://github.com/fab120)

## License
This software is release under GPL v2.  Please see [LICENSE](LICENSE) file for more information.
