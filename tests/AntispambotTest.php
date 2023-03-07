<?php

namespace Webnuvola\Antispambot\Tests;

use PHPUnit\Framework\TestCase;
use Webnuvola\Antispambot\Antispambot;

use function antispambot;

final class AntispambotTest extends TestCase
{
    protected static array $emails = [
        'info@example.com',
        'dot.in.name@example.com',
        'info@subdomain.example.com',
        'test+plus@example.com',
        'another+plus+test@example.com',
    ];

    public function testEmailObfuscation(): void
    {
        foreach (self::$emails as $email) {
            $antispam = Antispambot::antispambot($email);
            $this->assertNotEquals($email, $antispam);
            $this->assertEquals($email, $this->decodeEmail($antispam));
        }
    }

    public function testEmailObfuscationWithHexEncoding(): void
    {
        foreach (self::$emails as $email) {
            $antispam = Antispambot::antispambot($email, true);
            $this->assertNotEquals($email, $antispam);
            $antispam = str_replace('+', '&#43;', $antispam);
            $this->assertEquals($email, html_entity_decode(urldecode($antispam), ENT_HTML5));
        }
    }

    public function testAntispambotFunction(): void
    {
        $email = 'demo@example.com';

        $result = antispambot($email);
        $return = preg_match('/"([^"]+)"/', $result, $matches);
        $this->assertEquals(1, $return);
        $this->assertEquals('mailto:' . $email, $this->decodeEmail($matches[1]));
        $this->assertEquals($email, $this->decodeEmail(strip_tags($result)));

        $result = antispambot($email, 'Contact us');
        $return = preg_match('/"([^"]+)"/', $result, $matches);
        $this->assertEquals(1, $return);
        $this->assertEquals('mailto:' . $email, $this->decodeEmail($matches[1]));
        $this->assertEquals('Contact us', strip_tags($result));

        $result = antispambot($email, 'Contact us', ['class' => 'text-white', 'target' => '_blank']);
        $return = preg_match('/"([^"]+)"/', $result, $matches);
        $this->assertEquals(1, $return);
        $this->assertEquals('mailto:' . $email, $this->decodeEmail($matches[1]));
        $this->assertEquals('Contact us', strip_tags($result));
        $this->assertNotEquals(false, strpos($result, 'class="text-white"'));
        $this->assertNotEquals(false, strpos($result, 'target="_blank"'));
    }

    protected function decodeEmail(string $encodedEmail): string
    {
        return html_entity_decode($encodedEmail, ENT_HTML5);
    }
}
