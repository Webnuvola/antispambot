# Antispambot
[![Latest Version on Packagist](https://img.shields.io/packagist/v/webnuvola/antispambot.svg?style=flat-square)](https://packagist.org/packages/webnuvola/antispambot)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/webnuvola/antispambot/Tests?label=tests)](https://github.com/webnuvola/antispambot/actions?query=workflow%3ATests+branch%3Amaster)
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

## Testing
```bash
composer test
```

## Credits
- [Fabio Cagliero](https://github.com/fab120)
- [WordPress](https://wordpress.org/)

## License
The MIT License (MIT). Please see [License File](LICENSE) for more information.
