# Antispambot
[![Latest Version on Packagist](https://img.shields.io/packagist/v/webnuvola/antispambot.svg?style=flat-square)](https://packagist.org/packages/webnuvola/antispambot)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/webnuvola/antispambot/tests.yml?branch=main&style=flat-square)](https://github.com/Webnuvola/antispambot/actions?query=branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/webnuvola/antispambot.svg?style=flat-square)](https://packagist.org/packages/webnuvola/antispambot)

This package allows you to use the `antispambot` function outside of WordPress.

## Installation
You can install the package via composer:

```bash
composer require webnuvola/antispambot
```

## Usage
```php
echo antispambot('demo@example.com');
// Example output: <a href="mailto:dem&#111;&#64;exa&#109;p&#108;&#101;.&#99;o&#109;">de&#109;&#111;&#64;e&#120;ample&#46;&#99;om</a>
// Example output decoded: <a href="mailto:demo@example.com">demo@example.com</a>

echo antispambot('demo@example.com', 'Contact us');
// Example output: <a href="mailto:&#105;&#110;fo&#64;ex&#97;&#109;&#112;&#108;e&#46;co&#109;">Contact us</a>

echo antispambot('demo@example.com', 'Contact us', ['class' => 'text-white', 'target' => '_blank']);
// Example output: <a href="mailto:&#105;&#110;&#102;o&#64;e&#120;&#97;&#109;ple.&#99;&#111;&#109;" class="text-white" target="_blank">Contact us</a>

echo \Webnuvola\Antispambot\Antispambot::antispambot('demo@example.com');
// Example output: &#100;&#101;&#109;&#111;&#64;e&#120;a&#109;pl&#101;&#46;co&#109;
```

Use `antispambot_html` function to return an instance of `Illuminate\Support\HtmlString` to use with Laravel framework.

## Documentation
`antispambot` on [WordPress Developer Resources](https://developer.wordpress.org/reference/functions/antispambot/).

## Testing
```bash
composer test
```

## Credits
- [WordPress](https://wordpress.org/)
- [Fabio Cagliero](https://github.com/fab120)

## License
This software is release under GPL v2.  Please see [LICENSE](LICENSE) file for more information.
