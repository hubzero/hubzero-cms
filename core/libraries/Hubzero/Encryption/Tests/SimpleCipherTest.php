<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Encryption\Tests;

use Hubzero\Test\Basic;
use Hubzero\Encryption\Cipher\Simple;
use Hubzero\Encryption\Key;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Simple cipher tests
 */
class SimpleCipherTest extends Basic
{
    /**
     * The cipher instance
     *
     * @var Simple
     */
    protected $cipher;

    /**
     * Set up the test
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->cipher = new Simple();
    }

    /**
     */
    public function testGenerateKeyReturnsKeyInstance(): void
    {
        $key = $this->cipher->generateKey();

        $this->assertInstanceOf(Key::class, $key);
        $this->assertEquals('simple', $key->type);
    }

    /**
     */
    public function testGenerateKeyCreatesNonEmptyKeys(): void
    {
        $key = $this->cipher->generateKey();

        $this->assertNotEmpty($key->private);
        $this->assertNotEmpty($key->public);
    }

    /**
     */
    public function testGenerateKeyUsesSameValueForBothKeys(): void
    {
        $key = $this->cipher->generateKey();

        $this->assertEquals($key->private, $key->public);
    }

    /**
     */
    public function testGenerateKeyDefaultLength(): void
    {
        $key = $this->cipher->generateKey();

        $this->assertEquals(256, strlen($key->private));
    }

    /**
     * Data provider for encryption round trip tests
     *
     * @return  Generator
     */
    public static function seedPlaintextData(): Generator
    {
        yield 'simple text' => ['Hello, World!'];
        yield 'empty string' => [''];
        yield 'single character' => ['X'];
        yield 'numeric string' => ['1234567890'];
        yield 'whitespace only' => ['   '];
        yield 'newlines and tabs' => ["line1\nline2\ttab"];
        yield 'special characters' => ["!@#$%^&*()_+-=[]{}|;':\",./<>?"];
        yield 'unicode japanese' => ['日本語テスト'];
        yield 'unicode chinese' => ['中文测试'];
        yield 'unicode korean' => ['한국어 테스트'];
        yield 'unicode arabic' => ['اختبار عربي'];
        yield 'unicode mixed' => ['Mixed: 日本語 中文 한국어'];
        yield 'long text' => [str_repeat('This is a longer message. ', 100)];
        yield 'binary-like data' => ["\x00\x01\x02\x03\xff\xfe\xfd"];
    }

    /**
     */
    #[DataProvider('seedPlaintextData')]
    public function testEncryptDecryptRoundTrip(string $plaintext): void
    {
        $key = $this->cipher->generateKey();

        $encrypted = $this->cipher->encrypt($plaintext, $key);
        $decrypted = $this->cipher->decrypt($encrypted, $key);

        $this->assertEquals($plaintext, $decrypted);
    }

    /**
     */
    public function testEncryptedDiffersFromPlaintext(): void
    {
        $key = $this->cipher->generateKey();
        $plaintext = 'Hello, World!';

        $encrypted = $this->cipher->encrypt($plaintext, $key);

        $this->assertNotEquals($plaintext, $encrypted);
    }

    /**
     * Data provider for different input comparison tests
     *
     * @return  Generator
     */
    public static function seedDifferentInputPairs(): Generator
    {
        yield 'different words' => ['Message 1', 'Message 2'];
        yield 'different case' => ['ABC', 'abc'];
        yield 'different lengths' => ['short', 'much longer string'];
        yield 'with and without spaces' => ['hello', 'hello '];
    }

    /**
     */
    #[DataProvider('seedDifferentInputPairs')]
    public function testDifferentInputsProduceDifferentOutputs(string $input1, string $input2): void
    {
        $key = $this->cipher->generateKey();

        $encrypted1 = $this->cipher->encrypt($input1, $key);
        $encrypted2 = $this->cipher->encrypt($input2, $key);

        $this->assertNotEquals($encrypted1, $encrypted2);
    }

    /**
     * Data provider for invalid key types
     *
     * @return  Generator
     */
    public static function seedInvalidKeyTypes(): Generator
    {
        yield 'rsa key type' => ['rsa'];
        yield 'aes key type' => ['aes'];
        yield 'blowfish key type' => ['blowfish'];
        yield 'custom key type' => ['custom'];
    }

    /**
     */
    #[DataProvider('seedInvalidKeyTypes')]
    public function testEncryptWithInvalidKeyTypeThrowsException(string $keyType): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid key of type: {$keyType}");

        $key = new Key($keyType, 'private', 'public');
        $this->cipher->encrypt('test', $key);
    }

    /**
     */
    #[DataProvider('seedInvalidKeyTypes')]
    public function testDecryptWithInvalidKeyTypeThrowsException(string $keyType): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid key of type: {$keyType}");

        $key = new Key($keyType, 'private', 'public');
        $this->cipher->decrypt('test', $key);
    }
}
