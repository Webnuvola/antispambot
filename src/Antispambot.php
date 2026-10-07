<?php

namespace Webnuvola\Antispambot;

class Antispambot
{
    /**
     * Obscures email addresses in HTML to prevent spam bots from harvesting them.
     *
     * @see https://developer.wordpress.org/reference/functions/antispambot/
     *
     * @version 7.1.3
     */
    public static function antispambot(string $email_address, bool $hex_encoding = false): string
    {
        $obfuscated     = '';
        $at             = 0;
        $end            = strlen( $email_address );
        $invalid_length = 0;

        $max_rand = $hex_encoding ? 1 : 0;

        while ( $at < $end ) {
            $was_at = $at;
            if (
                0 === self::_wp_scan_utf8( $email_address, $at, $invalid_length, null, 1 ) &&
                0 === $invalid_length
            ) {
                break;
            }

            $character_length = $at - $was_at;

            if ( $character_length > 0 ) {
                $character = substr( $email_address, $was_at, $character_length );

                switch ( rand( 0, $max_rand ) ) {
                    case 0:
                        $code_point  = mb_ord( $character );
                        $obfuscated .= "&#{$code_point};";
                        break;

                    case 1:
                        $obfuscated .= $character;
                        break;

                    case 2:
                        for ( $i = 0; $i < $character_length; $i++ ) {
                            $hex_value   = bin2hex( $character[ $i ] );
                            $obfuscated .= "%{$hex_value}";
                        }
                        break;
                }
            }

            if ( 0 !== $invalid_length ) {
                $obfuscated .= substr( $email_address, $at, $invalid_length );
            }

            $at += $invalid_length;
        }

        return str_replace( '@', '&#64;', $obfuscated );
    }

    /**
     * Finds spans of valid and invalid UTF-8 bytes in a given string.
     *
     * @see https://developer.wordpress.org/reference/functions/_wp_scan_utf8/
     *
     * @version 7.1.3
     */
    public static function _wp_scan_utf8( string $bytes, int &$at, int &$invalid_length, ?int $max_bytes = null, ?int $max_code_points = null, ?bool &$has_noncharacters = null ): int {
        $byte_length       = strlen( $bytes );
        $end               = min( $byte_length, $at + ( $max_bytes ?? PHP_INT_MAX ) );
        $invalid_length    = 0;
        $count             = 0;
        $max_count         = $max_code_points ?? PHP_INT_MAX;
        $has_noncharacters = false;

        for ( $i = $at; $i < $end && $count <= $max_count; $i++ ) {
            /*
             * Quickly skip past US-ASCII bytes, all of which are valid UTF-8.
             *
             * This optimization step improves the speed from 10x to 100x
             * depending on whether the JIT has optimized the function.
             */
            $ascii_byte_count = strspn(
                $bytes,
                "\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c\x0d\x0e\x0f" .
                "\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1a\x1b\x1c\x1d\x1e\x1f" .
                " !\"#$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_`abcdefghijklmnopqrstuvwxyz{|}~\x7f",
                $i,
                min( $end - $i, $max_count - $count )
            );

            if ( $count + $ascii_byte_count >= $max_count ) {
                $at    = $i + ( $max_count - $count );
                $count = $max_count;
                return $count;
            }

            $count += $ascii_byte_count;
            $i     += $ascii_byte_count;

            if ( $i >= $end ) {
                $at = $end;
                return $count;
            }

            /**
             * The above fast-track handled all single-byte UTF-8 characters. What
             * follows MUST be a multibyte sequence otherwise there’s invalid UTF-8.
             *
             * Therefore everything past here is checking those multibyte sequences.
             *
             * It may look like there’s a need to check against the max bytes here,
             * but since each match of a single character returns, this functions will
             * bail already if crossing the max-bytes threshold. This function SHALL
             * NOT return in the middle of a multi-byte character, so if a character
             * falls on each side of the max bytes, the entire character will be scanned.
             *
             * Because it’s possible that there are truncated characters, the use of
             * the null-coalescing operator with "\xC0" is a convenience for skipping
             * length checks on every continuation bytes. This works because 0xC0 is
             * always invalid in a UTF-8 string, meaning that if the string has been
             * truncated, it will find 0xC0 and reject as invalid UTF-8.
             *
             * > [The following table] lists all of the byte sequences that are well-formed
             * > in UTF-8. A range of byte values such as A0..BF indicates that any byte
             * > from A0 to BF (inclusive) is well-formed in that position. Any byte value
             * > outside of the ranges listed is ill-formed.
             *
             * > Table 3-7. Well-Formed UTF-8 Byte Sequences
             *  ╭─────────────────────┬────────────┬──────────────┬─────────────┬──────────────╮
             *  │ Code Points         │ First Byte │ Second Byte  │ Third Byte  │ Fourth Byte  │
             *  ├─────────────────────┼────────────┼──────────────┼─────────────┼──────────────┤
             *  │ U+0000..U+007F      │ 00..7F     │              │             │              │
             *  │ U+0080..U+07FF      │ C2..DF     │ 80..BF       │             │              │
             *  │ U+0800..U+0FFF      │ E0         │ A0..BF       │ 80..BF      │              │
             *  │ U+1000..U+CFFF      │ E1..EC     │ 80..BF       │ 80..BF      │              │
             *  │ U+D000..U+D7FF      │ ED         │ 80..9F       │ 80..BF      │              │
             *  │ U+E000..U+FFFF      │ EE..EF     │ 80..BF       │ 80..BF      │              │
             *  │ U+10000..U+3FFFF    │ F0         │ 90..BF       │ 80..BF      │ 80..BF       │
             *  │ U+40000..U+FFFFF    │ F1..F3     │ 80..BF       │ 80..BF      │ 80..BF       │
             *  │ U+100000..U+10FFFF  │ F4         │ 80..8F       │ 80..BF      │ 80..BF       │
             *  ╰─────────────────────┴────────────┴──────────────┴─────────────┴──────────────╯
             *
             * @see https://www.unicode.org/versions/Unicode16.0.0/core-spec/chapter-3/#G27506
             */

            // Valid two-byte code points.
            $b1 = ord( $bytes[ $i ] );
            $b2 = ord( $bytes[ $i + 1 ] ?? "\xC0" );

            if ( $b1 >= 0xC2 && $b1 <= 0xDF && $b2 >= 0x80 && $b2 <= 0xBF ) {
                ++$count;
                ++$i;
                continue;
            }

            // Valid three-byte code points.
            $b3 = ord( $bytes[ $i + 2 ] ?? "\xC0" );

            if ( $b3 < 0x80 || $b3 > 0xBF ) {
                goto invalid_utf8;
            }

            if (
                ( 0xE0 === $b1 && $b2 >= 0xA0 && $b2 <= 0xBF ) ||
                ( $b1 >= 0xE1 && $b1 <= 0xEC && $b2 >= 0x80 && $b2 <= 0xBF ) ||
                ( 0xED === $b1 && $b2 >= 0x80 && $b2 <= 0x9F ) ||
                ( $b1 >= 0xEE && $b1 <= 0xEF && $b2 >= 0x80 && $b2 <= 0xBF )
            ) {
                ++$count;
                $i += 2;

                // Covers the range U+FDD0–U+FDEF, U+FFFE, U+FFFF.
                if ( 0xEF === $b1 ) {
                    $has_noncharacters |= (
                        ( 0xB7 === $b2 && $b3 >= 0x90 && $b3 <= 0xAF ) ||
                        ( 0xBF === $b2 && ( 0xBE === $b3 || 0xBF === $b3 ) )
                    );
                }

                continue;
            }

            // Valid four-byte code points.
            $b4 = ord( $bytes[ $i + 3 ] ?? "\xC0" );

            if ( $b4 < 0x80 || $b4 > 0xBF ) {
                goto invalid_utf8;
            }

            if (
                ( 0xF0 === $b1 && $b2 >= 0x90 && $b2 <= 0xBF ) ||
                ( $b1 >= 0xF1 && $b1 <= 0xF3 && $b2 >= 0x80 && $b2 <= 0xBF ) ||
                ( 0xF4 === $b1 && $b2 >= 0x80 && $b2 <= 0x8F )
            ) {
                ++$count;
                $i += 3;

                // Covers U+1FFFE, U+1FFFF, U+2FFFE, U+2FFFF, …, U+10FFFE, U+10FFFF.
                $has_noncharacters |= (
                    ( 0x0F === ( $b2 & 0x0F ) ) &&
                    0xBF === $b3 &&
                    ( 0xBE === $b4 || 0xBF === $b4 )
                );

                continue;
            }

            /**
             * When encountering invalid byte sequences, Unicode suggests finding the
             * maximal subpart of a text and replacing that subpart with a single
             * replacement character.
             *
             * > This practice is more secure because it does not result in the
             * > conversion consuming parts of valid sequences as though they were
             * > invalid. It also guarantees at least one replacement character will
             * > occur for each instance of an invalid sequence in the original text.
             * > Furthermore, this practice can be defined consistently for better
             * > interoperability between different implementations of conversion.
             *
             * @see https://www.unicode.org/versions/Unicode16.0.0/core-spec/chapter-5/#G40630
             */
            invalid_utf8:
            $at             = $i;
            $invalid_length = 1;

            // Single-byte and two-byte characters.
            if ( ( 0x00 === ( $b1 & 0x80 ) ) || ( 0xC0 === ( $b1 & 0xE0 ) ) ) {
                return $count;
            }

            $b2 = ord( $bytes[ $i + 1 ] ?? "\xC0" );
            $b3 = ord( $bytes[ $i + 2 ] ?? "\xC0" );

            // Find the maximal subpart and skip past it.
            if ( 0xE0 === ( $b1 & 0xF0 ) ) {
                // Three-byte characters.
                $b2_valid = (
                    ( 0xE0 === $b1 && $b2 >= 0xA0 && $b2 <= 0xBF ) ||
                    ( $b1 >= 0xE1 && $b1 <= 0xEC && $b2 >= 0x80 && $b2 <= 0xBF ) ||
                    ( 0xED === $b1 && $b2 >= 0x80 && $b2 <= 0x9F ) ||
                    ( $b1 >= 0xEE && $b1 <= 0xEF && $b2 >= 0x80 && $b2 <= 0xBF )
                );

                $invalid_length = min( $end - $i, $b2_valid ? 2 : 1 );
                return $count;
            } elseif ( 0xF0 === ( $b1 & 0xF8 ) ) {
                // Four-byte characters.
                $b2_valid = (
                    ( 0xF0 === $b1 && $b2 >= 0x90 && $b2 <= 0xBF ) ||
                    ( $b1 >= 0xF1 && $b1 <= 0xF3 && $b2 >= 0x80 && $b2 <= 0xBF ) ||
                    ( 0xF4 === $b1 && $b2 >= 0x80 && $b2 <= 0x8F )
                );

                $b3_valid = $b3 >= 0x80 && $b3 <= 0xBF;

                $invalid_length = min( $end - $i, $b2_valid ? ( $b3_valid ? 3 : 2 ) : 1 );
                return $count;
            }

            return $count;
        }

        $at = $i;
        return $count;
    }
}
