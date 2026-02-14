<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Hubzero\Database\Factory;
use Hubzero\Database\Driver;
use Hubzero\Database\Relational;
use Hubzero\Database\Rows;
use Hubzero\Database\Tests\TestModels\User;
use Hubzero\Database\Tests\TestModels\UserFactory;

#[CoversClass(Factory::class)]
class FactoryTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['factory_users'];
    }

    protected static function getTestModelClasses(): array
    {
        return [User::class];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        $driver->dropTable('factory_users', true);

        $driver->createTable('factory_users')
            ->id()
            ->string('name', 255)
            ->string('email', 255)
            ->string('status', 50)->default('active')
            ->execute();
    }

    /**
     * Reset sequence counter before each test
     */
    protected function setUp(): void
    {
        parent::setUp();
        Factory::resetSequence(1);
    }

    /**
     * Clean up factory_users table data before each database test
     */
    private function resetTable(Driver $driver): void
    {
        $driver->setQuery("DELETE FROM factory_users");
        try {
            $driver->execute();
        } catch (\Exception $e) {
        }

        User::useTable('factory_users');
        Relational::setDefaultConnection($driver);
    }

    // =========================================================================
    // Sequence Generator Tests
    // =========================================================================

    #[Test]
    public function sequenceReturnsIncrementingIntegers()
    {
        $this->assertEquals(1, Factory::sequence());
        $this->assertEquals(2, Factory::sequence());
        $this->assertEquals(3, Factory::sequence());
    }

    #[Test]
    public function resetSequenceSetsStartingValue()
    {
        Factory::sequence();
        Factory::sequence();
        Factory::resetSequence(100);

        $this->assertEquals(100, Factory::sequence());
        $this->assertEquals(101, Factory::sequence());
    }

    #[Test]
    public function resetSequenceDefaultsToOne()
    {
        Factory::sequence();
        Factory::sequence();
        Factory::resetSequence();

        $this->assertEquals(1, Factory::sequence());
    }

    // =========================================================================
    // UUID Generator Tests
    // =========================================================================

    #[Test]
    public function uuidGeneratesValidFormat()
    {
        $uuid = Factory::uuid();

        // UUID v4 format: xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $uuid
        );
    }

    #[Test]
    public function uuidGeneratesUniqueValues()
    {
        $uuids = [];
        for ($i = 0; $i < 100; $i++) {
            $uuids[] = Factory::uuid();
        }

        $this->assertCount(100, array_unique($uuids));
    }

    // =========================================================================
    // Random Integer Tests
    // =========================================================================

    #[Test]
    public function randomIntReturnsIntegerInRange()
    {
        for ($i = 0; $i < 50; $i++) {
            $value = Factory::randomInt(10, 20);
            $this->assertGreaterThanOrEqual(10, $value);
            $this->assertLessThanOrEqual(20, $value);
        }
    }

    #[Test]
    public function randomIntHandlesSameMinMax()
    {
        $value = Factory::randomInt(5, 5);
        $this->assertEquals(5, $value);
    }

    #[Test]
    public function randomIntDefaultsToZeroAndPhpIntMax()
    {
        $value = Factory::randomInt();
        $this->assertIsInt($value);
        $this->assertGreaterThanOrEqual(0, $value);
    }

    // =========================================================================
    // Random Float Tests
    // =========================================================================

    #[Test]
    public function randomFloatReturnsFloatInRange()
    {
        for ($i = 0; $i < 50; $i++) {
            $value = Factory::randomFloat(1.0, 10.0);
            $this->assertGreaterThanOrEqual(1.0, $value);
            $this->assertLessThanOrEqual(10.0, $value);
        }
    }

    #[Test]
    public function randomFloatRespectsDecimalPlaces()
    {
        $value = Factory::randomFloat(0, 1, 3);
        $parts = explode('.', (string) $value);

        if (isset($parts[1])) {
            $this->assertLessThanOrEqual(3, strlen($parts[1]));
        }
    }

    #[Test]
    public function randomFloatDefaultsToZeroOneRange()
    {
        for ($i = 0; $i < 20; $i++) {
            $value = Factory::randomFloat();
            $this->assertGreaterThanOrEqual(0, $value);
            $this->assertLessThanOrEqual(1, $value);
        }
    }

    // =========================================================================
    // Random Element Tests
    // =========================================================================

    #[Test]
    public function randomElementReturnsElementFromArray()
    {
        $array = ['a', 'b', 'c', 'd'];

        for ($i = 0; $i < 20; $i++) {
            $element = Factory::randomElement($array);
            $this->assertContains($element, $array);
        }
    }

    #[Test]
    public function randomElementHandlesSingleElementArray()
    {
        $array = ['only'];
        $this->assertEquals('only', Factory::randomElement($array));
    }

    #[Test]
    public function randomElementWorksWithMixedTypes()
    {
        $array = [1, 'string', true, null, ['nested']];
        $element = Factory::randomElement($array);
        $this->assertContains($element, $array);
    }

    // =========================================================================
    // Boolean Generator Tests
    // =========================================================================

    #[Test]
    public function booleanReturnsBooleanType()
    {
        $value = Factory::boolean();
        $this->assertIsBool($value);
    }

    #[Test]
    public function booleanWithZeroChanceReturnsFalse()
    {
        for ($i = 0; $i < 10; $i++) {
            $this->assertFalse(Factory::boolean(0));
        }
    }

    #[Test]
    public function booleanWithHundredChanceReturnsTrue()
    {
        for ($i = 0; $i < 10; $i++) {
            $this->assertTrue(Factory::boolean(100));
        }
    }

    #[Test]
    public function booleanDefaultsToFiftyPercent()
    {
        $trueCount = 0;
        $iterations = 1000;

        for ($i = 0; $i < $iterations; $i++) {
            if (Factory::boolean()) {
                $trueCount++;
            }
        }

        // Should be roughly 50% (within 10% tolerance)
        $percentage = ($trueCount / $iterations) * 100;
        $this->assertGreaterThan(40, $percentage);
        $this->assertLessThan(60, $percentage);
    }

    // =========================================================================
    // Timestamp Tests
    // =========================================================================

    #[Test]
    public function timestampReturnsSqlDateTimeFormat()
    {
        $timestamp = Factory::timestamp();

        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/',
            $timestamp
        );
    }

    #[Test]
    public function timestampReturnsCurrentTime()
    {
        $before = date('Y-m-d H:i:s');
        $timestamp = Factory::timestamp();
        $after = date('Y-m-d H:i:s');

        $this->assertGreaterThanOrEqual($before, $timestamp);
        $this->assertLessThanOrEqual($after, $timestamp);
    }

    // =========================================================================
    // Past Date Tests
    // =========================================================================

    #[Test]
    public function pastDateReturnsSqlDateTimeFormat()
    {
        $date = Factory::pastDate();

        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/',
            $date
        );
    }

    #[Test]
    public function pastDateReturnsDateInPast()
    {
        $now = time();
        $date = Factory::pastDate(30);
        $dateTime = strtotime($date);

        $this->assertLessThan($now, $dateTime);
    }

    #[Test]
    public function pastDateRespectsMaxDaysAgo()
    {
        $maxDays = 7;
        $minTimestamp = strtotime("-{$maxDays} days");
        $now = time();

        for ($i = 0; $i < 20; $i++) {
            $date = Factory::pastDate($maxDays);
            $dateTime = strtotime($date);

            $this->assertGreaterThanOrEqual($minTimestamp, $dateTime);
            $this->assertLessThan($now, $dateTime);
        }
    }

    // =========================================================================
    // Future Date Tests
    // =========================================================================

    #[Test]
    public function futureDateReturnsSqlDateTimeFormat()
    {
        $date = Factory::futureDate();

        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/',
            $date
        );
    }

    #[Test]
    public function futureDateReturnsDateInFuture()
    {
        $now = time();
        $date = Factory::futureDate(30);
        $dateTime = strtotime($date);

        $this->assertGreaterThan($now, $dateTime);
    }

    #[Test]
    public function futureDateRespectsMaxDaysAhead()
    {
        $maxDays = 7;
        $maxTimestamp = strtotime("+{$maxDays} days");
        $now = time();

        for ($i = 0; $i < 20; $i++) {
            $date = Factory::futureDate($maxDays);
            $dateTime = strtotime($date);

            $this->assertGreaterThan($now, $dateTime);
            $this->assertLessThanOrEqual($maxTimestamp, $dateTime);
        }
    }

    // =========================================================================
    // Words Generator Tests
    // =========================================================================

    #[Test]
    public function wordsReturnsStringByDefault()
    {
        $words = Factory::words(3);
        $this->assertIsString($words);
    }

    #[Test]
    public function wordsReturnsCorrectCount()
    {
        $words = Factory::words(5);
        $wordArray = explode(' ', $words);
        $this->assertCount(5, $wordArray);
    }

    #[Test]
    public function wordsCanReturnArray()
    {
        $words = Factory::words(4, true);
        $this->assertIsArray($words);
        $this->assertCount(4, $words);
    }

    #[Test]
    public function wordsUsesLoremIpsumVocabulary()
    {
        $loremWords = [
            'lorem', 'ipsum', 'dolor', 'sit', 'amet', 'consectetur', 'adipiscing',
            'elit', 'sed', 'do', 'eiusmod', 'tempor', 'incididunt', 'ut', 'labore',
            'et', 'dolore', 'magna', 'aliqua', 'enim', 'ad', 'minim', 'veniam',
            'quis', 'nostrud', 'exercitation', 'ullamco', 'laboris', 'nisi',
            'aliquip', 'ex', 'ea', 'commodo', 'consequat', 'duis', 'aute', 'irure',
            'in', 'reprehenderit', 'voluptate', 'velit', 'esse', 'cillum', 'fugiat',
            'nulla', 'pariatur', 'excepteur', 'sint', 'occaecat', 'cupidatat',
            'non', 'proident', 'sunt', 'culpa', 'qui', 'officia', 'deserunt',
            'mollit', 'anim', 'id', 'est', 'laborum'
        ];

        $words = Factory::words(10, true);
        foreach ($words as $word) {
            $this->assertContains($word, $loremWords);
        }
    }

    // =========================================================================
    // Sentence Generator Tests
    // =========================================================================

    #[Test]
    public function sentenceStartsWithCapitalLetter()
    {
        $sentence = Factory::sentence();
        $this->assertMatchesRegularExpression('/^[A-Z]/', $sentence);
    }

    #[Test]
    public function sentenceEndsWithPeriod()
    {
        $sentence = Factory::sentence();
        $this->assertStringEndsWith('.', $sentence);
    }

    #[Test]
    public function sentenceContainsCorrectWordCount()
    {
        $sentence = Factory::sentence(8);
        // Remove period and count words
        $words = explode(' ', rtrim($sentence, '.'));
        $this->assertCount(8, $words);
    }

    // =========================================================================
    // Paragraph Generator Tests
    // =========================================================================

    #[Test]
    public function paragraphIsNonEmptyString()
    {
        $paragraph = Factory::paragraph();
        $this->assertIsString($paragraph);
        $this->assertNotEmpty($paragraph);
    }

    #[Test]
    public function paragraphContainsMultipleSentences()
    {
        $paragraph = Factory::paragraph(3);
        // Count sentences by periods (approximate)
        $sentenceCount = substr_count($paragraph, '.');
        $this->assertEquals(3, $sentenceCount);
    }

    // =========================================================================
    // Paragraphs Generator Tests
    // =========================================================================

    #[Test]
    public function paragraphsReturnsStringByDefault()
    {
        $paragraphs = Factory::paragraphs(2);
        $this->assertIsString($paragraphs);
    }

    #[Test]
    public function paragraphsContainsDoubleNewlines()
    {
        $paragraphs = Factory::paragraphs(3);
        $this->assertStringContainsString("\n\n", $paragraphs);
    }

    #[Test]
    public function paragraphsCanReturnArray()
    {
        $paragraphs = Factory::paragraphs(3, true);
        $this->assertIsArray($paragraphs);
        $this->assertCount(3, $paragraphs);
    }

    // =========================================================================
    // Slug Generator Tests
    // =========================================================================

    #[Test]
    public function slugContainsOnlyValidCharacters()
    {
        $slug = Factory::slug();
        $this->assertMatchesRegularExpression('/^[a-z\-]+$/', $slug);
    }

    #[Test]
    public function slugUsesHyphensAsSeparators()
    {
        $slug = Factory::slug(3);
        $parts = explode('-', $slug);
        $this->assertCount(3, $parts);
    }

    #[Test]
    public function slugIsLowercase()
    {
        $slug = Factory::slug(5);
        $this->assertEquals(strtolower($slug), $slug);
    }

    // =========================================================================
    // Email Generator Tests
    // =========================================================================

    #[Test]
    public function emailHasValidFormat()
    {
        $email = Factory::email();
        $this->assertMatchesRegularExpression('/^[a-z0-9]+@[a-z]+\.[a-z]+$/', $email);
    }

    #[Test]
    public function emailUsesKnownTestDomains()
    {
        $validDomains = ['example.com', 'test.com', 'sample.org', 'demo.net'];

        for ($i = 0; $i < 20; $i++) {
            $email = Factory::email();
            $domain = substr($email, strpos($email, '@') + 1);
            $this->assertContains($domain, $validDomains);
        }
    }

    #[Test]
    public function emailUsesProvidedName()
    {
        $email = Factory::email('JohnDoe');
        $this->assertStringStartsWith('johndoe@', $email);
    }

    #[Test]
    public function emailSanitizesSpecialCharacters()
    {
        $email = Factory::email('John.Doe@Test!');
        // Should remove special characters
        $this->assertMatchesRegularExpression('/^[a-z0-9]+@/', $email);
    }

    // =========================================================================
    // Hex Color Generator Tests
    // =========================================================================

    #[Test]
    public function hexColorIncludesHashByDefault()
    {
        $color = Factory::hexColor();
        $this->assertStringStartsWith('#', $color);
    }

    #[Test]
    public function hexColorCanExcludeHash()
    {
        $color = Factory::hexColor(false);
        $this->assertStringStartsNotWith('#', $color);
    }

    #[Test]
    public function hexColorHasCorrectLength()
    {
        $colorWithHash = Factory::hexColor(true);
        $colorWithoutHash = Factory::hexColor(false);

        $this->assertEquals(7, strlen($colorWithHash));
        $this->assertEquals(6, strlen($colorWithoutHash));
    }

    #[Test]
    public function hexColorContainsOnlyHexCharacters()
    {
        $color = Factory::hexColor(false);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{6}$/', $color);
    }

    // =========================================================================
    // IP Address Generator Tests
    // =========================================================================

    #[Test]
    public function ipAddressHasValidFormat()
    {
        $ip = Factory::ipAddress();
        $this->assertMatchesRegularExpression('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $ip);
    }

    #[Test]
    public function ipAddressOctetsAreInValidRange()
    {
        for ($i = 0; $i < 20; $i++) {
            $ip = Factory::ipAddress();
            $octets = explode('.', $ip);

            foreach ($octets as $octet) {
                $this->assertGreaterThanOrEqual(0, (int) $octet);
                $this->assertLessThanOrEqual(255, (int) $octet);
            }
        }
    }

    #[Test]
    public function ipAddressFirstOctetNotZero()
    {
        for ($i = 0; $i < 20; $i++) {
            $ip = Factory::ipAddress();
            $octets = explode('.', $ip);
            $this->assertGreaterThanOrEqual(1, (int) $octets[0]);
        }
    }

    // =========================================================================
    // URL Generator Tests
    // =========================================================================

    #[Test]
    public function urlStartsWithProtocol()
    {
        $url = Factory::url();
        $this->assertMatchesRegularExpression('/^https?:\/\//', $url);
    }

    #[Test]
    public function urlHasValidStructure()
    {
        $url = Factory::url();
        $parsed = parse_url($url);

        $this->assertArrayHasKey('scheme', $parsed);
        $this->assertArrayHasKey('host', $parsed);
        $this->assertArrayHasKey('path', $parsed);
    }

    #[Test]
    public function urlUsesKnownTestDomains()
    {
        $validDomains = ['example.com', 'test.com', 'sample.org', 'demo.net'];

        for ($i = 0; $i < 20; $i++) {
            $url = Factory::url();
            $parsed = parse_url($url);
            $this->assertContains($parsed['host'], $validDomains);
        }
    }

    // =========================================================================
    // Factory Configuration Tests
    // =========================================================================

    #[Test]
    public function newReturnsFactoryInstance()
    {
        $factory = UserFactory::new();
        $this->assertInstanceOf(UserFactory::class, $factory);
    }

    #[Test]
    public function modelClassReturnsConfiguredModel()
    {
        $factory = new UserFactory();
        $this->assertEquals(User::class, $factory->modelClass());
    }

    #[Test]
    public function countIsFluentMethod()
    {
        $factory = new UserFactory();
        $result = $factory->count(3);

        $this->assertSame($factory, $result);
    }

    // =========================================================================
    // Factory make() Tests (In-Memory)
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function makeReturnsSingleModel(string $dbName, Driver $driver): void
    {
        $this->resetTable($driver);

        $user = UserFactory::new()->make();

        $this->assertInstanceOf(User::class, $user);
        $this->assertNotEmpty($user->get('name'));
        $this->assertNotEmpty($user->get('email'));
        $this->assertEquals('active', $user->get('status'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function makeDoesNotPersistToDatabase(string $dbName, Driver $driver): void
    {
        $this->resetTable($driver);

        UserFactory::new()->make();

        $driver->setQuery("SELECT COUNT(*) as cnt FROM factory_users");
        $row = $driver->loadObject();
        $this->assertEquals(0, (int) $row->cnt, "[$dbName] make() should not insert into database");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function makeWithCountReturnsRows(string $dbName, Driver $driver): void
    {
        $this->resetTable($driver);

        $users = UserFactory::new()->count(3)->make();

        $this->assertInstanceOf(Rows::class, $users);
        $this->assertCount(3, $users);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function makeAcceptsAttributeOverrides(string $dbName, Driver $driver): void
    {
        $this->resetTable($driver);

        $user = UserFactory::new()->make(['name' => 'Override Name']);

        $this->assertEquals('Override Name', $user->get('name'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function makeAppliesStates(string $dbName, Driver $driver): void
    {
        $this->resetTable($driver);

        $user = UserFactory::new()->inactive()->make();

        $this->assertEquals('inactive', $user->get('status'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function makeAppliesMultipleStates(string $dbName, Driver $driver): void
    {
        $this->resetTable($driver);

        $user = UserFactory::new()
            ->state(['status' => 'pending'])
            ->state(['name' => 'Chained'])
            ->make();

        $this->assertEquals('pending', $user->get('status'));
        $this->assertEquals('Chained', $user->get('name'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function makeAppliesCallableState(string $dbName, Driver $driver): void
    {
        $this->resetTable($driver);

        $user = UserFactory::new()
            ->state(function ($attributes) {
                return ['name' => strtoupper($attributes['name'])];
            })
            ->make();

        $this->assertEquals(strtoupper($user->get('name')), $user->get('name'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function afterMakingCallbackFires(string $dbName, Driver $driver): void
    {
        $this->resetTable($driver);
        $callbackFired = false;

        $user = UserFactory::new()
            ->afterMaking(function ($model) use (&$callbackFired) {
                $callbackFired = true;
            })
            ->make();

        $this->assertTrue($callbackFired, "[$dbName] afterMaking callback should fire");
    }

    // =========================================================================
    // Factory create() Tests (Persisted to Database)
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function createPersistsSingleModel(string $dbName, Driver $driver): void
    {
        $this->resetTable($driver);

        $user = UserFactory::new()->create();

        $this->assertInstanceOf(User::class, $user);
        $this->assertNotNull($user->get('id'), "[$dbName] Created model should have an ID");

        // Verify in database
        $driver->setQuery("SELECT COUNT(*) as cnt FROM factory_users");
        $row = $driver->loadObject();
        $this->assertEquals(1, (int) $row->cnt, "[$dbName] create() should insert one row");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function createWithCountPersistsMultipleModels(string $dbName, Driver $driver): void
    {
        $this->resetTable($driver);

        $users = UserFactory::new()->count(3)->create();

        $this->assertInstanceOf(Rows::class, $users);
        $this->assertCount(3, $users);

        // Verify in database
        $driver->setQuery("SELECT COUNT(*) as cnt FROM factory_users");
        $row = $driver->loadObject();
        $this->assertEquals(3, (int) $row->cnt, "[$dbName] create() should insert 3 rows");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function createAppliesAttributeOverrides(string $dbName, Driver $driver): void
    {
        $this->resetTable($driver);

        $user = UserFactory::new()->create(['name' => 'Custom Name']);

        $this->assertEquals('Custom Name', $user->get('name'));

        // Verify in database
        $driver->setQuery("SELECT name FROM factory_users WHERE id = " . (int) $user->get('id'));
        $row = $driver->loadObject();
        $this->assertEquals('Custom Name', $row->name, "[$dbName] Override should be persisted");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function createAppliesStates(string $dbName, Driver $driver): void
    {
        $this->resetTable($driver);

        $user = UserFactory::new()->inactive()->create();

        $this->assertEquals('inactive', $user->get('status'));

        // Verify in database
        $driver->setQuery("SELECT status FROM factory_users WHERE id = " . (int) $user->get('id'));
        $row = $driver->loadObject();
        $this->assertEquals('inactive', $row->status, "[$dbName] State should be persisted");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function afterCreatingCallbackFires(string $dbName, Driver $driver): void
    {
        $this->resetTable($driver);
        $callbackFired = false;
        $callbackModel = null;

        $user = UserFactory::new()
            ->afterCreating(function ($model) use (&$callbackFired, &$callbackModel) {
                $callbackFired = true;
                $callbackModel = $model;
            })
            ->create();

        $this->assertTrue($callbackFired, "[$dbName] afterCreating callback should fire");
        $this->assertNotNull($callbackModel->get('id'), "[$dbName] Model in callback should have ID (already saved)");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function createGeneratesUniqueModels(string $dbName, Driver $driver): void
    {
        $this->resetTable($driver);
        Factory::resetSequence(1);

        $users = UserFactory::new()->count(3)->create();

        // Each user should have a unique name (due to sequence)
        $names = [];
        foreach ($users as $user) {
            $names[] = $user->get('name');
        }
        $this->assertCount(3, array_unique($names), "[$dbName] Factory should generate unique names");
    }
}
