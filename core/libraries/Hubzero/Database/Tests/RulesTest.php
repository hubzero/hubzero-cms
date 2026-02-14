<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Hubzero\Database\Rules;
use Hubzero\Database\ValidationError;

/**
 * Database rules validation tests
 */
class RulesTest extends TestCase
{
    // =========================================================================
    // Original BC Tests
    // =========================================================================

    /**
     * Test to make sure an empty string fails validation
     */
    #[Test]
    public function notEmpty(): void
    {
        $pass = Rules::validate(['name' => 'Me'], ['name' => 'notempty']);
        $fail = Rules::validate(['name' => ''], ['name' => 'notempty']);

        $this->assertTrue($pass, 'Rule (notempty) should have validated with a name of "Me"');
        $this->assertCount(1, $fail, 'Rule (notempty) should have returned one error for empty name');
    }

    /**
     * Test to make sure a negative number fails validation
     *
     * @return  void
     **/
    #[Test]
    public function positive(): void
    {
        $pass = Rules::validate(['age' => 25], ['age' => 'positive']);
        $fail = Rules::validate(['age' => -1], ['age' => 'positive']);

        $this->assertTrue($pass, 'Rule (positive) should have validated with an age of 25');
        $this->assertCount(1, $fail, 'Rule (positive) should have returned one error for an age of -1');
    }

    /**
     * Test to make sure a zero fails validation
     *
     * @return  void
     **/
    #[Test]
    public function nonzero(): void
    {
        $passPos = Rules::validate(['score' =>  1], ['score' => 'nonzero']);
        $passNeg = Rules::validate(['score' => -1], ['score' => 'nonzero']);
        $fail    = Rules::validate(['score' =>  0], ['score' => 'nonzero']);

        $this->assertTrue($passPos, 'Rule (nonzero) should have validated with a score of 1');
        $this->assertTrue($passNeg, 'Rule (nonzero) should have validated with a score of -1');
        $this->assertCount(1, $fail, 'Rule (nonzero) should have returned one error for a score of 0');
    }

    /**
     * Test to make sure a number fails validation
     *
     * @return  void
     **/
    #[Test]
    public function alpha(): void
    {
        $pass = Rules::validate(['name' => "John Awesome"], ['name' => 'alpha']);
        $fail = Rules::validate(['name' =>  "MI6"], ['name' => 'alpha']);

        $this->assertTrue($pass, 'Rule (alpha) should have validated with a name of "John Awesome"');
        $this->assertCount(1, $fail, 'Rule (alpha) should have returned one error for a name of "MI6"');
    }

    /**
     * Test to make sure a bad phone fails validation
     *
     * @return  void
     **/
    #[Test]
    public function phone(): void
    {
        $pass1 = Rules::validate(['phone' => "765-494-4000"], ['phone' => 'phone']);
        $pass2 = Rules::validate(['phone' => "(765) 494-4000"], ['phone' => 'phone']);
        $pass3 = Rules::validate(['phone' => "7654944000"], ['phone' => 'phone']);
        $fail1 = Rules::validate(['phone' =>  "12345"], ['phone' => 'phone']);
        $fail2 = Rules::validate(['phone' =>  "123-456-7890"], ['phone' => 'phone']);

        $this->assertTrue($pass1, 'Rule (phone) should have validated with a phone of "765-494-4000"');
        $this->assertTrue($pass2, 'Rule (phone) should have validated with a phone of "(765) 494-4000"');
        $this->assertTrue($pass3, 'Rule (phone) should have validated with a phone of "7654944000"');
        $this->assertCount(1, $fail1, 'Rule (phone) should have returned one error for a phone of "12345"');
        $this->assertCount(1, $fail2, 'Rule (phone) should have returned one error for a phone of "123-456-7890"');
    }

    #[Test]
    public function phoneAdditionalFormats(): void
    {
        // With country code
        $this->assertTrue(Rules::validate(['phone' => "+1 765-494-4000"], ['phone' => 'phone']));
        $this->assertTrue(Rules::validate(['phone' => "1-765-494-4000"], ['phone' => 'phone']));

        // With extension
        $this->assertTrue(Rules::validate(['phone' => "765-494-4000 ext 1234"], ['phone' => 'phone']));
        $this->assertTrue(Rules::validate(['phone' => "765-494-4000 x1234"], ['phone' => 'phone']));

        // Invalid: letters
        $result = Rules::validate(['phone' => "abc-def-ghij"], ['phone' => 'phone']);
        $this->assertIsArray($result);

        // Invalid: too short
        $result = Rules::validate(['phone' => "494-4000"], ['phone' => 'phone']);
        $this->assertIsArray($result);
    }

    /**
     * Test to make sure an improper email fails validation
     *
     * @return  void
     **/
    #[Test]
    public function email(): void
    {
        $pass = Rules::validate(['email' => "you@gmail.com"], ['email' => 'email']);
        $fail = Rules::validate(['email' =>  "me.com"], ['email' => 'email']);

        $this->assertTrue($pass, 'Rule (email) should have validated with a email of "you@gmail.com"');
        $this->assertCount(1, $fail, 'Rule (email) should have returned one error for a email of "me.com"');
    }

    #[Test]
    public function emailAdditionalFormats(): void
    {
        // Subaddressing (plus addressing)
        $this->assertTrue(Rules::validate(['email' => "user+tag@example.com"], ['email' => 'email']));

        // Subdomain
        $this->assertTrue(Rules::validate(['email' => "user@mail.example.co.uk"], ['email' => 'email']));

        // Invalid: no @
        $result = Rules::validate(['email' => "plainaddress"], ['email' => 'email']);
        $this->assertIsArray($result);

        // Invalid: no domain
        $result = Rules::validate(['email' => "user@"], ['email' => 'email']);
        $this->assertIsArray($result);

        // Invalid: double @
        $result = Rules::validate(['email' => "user@@example.com"], ['email' => 'email']);
        $this->assertIsArray($result);
    }

    /**
     * Test to make sure an empty string fails validation
     *
     * @return  void
     **/
    #[Test]
    public function compoundRules(): void
    {
        $pass = Rules::validate(['name' => "mr cool"], ['name' => 'notempty|alpha']);
        $fail = Rules::validate(['name' => ""], ['name' => 'notempty|alpha']);

        $this->assertTrue($pass, 'Rule (notempty|alpha) should have validated with a name of "mr cool"');
        $this->assertCount(2, $fail, 'Rules (notempty|alpha) should have returned two errors for a name of ""');
    }

    /**
     * Test to make sure partial failure still results in failure
     *
     * @return  void
     **/
    #[Test]
    public function partialFailure(): void
    {
        $fail = Rules::validate(['name' => "Mr. Awesome"], ['name' => 'notempty|alpha']);

        $this->assertCount(
            1,
            $fail,
            'Rules (notempty|alpha) should have returned one error for a name of "Mr. Awesome"'
        );
    }

    /**
     * Test to make sure custom validation rules result in failure
     *
     * @return  void
     **/
    #[Test]
    public function customRules(): void
    {
        $endAfterStart = function ($data) {
            return $data['end'] > $data['start'] ? false : 'The end must be after the beginning';
        };
        $fail = Rules::validate(['start' => '2015-07-01 00:00:00',
            'end' => '2015-06-01 00:00:00'], ['end' => $endAfterStart]);
        $beSquare = function ($data) {
            return $data['height'] == $data['width'] ? false : 'It\'s not a square!';
        };

        $pass = Rules::validate(['height' => 5, 'width' => 5], ['width' => $beSquare]);

        $this->assertCount(
            1,
            $fail,
            'Rules (custom) should have returned one error for an end date before the beginning date'
        );
        $this->assertTrue($pass, 'Rule (custom) should have validated with an equal height and width');
    }

    // =========================================================================
    // ValidationError Tests
    // =========================================================================

    /**
     * Test that errors are ValidationError instances

     */

    #[Test]

    public function errorsAreValidationErrorInstances(): void
    {
        $fail = Rules::validate(['name' => ''], ['name' => 'notempty']);

        $this->assertInstanceOf(ValidationError::class, $fail[0]);
    }

    /**
     * Test ValidationError stringifies for BC

     */

    #[Test]

    public function validationErrorStringifies(): void
    {
        $fail = Rules::validate(['title' => ''], ['title' => 'notempty']);

        // Can be cast to string (BC)
        $this->assertStringContainsString('cannot be empty', (string) $fail[0]);

        // Can access structured data (new)
        $this->assertEquals('title', $fail[0]->getField());
        $this->assertEquals('notempty', $fail[0]->getRule());
    }

    /**
     * Test ValidationError JSON serialization

     */

    #[Test]

    public function validationErrorJsonSerializes(): void
    {
        $error = new ValidationError('email', 'email', 'Invalid email', ['format' => 'email']);

        $json = json_encode($error);
        $data = json_decode($json, true);

        $this->assertEquals('email', $data['field']);
        $this->assertEquals('email', $data['rule']);
        $this->assertEquals('Invalid email', $data['message']);
        $this->assertEquals(['format' => 'email'], $data['params']);
    }

    /**
     * Test ValidationError toArray

     */

    #[Test]

    public function validationErrorToArray(): void
    {
        $error = new ValidationError('title', 'length', 'Too short', ['min' => 3], 'ab');

        $array = $error->toArray();

        $this->assertEquals('title', $array['field']);
        $this->assertEquals('length', $array['rule']);
        $this->assertEquals('Too short', $array['message']);
        $this->assertEquals(['min' => 3], $array['params']);
    }

    /**
     * Test ValidationError getParam and getValue

     */

    #[Test]

    public function validationErrorAccessors(): void
    {
        $error = new ValidationError('age', 'between', 'Out of range', ['min' => 18, 'max' => 65], 70);

        $this->assertEquals(18, $error->getParam('min'));
        $this->assertEquals(65, $error->getParam('max'));
        $this->assertNull($error->getParam('nonexistent'));
        $this->assertEquals('default', $error->getParam('nonexistent', 'default'));
        $this->assertEquals(70, $error->getValue());
    }

    // =========================================================================
    // Parameterized Rules Tests
    // =========================================================================

    /**
     * Test length rule with min and max

     */

    #[Test]

    public function lengthRuleWithMinMax(): void
    {
        // Valid
        $result = Rules::validate(
            ['name' => 'John'],
            ['name' => 'length:2,10']
        );
        $this->assertTrue($result);

        // Too short
        $result = Rules::validate(
            ['name' => 'J'],
            ['name' => 'length:2,10']
        );
        $this->assertIsArray($result);
        $this->assertEquals('length', $result[0]->getRule());
        $this->assertStringContainsString('at least 2', $result[0]->getMessage());

        // Too long
        $result = Rules::validate(
            ['name' => 'JohnJacobSmith'],
            ['name' => 'length:2,10']
        );
        $this->assertIsArray($result);
        $this->assertStringContainsString('cannot exceed 10', $result[0]->getMessage());
    }

    /**
     * Test length rule with only min

     */

    #[Test]

    public function lengthRuleMinOnly(): void
    {
        $result = Rules::validate(
            ['name' => 'Jo'],
            ['name' => 'length:3']
        );

        $this->assertIsArray($result);
        $this->assertStringContainsString('at least 3', $result[0]->getMessage());
    }

    /**
     * Test length rule with only max

     */

    #[Test]

    public function lengthRuleMaxOnly(): void
    {
        $result = Rules::validate(
            ['code' => 'ABCDE'],
            ['code' => 'length:,3']
        );

        $this->assertIsArray($result);
        $this->assertStringContainsString('cannot exceed 3', $result[0]->getMessage());
    }

    /**
     * Test min rule

     */

    #[Test]

    public function minRule(): void
    {
        // Valid
        $result = Rules::validate(['age' => 18], ['age' => 'min:18']);
        $this->assertTrue($result);

        // Fails
        $result = Rules::validate(['age' => 17], ['age' => 'min:18']);
        $this->assertIsArray($result);
        $this->assertEquals('min', $result[0]->getRule());
        $this->assertEquals(18, $result[0]->getParam('min'));
    }

    /**
     * Test max rule

     */

    #[Test]

    public function maxRule(): void
    {
        // Valid
        $result = Rules::validate(['score' => 100], ['score' => 'max:100']);
        $this->assertTrue($result);

        // Fails
        $result = Rules::validate(['score' => 101], ['score' => 'max:100']);
        $this->assertIsArray($result);
        $this->assertEquals('max', $result[0]->getRule());
    }

    /**
     * Test between rule

     */

    #[Test]

    public function betweenRule(): void
    {
        // Valid
        $result = Rules::validate(['age' => 25], ['age' => 'between:18,65']);
        $this->assertTrue($result);

        // Too low
        $result = Rules::validate(['age' => 17], ['age' => 'between:18,65']);
        $this->assertIsArray($result);
        $this->assertEquals('between', $result[0]->getRule());

        // Too high
        $result = Rules::validate(['age' => 66], ['age' => 'between:18,65']);
        $this->assertIsArray($result);
    }

    /**
     * Test in rule

     */

    #[Test]

    public function inRule(): void
    {
        // Valid
        $result = Rules::validate(
            ['status' => 'published'],
            ['status' => 'in:draft,published,archived']
        );
        $this->assertTrue($result);

        // Fails
        $result = Rules::validate(
            ['status' => 'deleted'],
            ['status' => 'in:draft,published,archived']
        );
        $this->assertIsArray($result);
        $this->assertEquals('in', $result[0]->getRule());
        $this->assertStringContainsString('must be one of', $result[0]->getMessage());
    }

    /**
     * Test notin rule

     */

    #[Test]

    public function notinRule(): void
    {
        // Valid
        $result = Rules::validate(
            ['status' => 'active'],
            ['status' => 'notin:banned,suspended']
        );
        $this->assertTrue($result);

        // Fails
        $result = Rules::validate(
            ['status' => 'banned'],
            ['status' => 'notin:banned,suspended']
        );
        $this->assertIsArray($result);
        $this->assertEquals('notin', $result[0]->getRule());
    }

    /**
     * Test regex rule

     */

    #[Test]

    public function regexRule(): void
    {
        // Valid
        $result = Rules::validate(
            ['code' => 'ABC'],
            ['code' => 'regex:/^[A-Z]{3}$/']
        );
        $this->assertTrue($result);

        // Fails
        $result = Rules::validate(
            ['code' => 'abc'],
            ['code' => 'regex:/^[A-Z]{3}$/']
        );
        $this->assertIsArray($result);
        $this->assertEquals('regex', $result[0]->getRule());
    }

    /**
     * Test numeric rule

     */

    #[Test]

    public function numericRule(): void
    {
        // Valid integers
        $this->assertTrue(Rules::validate(['n' => 42], ['n' => 'numeric']));

        // Valid floats
        $this->assertTrue(Rules::validate(['n' => 3.14], ['n' => 'numeric']));

        // Valid string numbers
        $this->assertTrue(Rules::validate(['n' => '123'], ['n' => 'numeric']));

        // Fails
        $result = Rules::validate(['n' => 'abc'], ['n' => 'numeric']);
        $this->assertIsArray($result);
        $this->assertEquals('numeric', $result[0]->getRule());
    }

    /**
     * Test integer rule

     */

    #[Test]

    public function integerRule(): void
    {
        // Valid
        $this->assertTrue(Rules::validate(['n' => 42], ['n' => 'integer']));
        $this->assertTrue(Rules::validate(['n' => '42'], ['n' => 'integer']));

        // Fails on float
        $result = Rules::validate(['n' => 3.14], ['n' => 'integer']);
        $this->assertIsArray($result);
        $this->assertEquals('integer', $result[0]->getRule());
    }

    /**
     * Test boolean rule

     */

    #[Test]

    public function booleanRule(): void
    {
        // Valid
        $this->assertTrue(Rules::validate(['active' => true], ['active' => 'boolean']));
        $this->assertTrue(Rules::validate(['active' => false], ['active' => 'boolean']));
        $this->assertTrue(Rules::validate(['active' => 1], ['active' => 'boolean']));
        $this->assertTrue(Rules::validate(['active' => 0], ['active' => 'boolean']));
        $this->assertTrue(Rules::validate(['active' => '1'], ['active' => 'boolean']));
        $this->assertTrue(Rules::validate(['active' => '0'], ['active' => 'boolean']));

        // Fails
        $result = Rules::validate(['active' => 'yes'], ['active' => 'boolean']);
        $this->assertIsArray($result);
        $this->assertEquals('boolean', $result[0]->getRule());
    }

    /**
     * Test alphanumeric rule

     */

    #[Test]

    public function alphanumericRule(): void
    {
        // Valid
        $this->assertTrue(Rules::validate(['code' => 'ABC123'], ['code' => 'alphanumeric']));

        // Fails with special chars
        $result = Rules::validate(['code' => 'ABC-123'], ['code' => 'alphanumeric']);
        $this->assertIsArray($result);
        $this->assertEquals('alphanumeric', $result[0]->getRule());
    }

    /**
     * Test url rule

     */

    #[Test]

    public function urlRule(): void
    {
        // Valid
        $this->assertTrue(Rules::validate(['site' => 'https://example.com'], ['site' => 'url']));
        $this->assertTrue(Rules::validate(['site' => 'http://example.com/path'], ['site' => 'url']));

        // Fails
        $result = Rules::validate(['site' => 'not-a-url'], ['site' => 'url']);
        $this->assertIsArray($result);
        $this->assertEquals('url', $result[0]->getRule());
    }

    /**
     * Test same rule (field comparison)

     */

    #[Test]

    public function sameRule(): void
    {
        // Valid
        $result = Rules::validate(
            ['password' => 'secret123', 'password_confirm' => 'secret123'],
            ['password_confirm' => 'same:password']
        );
        $this->assertTrue($result);

        // Fails
        $result = Rules::validate(
            ['password' => 'secret123', 'password_confirm' => 'different'],
            ['password_confirm' => 'same:password']
        );
        $this->assertIsArray($result);
        $this->assertEquals('same', $result[0]->getRule());
    }

    /**
     * Test different rule (field comparison)

     */

    #[Test]

    public function differentRule(): void
    {
        // Valid
        $result = Rules::validate(
            ['new_password' => 'newsecret', 'old_password' => 'oldsecret'],
            ['new_password' => 'different:old_password']
        );
        $this->assertTrue($result);

        // Fails
        $result = Rules::validate(
            ['new_password' => 'same', 'old_password' => 'same'],
            ['new_password' => 'different:old_password']
        );
        $this->assertIsArray($result);
        $this->assertEquals('different', $result[0]->getRule());
    }

    /**
     * Test required rule (alias for notempty)

     */

    #[Test]

    public function requiredRule(): void
    {
        // Valid
        $this->assertTrue(Rules::validate(['name' => 'John'], ['name' => 'required']));

        // Zero is valid
        $this->assertTrue(Rules::validate(['count' => 0], ['count' => 'required']));
        $this->assertTrue(Rules::validate(['count' => '0'], ['count' => 'required']));

        // Fails
        $result = Rules::validate(['name' => ''], ['name' => 'required']);
        $this->assertIsArray($result);
        $this->assertEquals('required', $result[0]->getRule());
    }

    /**
     * Test date rule

     */

    #[Test]

    public function dateRule(): void
    {
        // Valid date string
        $this->assertTrue(Rules::validate(['created' => '2025-01-15'], ['created' => 'date']));

        // Valid with format
        $this->assertTrue(Rules::validate(['created' => '15/01/2025'], ['created' => 'date:d/m/Y']));

        // Fails
        $result = Rules::validate(['created' => 'not-a-date'], ['created' => 'date']);
        $this->assertIsArray($result);
        $this->assertEquals('date', $result[0]->getRule());
    }

    /**
     * Test uuid rule

     */

    #[Test]

    public function uuidRule(): void
    {
        // Valid UUID v4
        $this->assertTrue(Rules::validate(
            ['id' => '550e8400-e29b-41d4-a716-446655440000'],
            ['id' => 'uuid']
        ));

        // Valid UUID v4 (lowercase)
        $this->assertTrue(Rules::validate(
            ['id' => '6ba7b810-9dad-41d4-8b78-9c2e8e2e8b48'],
            ['id' => 'uuid']
        ));

        // Valid UUID v4 (uppercase)
        $this->assertTrue(Rules::validate(
            ['id' => '550E8400-E29B-41D4-A716-446655440000'],
            ['id' => 'uuid']
        ));

        // Empty is allowed (use required|uuid if needed)
        $this->assertTrue(Rules::validate(['id' => ''], ['id' => 'uuid']));
        $this->assertTrue(Rules::validate(['id' => null], ['id' => 'uuid']));

        // Invalid - wrong format
        $result = Rules::validate(['id' => 'not-a-uuid'], ['id' => 'uuid']);
        $this->assertIsArray($result);
        $this->assertEquals('uuid', $result[0]->getRule());

        // Invalid - UUID v1 (version digit is 1, not 4)
        $result = Rules::validate(['id' => '550e8400-e29b-11d4-a716-446655440000'], ['id' => 'uuid']);
        $this->assertIsArray($result);

        // Combined with required
        $result = Rules::validate(['id' => ''], ['id' => 'required|uuid']);
        $this->assertIsArray($result);
        $this->assertEquals('required', $result[0]->getRule());
    }

    // =========================================================================
    // Combined Rules Tests
    // =========================================================================

    /**
     * Test combining old and new rules

     */

    #[Test]

    public function combinedOldAndNewRules(): void
    {
        $result = Rules::validate(
            ['name' => 'Jo'],
            ['name' => 'notempty|length:3,50']
        );

        // notempty passes, length fails
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('length', $result[0]->getRule());
    }

    /**
     * Test multiple parameterized rules

     */

    #[Test]

    public function multipleParameterizedRules(): void
    {
        $result = Rules::validate(
            ['score' => 150],
            ['score' => 'numeric|min:0|max:100']
        );

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('max', $result[0]->getRule());
    }

    /**
     * Test all rules pass

     */

    #[Test]

    public function allRulesPass(): void
    {
        $result = Rules::validate(
            [
                'name' => 'John Doe',
                'age' => 25,
                'status' => 'active',
            ],
            [
                'name' => 'required|length:2,100',
                'age' => 'numeric|between:18,120',
                'status' => 'in:active,inactive,pending',
            ]
        );

        $this->assertTrue($result);
    }

    // =========================================================================
    // Edge Cases
    // =========================================================================

    /**
     * Test empty rules array returns true

     */

    #[Test]

    public function emptyRulesReturnsTrue(): void
    {
        $result = Rules::validate(['field' => 'value'], []);
        $this->assertTrue($result);
    }

    /**
     * Test field without rule is ignored

     */

    #[Test]

    public function fieldWithoutRuleIsIgnored(): void
    {
        $result = Rules::validate(
            ['name' => 'John', 'ignored' => 'value'],
            ['name' => 'notempty']
        );
        $this->assertTrue($result);
    }

    /**
     * Test rule for missing field is skipped

     */

    #[Test]

    public function ruleForMissingFieldIsSkipped(): void
    {
        $result = Rules::validate(
            ['name' => 'John'],
            ['name' => 'notempty', 'email' => 'email']
        );
        $this->assertTrue($result);
    }

    /**
     * Test unknown rule is silently skipped (BC)

     */

    #[Test]

    public function unknownRuleIsSilentlySkipped(): void
    {
        $result = Rules::validate(
            ['field' => 'value'],
            ['field' => 'unknownrule']
        );
        $this->assertTrue($result);
    }

    /**
     * Test callable returning structured array

     */

    #[Test]

    public function callableReturningStructuredArray(): void
    {
        $result = Rules::validate(
            ['field' => 'bad'],
            ['field' => function ($data) {
                return [
                    'field' => 'field',
                    'rule' => 'custom',
                    'message' => 'Custom error message',
                    'params' => ['custom' => true],
                ];
            }]
        );

        $this->assertIsArray($result);
        $this->assertEquals('custom', $result[0]->getRule());
        $this->assertEquals('Custom error message', $result[0]->getMessage());
        $this->assertTrue($result[0]->getParam('custom'));
    }
}
