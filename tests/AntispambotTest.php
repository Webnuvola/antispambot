<?php

namespace Webnuvola\Antispambot\Tests;

use PHPUnit\Framework\TestCase;
use Webnuvola\Antispambot\Antispambot;

final class AntispambotTest extends TestCase
{
    protected static $emails = [
        'info@example.com',
        'dot.in.name@example.com',
        'info@subdomain.example.com',
        'test+plus@example.com',
        'another+plus+test@example.com',
    ];

    public function testEmailObfuscation()
    {
        foreach (self::$emails as $email) {
            $antispam = Antispambot::antispambot($email);
            $this->assertNotEquals($email, $antispam);
            $this->assertEquals($email, html_entity_decode($antispam, ENT_HTML5));
        }
    }

    public function testEmailObfuscationWithHexEncoding()
    {
        foreach (self::$emails as $email) {
            $antispam = Antispambot::antispambot($email, true);
            $this->assertNotEquals($email, $antispam);
            $antispam = str_replace('+', '&#43;', $antispam);
            $this->assertEquals($email, html_entity_decode(urldecode($antispam), ENT_HTML5));
        }
    }
}
