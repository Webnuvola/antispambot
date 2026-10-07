<?php

use Illuminate\Support\HtmlString;
use Webnuvola\Antispambot\Antispambot;

if (! function_exists('antispambot')) {
    /**
     * Obscures email addresses in HTML to prevent spam bots from harvesting them.
     */
    function antispambot(string $email, string $text = '', array $attributes = []): string
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return '';
        }

        if (! $text) {
            $text = Antispambot::antispambot($email);
        }

        $attributeString = '';
        foreach ($attributes as $key => $value) {
            $attributeString .= sprintf(' %s="%s"', $key, htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
        }

        return sprintf('<a href="mailto:%s"%s>%s</a>', Antispambot::antispambot($email), $attributeString, $text);
    }
}

if (! function_exists('antispambot_html')) {
    /**
     * Obscures email addresses in HTML to prevent spam bots from harvesting them.
     * Return the result as an instance of HtmlString.
     */
    function antispambot_html(string $email, string $text = '', array $attributes = []): HtmlString
    {
        return new HtmlString(antispambot($email, $text, $attributes));
    }
}
