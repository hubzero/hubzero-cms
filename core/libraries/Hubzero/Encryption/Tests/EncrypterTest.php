<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Encryption\Tests;

use Hubzero\Test\Basic;
use Hubzero\Encryption\Encrypter;
use Hubzero\Encryption\Cipher\Simple;
use Hubzero\Encryption\Key;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Encrypter class tests
 */
class EncrypterTest extends Basic
{
    /**
     * Data provider for random bytes length tests
     *
     * @return  Generator
     */
    public static function seedRandomBytesLengths(): Generator
    {
        yield 'length 8' => [8];
        yield 'length 16 (default)' => [16];
        yield 'length 32' => [32];
        yield 'length 64' => [64];
        yield 'length 128' => [128];
        yield 'length 256' => [256];
    }

    /**
     */
    #[DataProvider('seedRandomBytesLengths')]
    public function testGenRandomBytesLength(int $length): void
    {
        $bytes = Encrypter::genRandomBytes($length);

        $this->assertEquals($length, strlen($bytes));
    }

    /**
     */
    public function testGenRandomBytesDefaultLength(): void
    {
        $bytes = Encrypter::genRandomBytes();

        $this->assertEquals(16, strlen($bytes));
    }

    /**
     */
    public function testGenRandomBytesAreRandom(): void
    {
        $bytes1 = Encrypter::genRandomBytes(32);
        $bytes2 = Encrypter::genRandomBytes(32);

        $this->assertNotEquals($bytes1, $bytes2);
    }

    /**
     * Data provider for timing safe compare identical strings
     *
     * @return  Generator
     */
    public static function seedIdenticalStrings(): Generator
    {
        yield 'simple password' => ['password123', 'password123'];
        yield 'empty strings' => ['', ''];
        yield 'single character' => ['a', 'a'];
        yield 'numeric string' => ['1234567890', '1234567890'];
        yield 'special characters' => ["!@#$%^&*()_+-=[]{}|;':\",./<>?", "!@#$%^&*()_+-=[]{}|;':\",./<>?"];
        yield 'unicode' => ['日本語', '日本語'];
        yield 'binary data' => ["\x00\x01\x02\x03\xff\xfe\xfd", "\x00\x01\x02\x03\xff\xfe\xfd"];
        yield 'long string' => [str_repeat('x', 1000), str_repeat('x', 1000)];
    }

    /**
     */
    #[DataProvider('seedIdenticalStrings')]
    public function testTimingSafeCompareIdentical(string $str1, string $str2): void
    {
        $this->assertTrue(Encrypter::timingSafeCompare($str1, $str2));
    }

    /**
     * Data provider for timing safe compare different strings
     *
     * @return  Generator
     */
    public static function seedDifferentStrings(): Generator
    {
        yield 'different passwords' => ['password123', 'password456'];
        yield 'different case' => ['Password', 'password'];
        yield 'different lengths' => ['short', 'much longer string'];
        yield 'one empty' => ['nonempty', ''];
        yield 'empty vs non-empty' => ['', 'nonempty'];
        yield 'similar start' => ['password123', 'password124'];
        yield 'similar end' => ['abc123', 'xyz123'];
        yield 'binary data diff' => ["\x00\x01\x02", "\x00\x01\x03"];
    }

    /**
     */
    #[DataProvider('seedDifferentStrings')]
    public function testTimingSafeCompareDifferent(string $str1, string $str2): void
    {
        $this->assertFalse(Encrypter::timingSafeCompare($str1, $str2));
    }

    /**
     */
    public function testConstructWithCipherAndKey(): void
    {
        $cipher = new Simple();
        $key = $cipher->generateKey();
        $encrypter = new Encrypter($cipher, $key);

        $plaintext = 'Test message';
        $encrypted = $encrypter->encrypt($plaintext);
        $decrypted = $encrypter->decrypt($encrypted);

        $this->assertEquals($plaintext, $decrypted);
    }

    /**
     */
    public function testGenerateKey(): void
    {
        $cipher = new Simple();
        $encrypter = new Encrypter($cipher);

        $key = $encrypter->generateKey();

        $this->assertInstanceOf(Key::class, $key);
        $this->assertEquals('simple', $key->type);
    }

    /**
     */
    public function testSetKeyReturnsThis(): void
    {
        $cipher = new Simple();
        $encrypter = new Encrypter($cipher);
        $key = $cipher->generateKey();

        $result = $encrypter->setKey($key);

        $this->assertSame($encrypter, $result);
    }

    /**
     */
    public function testEncryptionWorksAfterSetKey(): void
    {
        $cipher = new Simple();
        $encrypter = new Encrypter($cipher);
        $key = $cipher->generateKey();

        $encrypter->setKey($key);

        $plaintext = 'Test with new key';
        $encrypted = $encrypter->encrypt($plaintext);
        $decrypted = $encrypter->decrypt($encrypted);

        $this->assertEquals($plaintext, $decrypted);
    }

    /**
     * Data provider for encrypter round trip tests
     *
     * @return  Generator
     */
    public static function seedEncrypterPlaintext(): Generator
    {
        yield 'simple text' => ['Hello, World!'];
        yield 'empty string' => [''];
        yield 'unicode text' => ['日本語 中文 한국어'];
        yield 'long text' => [str_repeat('Long message. ', 50)];
        yield 'special chars' => ['<script>alert("xss")</script>'];
        yield 'json data' => ['{"key": "value", "number": 123}'];
    }

    /**
     */
    #[DataProvider('seedEncrypterPlaintext')]
    public function testEncrypterRoundTrip(string $plaintext): void
    {
        $cipher = new Simple();
        $key = $cipher->generateKey();
        $encrypter = new Encrypter($cipher, $key);

        $encrypted = $encrypter->encrypt($plaintext);
        $decrypted = $encrypter->decrypt($encrypted);

        $this->assertEquals($plaintext, $decrypted);
    }
}
