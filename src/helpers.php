<?php

use Webnuvola\Antispambot\Antispambot;

if (! function_exists('antispambot')) {
    /**
     * Hide email from Spam Bots using a shortcode.
     *
     * @param  string $email
     * @param  string $text
     * @return string
     */
    function antispambot(string $email, string $text = null): string
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return '';
        }

        if (! $text) {
            $text = Antispambot::antispambot($email);
        }

        return sprintf('<a href="mailto:%s">%s</a>', Antispambot::antispambot($email), $text);
    }
}
