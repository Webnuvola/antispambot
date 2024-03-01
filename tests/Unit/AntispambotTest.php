<?php

use Illuminate\Support\HtmlString;
use Webnuvola\Antispambot\Antispambot;

function decode_email(string $encodedEmail): string
{
    return html_entity_decode($encodedEmail, ENT_HTML5);
}

it('can obfuscate email', function (string $email) {
    $antispambot = Antispambot::antispambot($email);

    expect($antispambot)
        ->not->toBe($email)
        ->and(decode_email($antispambot))->toBe($email);
})->with('emails');

it('can obfuscate email with hex encoding', function (string $email) {
    $antispambot = Antispambot::antispambot($email, true);

    expect($antispambot)
        ->not->toBe($email)
        ->and(html_entity_decode(urldecode(str_replace('+', '&#43;', $antispambot)), ENT_HTML5))->toBe($email);
})->with('emails');

test('antispambot function return value', function (string $email) {
    $result = antispambot($email);
    $return = preg_match('/"([^"]+)"/', $result, $matches);
    expect($return)
        ->toBe(1)
        ->and(decode_email($matches[1]))->toBe('mailto:' . $email)
        ->and(decode_email(strip_tags($result)))->toBe($email);

    $result = antispambot($email, 'Contact us');
    $return = preg_match('/"([^"]+)"/', $result, $matches);
    expect($return)
        ->toBe(1)
        ->and(decode_email($matches[1]))->toBe('mailto:' . $email)
        ->and(strip_tags($result))->toBe('Contact us');

    $result = antispambot($email, 'Contact us', ['class' => 'text-white', 'target' => '_blank']);
    $return = preg_match('/"([^"]+)"/', $result, $matches);
    expect($return)
        ->toBe(1)
        ->and(decode_email($matches[1]))->toBe('mailto:' . $email)
        ->and(strip_tags($result))->toBe('Contact us')
        ->and(str_contains($result, 'class="text-white"'))->toBeTrue()
        ->and(str_contains($result, 'target="_blank"'))->toBeTrue();
})->with(['demo@example.com']);

test('antispambot_html function return value', function (string $email) {
    $antispambotHtml = antispambot_html($email);

    expect($antispambotHtml)
        ->toBeInstanceOf(HtmlString::class)
        ->and(decode_email(strip_tags((string) $antispambotHtml)))->toBe($email);
})->with(['demo@example.com']);
