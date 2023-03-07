<?php

use Illuminate\Support\HtmlString;
use Webnuvola\Antispambot\Antispambot;

if (! function_exists('antispambot')) {
    /**
     * Hide email from Spam Bots.
     *
     * @param  string $email
     * @param  string $text
     * @param  array $attributes
     * @return string
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
     * Hide email from Spam Bots. Return instance of HtmlString.
     *
     * @param  string $email
     * @param  string $text
     * @param  array $attributes
     * @return \Illuminate\Support\HtmlString
     */
    function antispambot_html(string $email, string $text = '', array $attributes = []): HtmlString
    {
        return new HtmlString(antispambot($email, $text, $attributes));
    }
}
