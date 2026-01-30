<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Encryption\Tests;

use Hubzero\Test\Basic;
use Hubzero\Encryption\Key;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Key class tests
 */
class KeyTest extends Basic
{
    /**
     * Data provider for key construction tests
     *
     * @return  Generator
     */
    public static function seedKeyConstruction(): Generator
    {
        yield 'simple key with both keys' => [
            'type' => 'simple',
            'private' => 'private_key_value',
            'public' => 'public_key_value',
            'expectedType' => 'simple',
            'expectedPrivate' => 'private_key_value',
            'expectedPublic' => 'public_key_value',
        ];

        yield 'rsa key type' => [
            'type' => 'rsa',
            'private' => 'rsa_private',
            'public' => 'rsa_public',
            'expectedType' => 'rsa',
            'expectedPrivate' => 'rsa_private',
            'expectedPublic' => 'rsa_public',
        ];

        yield 'aes key type' => [
            'type' => 'aes',
            'private' => 'aes_secret',
            'public' => 'aes_secret',
            'expectedType' => 'aes',
            'expectedPrivate' => 'aes_secret',
            'expectedPublic' => 'aes_secret',
        ];

        yield 'empty string keys' => [
            'type' => 'test',
            'private' => '',
            'public' => '',
            'expectedType' => 'test',
            'expectedPrivate' => '',
            'expectedPublic' => '',
        ];
    }

    /**
     */
    #[DataProvider('seedKeyConstruction')]
    public function testKeyConstruction(
        string $type,
        string $private,
        string $public,
        string $expectedType,
        string $expectedPrivate,
        string $expectedPublic
    ): void {
        $key = new Key($type, $private, $public);

        $this->assertEquals($expectedType, $key->type);
        $this->assertEquals($expectedPrivate, $key->private);
        $this->assertEquals($expectedPublic, $key->public);
    }

    /**
     * Data provider for key construction with type only
     *
     * @return  Generator
     */
    public static function seedKeyTypeOnly(): Generator
    {
        yield 'simple type only' => ['simple'];
        yield 'rsa type only' => ['rsa'];
        yield 'aes type only' => ['aes'];
        yield 'custom type only' => ['custom'];
    }

    /**
     */
    #[DataProvider('seedKeyTypeOnly')]
    public function testKeyConstructionWithTypeOnly(string $type): void
    {
        $key = new Key($type);

        $this->assertEquals($type, $key->type);
        $this->assertNull($key->private);
        $this->assertNull($key->public);
    }

    /**
     * Data provider for type casting tests
     *
     * @return  Generator
     */
    public static function seedTypeCasting(): Generator
    {
        yield 'integer type cast to string' => [
            'type' => 123,
            'expectedType' => '123',
        ];

        yield 'zero type cast to string' => [
            'type' => 0,
            'expectedType' => '0',
        ];

        yield 'float type cast to string' => [
            'type' => 45.67,
            'expectedType' => '45.67',
        ];
    }

    /**
     */
    #[DataProvider('seedTypeCasting')]
    public function testTypeCastToString($type, string $expectedType): void
    {
        $key = new Key($type);

        $this->assertSame($expectedType, $key->type);
    }

    /**
     * Data provider for key value casting tests
     *
     * @return  Generator
     */
    public static function seedKeyCasting(): Generator
    {
        yield 'integer keys cast to string' => [
            'private' => 12345,
            'public' => 67890,
            'expectedPrivate' => '12345',
            'expectedPublic' => '67890',
        ];

        yield 'zero keys cast to string' => [
            'private' => 0,
            'public' => 0,
            'expectedPrivate' => '0',
            'expectedPublic' => '0',
        ];

        yield 'float keys cast to string' => [
            'private' => 123.45,
            'public' => 678.90,
            'expectedPrivate' => '123.45',
            'expectedPublic' => '678.9',
        ];
    }

    /**
     */
    #[DataProvider('seedKeyCasting')]
    public function testKeysCastToString($private, $public, string $expectedPrivate, string $expectedPublic): void
    {
        $key = new Key('test', $private, $public);

        $this->assertSame($expectedPrivate, $key->private);
        $this->assertSame($expectedPublic, $key->public);
    }
}
