<?php

namespace Webnuvola\Antispambot;

class Antispambot
{
    /**
     * Converts email addresses characters to HTML entities to block spam bots.
     *
     * @see https://developer.wordpress.org/reference/functions/antispambot/
     */
    public static function antispambot(string $email_address, bool $hex_encoding = false): string
    {
        $email_no_spam_address = '';
        $random_max = $hex_encoding ? 2 : 1;

        for ($i = 0, $len = strlen($email_address); $i < $len; $i++) {
            $j = random_int(0, $random_max);
            if ($j === 0) {
                $email_no_spam_address .= '&#' . ord($email_address[$i]) . ';';
            } elseif ($j === 1) {
                $email_no_spam_address .= $email_address[$i];
            } elseif ($j === 2) {
                $email_no_spam_address .= '%' . self::zeroise(dechex(ord($email_address[$i])), 2);
            }
        }
        return str_replace('@', '&#64;', $email_no_spam_address);
    }

    /**
     * Add leading zeros when necessary.
     *
     * @see https://developer.wordpress.org/reference/functions/zeroise/
     */
    protected static function zeroise(int|string $number, int $threshold): string
    {
        return sprintf('%0' . $threshold . 's', $number);
    }
}
