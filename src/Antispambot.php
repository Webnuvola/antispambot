<?php

namespace Webnuvola\Antispambot;

/**
 * Based on wordpress function antispambot.
 *
 * @see https://codex.wordpress.org/Function_Reference/antispambot
 */
class Antispambot
{
    /**
     * Converts email addresses characters to HTML entities to block spam bots.
     *
     * @param  string $email_address
     * @param  bool $hex_encoding
     * @return string
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
     * If you set the threshold to '4' and the number is '10', then you will get
     * back '0010'. If you set the threshold to '4' and the number is '5000', then you
     * will get back '5000'.
     *
     * Uses sprintf to append the amount of zeros based on the $threshold parameter
     * and the size of the number. If the number is large enough, then no zeros will
     * be appended.
     *
     * @param  int|string $number
     * @param  int $threshold
     * @return string
     */
    protected static function zeroise($number, int $threshold): string
    {
        return sprintf('%0' . $threshold . 's', $number);
    }
}
