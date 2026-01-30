<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Password\Tests;

use Hubzero\Test\Basic;
use Hubzero\Password\CharacterClass;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * CharacterClass tests
 */
class CharacterClassTest extends Basic
{
    /**
     * Reset the static classes array before each test
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        CharacterClass::$classes = null;
    }

    /**
     * Data provider for uppercase letter matching
     *
     * @return  Generator
     */
    public static function seedUppercaseLetters(): Generator
    {
        foreach (range('A', 'Z') as $letter) {
            yield "uppercase letter {$letter}" => [$letter];
        }
    }

    /**
     */
    #[DataProvider('seedUppercaseLetters')]
    public function testMatchUppercase(string $letter): void
    {
        $result = CharacterClass::match($letter);
        $names = $this->extractClassNames($result);

        $this->assertContains('uppercase', $names);
        $this->assertContains('alpha', $names);
    }

    /**
     * Data provider for lowercase letter matching
     *
     * @return  Generator
     */
    public static function seedLowercaseLetters(): Generator
    {
        foreach (range('a', 'z') as $letter) {
            yield "lowercase letter {$letter}" => [$letter];
        }
    }

    /**
     */
    #[DataProvider('seedLowercaseLetters')]
    public function testMatchLowercase(string $letter): void
    {
        $result = CharacterClass::match($letter);
        $names = $this->extractClassNames($result);

        $this->assertContains('lowercase', $names);
        $this->assertContains('alpha', $names);
    }

    /**
     * Data provider for numeric character matching (1-9)
     *
     * @return  Generator
     */
    public static function seedNumericCharacters(): Generator
    {
        for ($i = 1; $i <= 9; $i++) {
            yield "digit {$i}" => [(string) $i];
        }
    }

    /**
     */
    #[DataProvider('seedNumericCharacters')]
    public function testMatchNumeric(string $digit): void
    {
        $result = CharacterClass::match($digit);
        $names = $this->extractClassNames($result);

        $this->assertContains('numeric', $names);
        $this->assertContains('nonalpha', $names);
    }

    /**
     */
    public function testZeroTreatedAsEmpty(): void
    {
        $result = CharacterClass::match('0');

        $this->assertEmpty($result);
    }

    /**
     * Data provider for special character matching
     *
     * @return  Generator
     */
    public static function seedSpecialCharacters(): Generator
    {
        $specials = [
            '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '-', '_', '+', '=',
            '[', ']', '{', '}', '|', '\\', ':', ';', '"', "'", '<', '>', ',', '.',
            '?', '/'
        ];

        foreach ($specials as $char) {
            yield "special char {$char}" => [$char];
        }
    }

    /**
     */
    #[DataProvider('seedSpecialCharacters')]
    public function testMatchSpecial(string $char): void
    {
        $result = CharacterClass::match($char);
        $names = $this->extractClassNames($result);

        $this->assertContains('special', $names);
        $this->assertContains('nonalpha', $names);
    }

    /**
     */
    public function testMatchEmptyString(): void
    {
        $result = CharacterClass::match('');

        $this->assertIsArray($result);
    }

    /**
     * Data provider for multi-character string tests
     *
     * @return  Generator
     */
    public static function seedMultiCharacterStrings(): Generator
    {
        yield 'uppercase first (Ab1!)' => [
            'input' => 'Ab1!',
            'expectedClass' => 'uppercase',
            'notExpectedClass' => 'lowercase',
        ];

        yield 'lowercase first (aB1!)' => [
            'input' => 'aB1!',
            'expectedClass' => 'lowercase',
            'notExpectedClass' => 'uppercase',
        ];

        yield 'numeric first (1Ab!)' => [
            'input' => '1Ab!',
            'expectedClass' => 'numeric',
            'notExpectedClass' => 'alpha',
        ];

        yield 'special first (!Ab1)' => [
            'input' => '!Ab1',
            'expectedClass' => 'special',
            'notExpectedClass' => 'alpha',
        ];
    }

    /**
     */
    #[DataProvider('seedMultiCharacterStrings')]
    public function testMatchOnlyFirstCharacter(string $input, string $expectedClass, string $notExpectedClass): void
    {
        $result = CharacterClass::match($input);
        $names = $this->extractClassNames($result);

        $this->assertContains($expectedClass, $names);
        $this->assertNotContains($notExpectedClass, $names);
    }

    /**
     * Data provider for flag value tests
     *
     * @return  Generator
     */
    public static function seedFlagValues(): Generator
    {
        yield 'uppercase has flag 1' => [
            'char' => 'A',
            'className' => 'uppercase',
            'expectedFlag' => '1',
        ];

        yield 'lowercase has flag 1' => [
            'char' => 'a',
            'className' => 'lowercase',
            'expectedFlag' => '1',
        ];

        yield 'numeric has flag 1' => [
            'char' => '5',
            'className' => 'numeric',
            'expectedFlag' => '1',
        ];

        yield 'special has flag 1' => [
            'char' => '!',
            'className' => 'special',
            'expectedFlag' => '1',
        ];

        yield 'alpha has flag 0' => [
            'char' => 'A',
            'className' => 'alpha',
            'expectedFlag' => '0',
        ];

        yield 'nonalpha has flag 0' => [
            'char' => '5',
            'className' => 'nonalpha',
            'expectedFlag' => '0',
        ];
    }

    /**
     */
    #[DataProvider('seedFlagValues')]
    public function testFlagValues(string $char, string $className, string $expectedFlag): void
    {
        $result = CharacterClass::match($char);

        $matchedClass = null;
        foreach ($result as $match) {
            if ($match->name === $className) {
                $matchedClass = $match;
                break;
            }
        }

        $this->assertNotNull($matchedClass, "Class '{$className}' should be found");
        $this->assertEquals($expectedFlag, $matchedClass->flag);
    }

    /**
     */
    public function testClassesInitialization(): void
    {
        $this->assertNull(CharacterClass::$classes);

        CharacterClass::match('A');

        $this->assertNotNull(CharacterClass::$classes);
        $this->assertCount(6, CharacterClass::$classes);
    }

    /**
     * Data provider for expected class names
     *
     * @return  Generator
     */
    public static function seedExpectedClassNames(): Generator
    {
        yield 'uppercase class exists' => ['uppercase'];
        yield 'lowercase class exists' => ['lowercase'];
        yield 'numeric class exists' => ['numeric'];
        yield 'special class exists' => ['special'];
        yield 'alpha class exists' => ['alpha'];
        yield 'nonalpha class exists' => ['nonalpha'];
    }

    /**
     */
    #[DataProvider('seedExpectedClassNames')]
    public function testClassesContainExpectedName(string $expectedName): void
    {
        CharacterClass::match('A');

        $classNames = array_map(function ($class) {
            return $class['name'];
        }, CharacterClass::$classes);

        $this->assertContains($expectedName, $classNames);
    }

    /**
     * Helper method to extract class names from match results
     *
     * @param   array  $results  Match results
     * @return  array  Class names
     */
    private function extractClassNames(array $results): array
    {
        return array_map(function ($match) {
            return $match->name;
        }, $results);
    }
}
