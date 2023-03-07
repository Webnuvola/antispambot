<?php

use Webnuvola\Antispambot\Antispambot;

if (! function_exists('antispambot')) {
    /**
     * Hide email from Spam Bots using a shortcode.
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
