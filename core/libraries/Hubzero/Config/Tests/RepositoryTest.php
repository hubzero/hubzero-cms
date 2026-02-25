<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Config\Tests;

use PHPUnit\Framework\TestCase;
use Hubzero\Config\Repository;
use Hubzero\Config\Registry;
use stdClass;
use Hubzero\Facades\Config;

/**
 * Repository tests
 */
class RepositoryTest extends TestCase
{
    /**
     * Path to test fixture directory (contains config/ subdirectory)
     *
     * @var  string
     */
    protected $fixturePath;

    /**
     * Set up test fixtures
     *
     * @return  void
     */
    protected function setUp(): void
    {
        $this->fixturePath = __DIR__ . '/Files';
    }

    // ---------------------------------------------------------------
    // Construction
    // ---------------------------------------------------------------

    /**
     * Tests that the constructor loads config from a single string path
     *
     * @return  void
     */
    public function testConstructWithStringPath()
    {
        $config = new Repository($this->fixturePath);

        $this->assertInstanceOf(Repository::class, $config);
        $this->assertInstanceOf(Registry::class, $config);
        $this->assertEquals('development', $config->get('app.application_env'));
        $this->assertEquals('ckeditor', $config->get('app.editor'));
    }

    /**
     * Tests that the constructor accepts an array of paths
     *
     * @return  void
     */
    public function testConstructWithArrayOfPaths()
    {
        $config = new Repository([$this->fixturePath]);

        $this->assertEquals('development', $config->get('app.application_env'));
        $this->assertEquals('1', $config->get('seo.sef'));
    }

    /**
     * Tests that later paths override earlier paths while preserving
     * non-overlapping values from both
     *
     * @return  void
     */
    public function testMultiplePathsOverrideInOrder()
    {
        $basePath = $this->fixturePath . '/MultiPath/base';
        $overridePath = $this->fixturePath . '/MultiPath/override';

        $config = new Repository([$basePath, $overridePath]);

        // Override path wins for keys it redefines
        $this->assertEquals('production', $config->get('app.application_env'));
        $this->assertEquals('none', $config->get('app.editor'));
        $this->assertEquals('0', $config->get('app.debug'));

        // Base path values survive for keys not overridden
        $this->assertEquals('25', $config->get('app.list_limit'));

        // Group only in base should be present
        $this->assertTrue($config->has('seo'));
        $this->assertEquals('1', $config->get('seo.sef'));

        // Group only in override should be present
        $this->assertTrue($config->has('session'));
        $this->assertEquals('database', $config->get('session.session_handler'));
    }

    /**
     * Tests that reversed path order produces different results
     *
     * @return  void
     */
    public function testPathOrderMatters()
    {
        $basePath = $this->fixturePath . '/MultiPath/base';
        $overridePath = $this->fixturePath . '/MultiPath/override';

        $configBaseFirst = new Repository([$basePath, $overridePath]);
        $configOverrideFirst = new Repository([$overridePath, $basePath]);

        // When base is last, its values win
        $this->assertEquals('production', $configBaseFirst->get('app.application_env'));
        $this->assertEquals('development', $configOverrideFirst->get('app.application_env'));
    }

    /**
     * Tests that a nonexistent path produces an empty config
     *
     * @return  void
     */
    public function testConstructWithNonexistentPath()
    {
        $config = new Repository('/nonexistent/path/that/does/not/exist');

        $this->assertNull($config->get('app.debug'));
        $this->assertEquals('fallback', $config->get('app.debug', 'fallback'));
        $this->assertEquals(0, $config->count());
    }

    /**
     * Tests mixing valid and nonexistent paths
     *
     * @return  void
     */
    public function testMixedValidAndInvalidPaths()
    {
        $config = new Repository(['/nonexistent', $this->fixturePath]);

        // Valid path's config should still load
        $this->assertEquals('development', $config->get('app.application_env'));
    }

    // ---------------------------------------------------------------
    // Dot-notation get()
    // ---------------------------------------------------------------

    /**
     * Tests dot-notation access for grouped config values
     *
     * @return  void
     */
    public function testGetWithDotNotation()
    {
        $config = new Repository($this->fixturePath);

        $this->assertEquals('development', $config->get('app.application_env'));
        $this->assertEquals('1', $config->get('app.debug'));
        $this->assertEquals('1', $config->get('seo.sef'));
        $this->assertEquals('0', $config->get('seo.unicodeslugs'));
        $this->assertEquals('0', $config->get('seo.sitename_pagetitles'));
    }

    /**
     * Tests getting an entire config group as array
     *
     * @return  void
     */
    public function testGetEntireGroup()
    {
        $config = new Repository($this->fixturePath);

        $app = $config->get('app');

        // Registry stores groups as stdClass objects internally
        $this->assertInstanceOf(stdClass::class, $app);
        $this->assertEquals('development', $app->application_env);
        $this->assertEquals('ckeditor', $app->editor);
        $this->assertEquals('1', $app->debug);
    }

    /**
     * Tests that bare keys without dots search across all config groups
     * (backward compatibility with legacy monolithic config)
     *
     * @return  void
     */
    public function testBareKeySearchesAllGroups()
    {
        $config = new Repository($this->fixturePath);

        // 'application_env' is in the 'app' group
        $this->assertEquals('development', $config->get('application_env'));

        // 'editor' is in the 'app' group
        $this->assertEquals('ckeditor', $config->get('editor'));

        // 'unicodeslugs' is in the 'seo' group
        $this->assertEquals('0', $config->get('unicodeslugs'));
    }

    /**
     * Tests that get() returns default for missing keys at all levels
     *
     * @return  void
     */
    public function testGetReturnsDefaultForMissingKey()
    {
        $config = new Repository($this->fixturePath);

        // Missing bare key
        $this->assertNull($config->get('nonexistent'));
        $this->assertEquals('default_val', $config->get('nonexistent', 'default_val'));

        // Missing key in existing group
        $this->assertNull($config->get('app.nonexistent_key'));
        $this->assertEquals('fallback', $config->get('app.nonexistent_key', 'fallback'));

        // Missing group entirely
        $this->assertNull($config->get('fake_group.fake_key'));
        $this->assertEquals('nope', $config->get('fake_group.fake_key', 'nope'));

        // Deeply missing
        $this->assertEquals('deep', $config->get('a.b.c.d.e', 'deep'));
    }

    /**
     * Tests that get() returns default for empty path
     *
     * @return  void
     */
    public function testGetReturnsDefaultForEmptyPath()
    {
        $config = new Repository($this->fixturePath);

        $this->assertNull($config->get(''));
        $this->assertEquals('default', $config->get('', 'default'));
    }

    // ---------------------------------------------------------------
    // set()
    // ---------------------------------------------------------------

    /**
     * Tests setting values with dot notation
     *
     * @return  void
     */
    public function testSetWithDotNotation()
    {
        $config = new Repository($this->fixturePath);

        // Override existing value
        $config->set('app.debug', '0');
        $this->assertEquals('0', $config->get('app.debug'));

        // Add new key to existing group
        $config->set('app.custom_key', 'custom_value');
        $this->assertEquals('custom_value', $config->get('app.custom_key'));
    }

    /**
     * Tests creating a new group via dot-notation set
     *
     * @return  void
     */
    public function testSetCreatesNewGroup()
    {
        $config = new Repository($this->fixturePath);

        $config->set('newgroup.key1', 'val1');

        $this->assertEquals('val1', $config->get('newgroup.key1'));

        // Bare key search should find it in the new group
        $this->assertEquals('val1', $config->get('key1'));
    }

    /**
     * Tests setting deeply nested values creates intermediate nodes
     *
     * @return  void
     */
    public function testSetDeeplyNested()
    {
        $config = new Repository($this->fixturePath);

        $config->set('deep.level1.level2', 'bottom');

        $this->assertEquals('bottom', $config->get('deep.level1.level2'));
    }

    /**
     * Tests that setting an array value via dot-notation makes its keys
     * accessible via deeper dot notation
     *
     * @return  void
     */
    public function testSetArrayValue()
    {
        $config = new Repository($this->fixturePath);

        $config->set('database.driver', 'mysql');
        $config->set('database.host', 'localhost');
        $config->set('database.name', 'testdb');

        $this->assertEquals('mysql', $config->get('database.driver'));
        $this->assertEquals('localhost', $config->get('database.host'));
        $this->assertEquals('testdb', $config->get('database.name'));
    }

    // ---------------------------------------------------------------
    // ArrayAccess
    // ---------------------------------------------------------------

    /**
     * Tests reading config via array syntax
     *
     * @return  void
     */
    public function testArrayAccessGet()
    {
        $config = new Repository($this->fixturePath);

        $this->assertEquals('development', $config['app.application_env']);
        $this->assertEquals('1', $config['app.debug']);
        $this->assertEquals('1', $config['seo.sef']);
    }

    /**
     * Tests writing config via array syntax
     *
     * @return  void
     */
    public function testArrayAccessSet()
    {
        $config = new Repository($this->fixturePath);

        $config['app.debug'] = '0';

        $this->assertEquals('0', $config['app.debug']);
        $this->assertEquals('0', $config->get('app.debug'));
    }

    /**
     * Tests isset via array syntax
     *
     * @return  void
     */
    public function testArrayAccessIsset()
    {
        $config = new Repository($this->fixturePath);

        $this->assertTrue(isset($config['app']));
        $this->assertTrue(isset($config['app.debug']));
        $this->assertTrue(isset($config['seo.sef']));
        $this->assertFalse(isset($config['nonexistent']));
        $this->assertFalse(isset($config['app.nonexistent']));
    }

    /**
     * Tests unset via array syntax
     *
     * @return  void
     */
    public function testArrayAccessUnset()
    {
        $config = new Repository($this->fixturePath);

        $this->assertTrue(isset($config['app.debug']));

        unset($config['app.debug']);

        $this->assertFalse(isset($config['app.debug']));
    }

    // ---------------------------------------------------------------
    // Registry methods on config data
    // ---------------------------------------------------------------

    /**
     * Tests has() for existing and missing paths
     *
     * @return  void
     */
    public function testHas()
    {
        $config = new Repository($this->fixturePath);

        $this->assertTrue($config->has('app'));
        $this->assertTrue($config->has('seo'));
        $this->assertTrue($config->has('app.debug'));
        $this->assertTrue($config->has('seo.sef'));
        $this->assertFalse($config->has('nonexistent'));
        $this->assertFalse($config->has('app.nonexistent'));
        $this->assertFalse($config->has('fake.group.key'));
    }

    /**
     * Tests def() sets a default only when the key is not already set
     *
     * @return  void
     */
    public function testDef()
    {
        $config = new Repository($this->fixturePath);

        // Should NOT overwrite an existing value
        $config->def('app.debug', '999');
        $this->assertEquals('1', $config->get('app.debug'));

        // Should set value for a missing key
        $config->def('app.new_setting', 'new_default');
        $this->assertEquals('new_default', $config->get('app.new_setting'));
    }

    /**
     * Tests toArray() returns complete nested array
     *
     * @return  void
     */
    public function testToArray()
    {
        $config = new Repository($this->fixturePath);

        $arr = $config->toArray();

        $this->assertIsArray($arr);
        $this->assertArrayHasKey('app', $arr);
        $this->assertArrayHasKey('seo', $arr);
        $this->assertIsArray($arr['app']);
        $this->assertIsArray($arr['seo']);
        $this->assertEquals('development', $arr['app']['application_env']);
        $this->assertEquals('1', $arr['seo']['sef']);
    }

    /**
     * Tests flatten() collapses nested structure to dot-notation keys
     *
     * @return  void
     */
    public function testFlatten()
    {
        $config = new Repository($this->fixturePath);

        $flat = $config->flatten();

        $this->assertIsArray($flat);
        $this->assertArrayHasKey('app.application_env', $flat);
        $this->assertArrayHasKey('app.debug', $flat);
        $this->assertArrayHasKey('seo.sef', $flat);
        $this->assertArrayHasKey('seo.unicodeslugs', $flat);
        $this->assertEquals('development', $flat['app.application_env']);
        $this->assertEquals('1', $flat['app.debug']);
    }

    /**
     * Tests count() returns number of top-level config groups
     *
     * @return  void
     */
    public function testCount()
    {
        $config = new Repository($this->fixturePath);

        // Files/config/ has app.php and seo.php
        $this->assertEquals(2, $config->count());

        // MultiPath with override adds session group
        $config2 = new Repository([
            $this->fixturePath . '/MultiPath/base',
            $this->fixturePath . '/MultiPath/override',
        ]);
        $this->assertEquals(3, $config2->count()); // app, seo, session
    }

    /**
     * Tests merge() combines additional data into config
     *
     * @return  void
     */
    public function testMerge()
    {
        $config = new Repository($this->fixturePath);

        $extra = [
            'cache' => ['driver' => 'file', 'path' => '/tmp/cache'],
            'app' => ['custom' => 'merged_value'],
        ];

        $result = $config->merge($extra, true);

        $this->assertTrue($result);

        // New group added
        $this->assertEquals('file', $config->get('cache.driver'));

        // New key merged into existing group
        $this->assertEquals('merged_value', $config->get('app.custom'));

        // Original values preserved
        $this->assertEquals('development', $config->get('app.application_env'));
    }

    /**
     * Tests merge() with null returns false
     *
     * @return  void
     */
    public function testMergeWithNullReturnsFalse()
    {
        $config = new Repository($this->fixturePath);

        $this->assertFalse($config->merge(null));
    }

    /**
     * Tests extract() pulls out a sub-registry for a config group
     *
     * @return  void
     */
    public function testExtract()
    {
        $config = new Repository($this->fixturePath);

        $appConfig = $config->extract('app');

        $this->assertInstanceOf(Registry::class, $appConfig);
        $this->assertEquals('development', $appConfig->get('application_env'));
        $this->assertEquals('ckeditor', $appConfig->get('editor'));
        $this->assertEquals('1', $appConfig->get('debug'));

        // Nonexistent group returns null
        $this->assertNull($config->extract('nonexistent'));
    }

    /**
     * Tests that config is iterable via foreach
     *
     * @return  void
     */
    public function testIterable()
    {
        $config = new Repository($this->fixturePath);

        $groups = [];
        foreach ($config as $key => $value) {
            $groups[] = $key;
        }

        $this->assertContains('app', $groups);
        $this->assertContains('seo', $groups);
        $this->assertCount(2, $groups);
    }

    /**
     * Tests JSON serialization via json_encode()
     *
     * @return  void
     */
    public function testJsonSerialize()
    {
        $config = new Repository($this->fixturePath);

        $json = json_encode($config);

        $this->assertIsString($json);
        $this->assertNotFalse($json);

        $decoded = json_decode($json, true);

        $this->assertArrayHasKey('app', $decoded);
        $this->assertArrayHasKey('seo', $decoded);
        $this->assertEquals('development', $decoded['app']['application_env']);
    }

    /**
     * Tests toString() serialization
     *
     * @return  void
     */
    public function testToString()
    {
        $config = new Repository($this->fixturePath);

        $json = $config->toString('json');

        $this->assertIsString($json);

        $decoded = json_decode($json, true);
        $this->assertEquals('development', $decoded['app']['application_env']);

        // __toString should also work
        $str = (string) $config;
        $this->assertIsString($str);
        $this->assertStringContainsString('application_env', $str);
    }

    // ---------------------------------------------------------------
    // Edge cases
    // ---------------------------------------------------------------

    /**
     * Tests that clone creates an independent copy
     *
     * @return  void
     */
    public function testCloneIsIndependent()
    {
        $config = new Repository($this->fixturePath);
        $clone = clone $config;

        $clone->set('app.debug', '999');

        $this->assertEquals('999', $clone->get('app.debug'));
        $this->assertEquals('1', $config->get('app.debug'));
    }

    /**
     * Tests that all config files in the directory are loaded
     *
     * @return  void
     */
    public function testLoadsAllConfigFiles()
    {
        $config = new Repository($this->fixturePath);

        $this->assertTrue($config->has('app'));
        $this->assertTrue($config->has('seo'));

        // Verify values from each file
        $this->assertEquals('ckeditor', $config->get('app.editor'));
        $this->assertEquals('0', $config->get('seo.unicodeslugs'));
    }

    /**
     * Tests that .html files and unknown extensions are skipped
     *
     * @return  void
     */
    public function testSkipsNonConfigFiles()
    {
        // Create a temp .bak file in config dir
        $bakFile = $this->fixturePath . '/config/temp_test.bak';
        file_put_contents($bakFile, '<?php return ["should" => "skip"];');

        try {
            $config = new Repository($this->fixturePath);

            $this->assertFalse($config->has('temp_test'));
            $this->assertTrue($config->has('app'));
        } finally {
            if (file_exists($bakFile)) {
                unlink($bakFile);
            }
        }
    }

    /**
     * Tests reset() clears all loaded config
     *
     * @return  void
     */
    public function testReset()
    {
        $config = new Repository($this->fixturePath);

        $this->assertTrue($config->has('app'));
        $this->assertGreaterThan(0, $config->count());

        $config->reset();

        $this->assertFalse($config->has('app'));
        $this->assertFalse($config->has('seo'));
        $this->assertEquals(0, $config->count());
    }

    /**
     * Tests deeply nested array access with defaults
     *
     * @return  void
     */
    public function testNestedArrayAccess()
    {
        $config = new Repository($this->fixturePath);

        $config->set('services.mail.driver', 'smtp');
        $config->set('services.mail.host', 'mail.example.com');
        $config->set('services.mail.port', 587);

        $this->assertEquals('smtp', $config->get('services.mail.driver'));
        $this->assertEquals('mail.example.com', $config->get('services.mail.host'));
        $this->assertEquals(587, $config->get('services.mail.port'));
        $this->assertNull($config->get('services.mail.nonexistent'));
        $this->assertEquals('tls', $config->get('services.mail.encryption', 'tls'));
    }

    /**
     * Tests that the bare key search returns the last group's value
     * when the same key exists in multiple groups
     *
     * @return  void
     */
    public function testBareKeyWithDuplicateAcrossGroups()
    {
        $config = new Repository($this->fixturePath);

        // 'sef' exists in both app and seo groups
        // The bare key search iterates all groups and keeps the last match
        $value = $config->get('sef');
        $this->assertEquals('1', $value);
    }

    /**
     * Tests that toObject() returns a stdClass tree
     *
     * @return  void
     */
    public function testToObject()
    {
        $config = new Repository($this->fixturePath);

        $obj = $config->toObject();

        $this->assertInstanceOf(stdClass::class, $obj);
        $this->assertTrue(isset($obj->app));
        $this->assertTrue(isset($obj->seo));
    }

    // ---------------------------------------------------------------
    // Real-world access patterns
    // ---------------------------------------------------------------

    /**
     * Path to multi-group test fixture (mirrors real app/config/ structure)
     *
     * @return  string
     */
    protected function multiGroupPath()
    {
        return __DIR__ . '/Files/MultiGroup';
    }

    /**
     * Tests bare key access across multiple config groups,
     * matching patterns used throughout bootstrap service providers:
     *   $app['config']->get('debug')
     *   $app['config']->get('secret')
     *   $app['config']->get('session_handler')
     *   $app['config']->get('dbtype')
     *   $app['config']->get('lifetime')
     *
     * @return  void
     */
    public function testBareKeyAccessAcrossMultipleGroups()
    {
        $config = new Repository($this->multiGroupPath());

        // app group keys
        $this->assertEquals('1', $config->get('debug'));
        $this->assertEquals('abc123secret', $config->get('secret'));
        $this->assertEquals('0', $config->get('gzip'));
        $this->assertEquals('ckeditor', $config->get('editor'));
        $this->assertEquals('Test Hub', $config->get('sitename'));

        // database group keys
        $this->assertEquals('mysql', $config->get('dbtype'));
        $this->assertEquals('localhost', $config->get('host'));
        $this->assertEquals('hubzero_db', $config->get('db'));
        $this->assertEquals('jos_', $config->get('dbprefix'));

        // session group keys
        $this->assertEquals('database', $config->get('session_handler'));
        $this->assertEquals('15', $config->get('lifetime'));

        // cache group keys
        $this->assertEquals('0', $config->get('caching'));
        $this->assertEquals('file', $config->get('cache_handler'));

        // mail group keys
        $this->assertEquals('smtp', $config->get('mailer'));
        $this->assertEquals('noreply@example.com', $config->get('mailfrom'));
    }

    /**
     * Tests bare key access returns default when key doesn't exist
     * in any group — mirrors bootstrap fallback patterns
     *
     * @return  void
     */
    public function testBareKeyReturnsDefaultWhenMissing()
    {
        $config = new Repository($this->multiGroupPath());

        $this->assertNull($config->get('nonexistent_key'));
        $this->assertEquals('fallback', $config->get('nonexistent_key', 'fallback'));
    }

    /**
     * Tests boolean-like config values ('0', '1') are preserved as strings
     * when accessed via bare key — mirrors `if ($config->get('debug'))` pattern
     *
     * @return  void
     */
    public function testBareKeyPreservesStringBooleans()
    {
        $config = new Repository($this->multiGroupPath());

        // debug = '1' (truthy string)
        $debug = $config->get('debug');
        $this->assertSame('1', $debug);
        $this->assertTrue((bool) $debug);

        // gzip = '0' (falsy string)
        $gzip = $config->get('gzip');
        $this->assertSame('0', $gzip);
        $this->assertFalse((bool) $gzip);

        // caching = '0'
        $caching = $config->get('caching');
        $this->assertSame('0', $caching);
    }

    /**
     * Tests setting a bare key finds the matching group and updates
     * it there — symmetric with how bare key get() works.
     * Mirrors Config::set('gzip', 0) pattern used by system plugins.
     *
     * @return  void
     */
    public function testBareKeySetFindsGroup()
    {
        $config = new Repository($this->multiGroupPath());

        // Verify initial value from app group
        $this->assertEquals('0', $config->get('gzip'));

        // Set via bare key — finds app group and updates there
        $config->set('gzip', 1);

        // Both bare key and dot-notation return the updated value
        $this->assertEquals(1, $config->get('gzip'));
        $this->assertEquals(1, $config->get('app.gzip'));
    }

    /**
     * Tests that setting a bare key that doesn't exist in any group
     * throws InvalidArgumentException
     *
     * @return  void
     */
    public function testBareKeySetThrowsForUnknownKey()
    {
        $config = new Repository($this->multiGroupPath());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Config key 'nonexistent' not found in any group");

        $config->set('nonexistent', 'value');
    }

    /**
     * Tests that setting via dot-notation then retrieving via bare key
     * finds the updated value
     *
     * @return  void
     */
    public function testSetDotNotationGetBareKey()
    {
        $config = new Repository($this->multiGroupPath());

        // Change debug via dot notation
        $config->set('app.debug', '0');

        // Bare key should return the updated value from the app group
        $this->assertEquals('0', $config->get('debug'));
    }

    /**
     * Tests getting an entire group returns stdClass, and how to convert
     * to array using json_decode(json_encode()) — mirrors pattern in
     * RateLimitService and other providers
     *
     * @return  void
     */
    public function testGroupToArrayViaJsonRoundtrip()
    {
        $config = new Repository($this->multiGroupPath());

        $dbGroup = $config->get('database');

        // Group comes back as stdClass
        $this->assertInstanceOf(stdClass::class, $dbGroup);

        // Convert to array via json roundtrip (real-world pattern)
        $dbArray = json_decode(json_encode($dbGroup), true);

        $this->assertIsArray($dbArray);
        $this->assertEquals('localhost', $dbArray['host']);
        $this->assertEquals('hubzero_db', $dbArray['db']);
        $this->assertEquals('mysql', $dbArray['dbtype']);
        $this->assertEquals('jos_', $dbArray['dbprefix']);
    }

    /**
     * Tests extract()->toArray() pattern for getting a config group
     * as an associative array — mirrors ClientDetector pattern:
     *   $config->extract('database')->toArray()
     *
     * @return  void
     */
    public function testExtractToArrayPattern()
    {
        $config = new Repository($this->multiGroupPath());

        $dbConfig = $config->extract('database')->toArray();

        $this->assertIsArray($dbConfig);
        $this->assertEquals('localhost', $dbConfig['host']);
        $this->assertEquals('3306', $dbConfig['port']);
        $this->assertEquals('hubzero', $dbConfig['user']);
        $this->assertEquals('hubzero_db', $dbConfig['db']);
        $this->assertEquals('jos_', $dbConfig['dbprefix']);
        $this->assertEquals('mysql', $dbConfig['dbtype']);
    }

    /**
     * Tests that extract() on a nonexistent group returns null —
     * callers must guard against this
     *
     * @return  void
     */
    public function testExtractNonexistentGroupReturnsNull()
    {
        $config = new Repository($this->multiGroupPath());

        $this->assertNull($config->extract('nonexistent'));
    }

    /**
     * Tests that Repository is instanceof Repository and Registry —
     * mirrors EventServiceProvider instanceof check:
     *   if ($app['config'] instanceof Repository)
     *
     * @return  void
     */
    public function testInstanceOfChecks()
    {
        $config = new Repository($this->multiGroupPath());

        $this->assertInstanceOf(Repository::class, $config);
        $this->assertInstanceOf(Registry::class, $config);
    }

    /**
     * Tests dot-notation access to individual database config values —
     * mirrors ClientDetector pattern:
     *   $config->get('database.host')
     *   $config->get('database.db')
     *   $config->get('database.user')
     *
     * @return  void
     */
    public function testDotNotationDatabaseAccess()
    {
        $config = new Repository($this->multiGroupPath());

        $this->assertEquals('localhost', $config->get('database.host'));
        $this->assertEquals('hubzero_db', $config->get('database.db'));
        $this->assertEquals('hubzero', $config->get('database.user'));
        $this->assertEquals('3306', $config->get('database.port'));
        $this->assertEquals('secret_password', $config->get('database.password'));
    }

    /**
     * Tests that all groups are accessible via get() returning stdClass —
     * each config file becomes a group
     *
     * @return  void
     */
    public function testAllGroupsAccessible()
    {
        $config = new Repository($this->multiGroupPath());

        $groups = ['app', 'database', 'session', 'cache', 'mail'];

        foreach ($groups as $group) {
            $value = $config->get($group);
            $this->assertInstanceOf(
                stdClass::class,
                $value,
                "Group '$group' should return stdClass"
            );
        }
    }

    /**
     * Tests that group stdClass properties match dot-notation values —
     * ensures both access patterns return the same data
     *
     * @return  void
     */
    public function testGroupPropertyMatchesDotNotation()
    {
        $config = new Repository($this->multiGroupPath());

        $app = $config->get('app');

        $this->assertEquals($app->debug, $config->get('app.debug'));
        $this->assertEquals($app->editor, $config->get('app.editor'));
        $this->assertEquals($app->secret, $config->get('app.secret'));
        $this->assertEquals($app->sitename, $config->get('app.sitename'));
    }

    /**
     * Tests the flatten() output contains all keys from all groups
     * with dot-notation prefixes — this is the flattened key assumption
     *
     * @return  void
     */
    public function testFlattenContainsAllGroupKeys()
    {
        $config = new Repository($this->multiGroupPath());

        $flat = $config->flatten();

        // Spot-check keys from each group
        $expectedKeys = [
            'app.debug',
            'app.secret',
            'app.gzip',
            'database.host',
            'database.dbtype',
            'database.dbprefix',
            'session.session_handler',
            'session.lifetime',
            'cache.caching',
            'cache.cache_handler',
            'mail.mailer',
            'mail.mailfrom',
        ];

        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $flat, "Flattened config missing key: $key");
        }
    }

    /**
     * Tests toArray() returns pure associative arrays at all levels —
     * no stdClass objects remain after conversion
     *
     * @return  void
     */
    public function testToArrayReturnsNestedArrays()
    {
        $config = new Repository($this->multiGroupPath());

        $arr = $config->toArray();

        $this->assertIsArray($arr);
        $this->assertIsArray($arr['app']);
        $this->assertIsArray($arr['database']);
        $this->assertIsArray($arr['session']);

        // Values are accessible with array syntax
        $this->assertEquals('1', $arr['app']['debug']);
        $this->assertEquals('localhost', $arr['database']['host']);
        $this->assertEquals('database', $arr['session']['session_handler']);
    }

    /**
     * Tests that count() returns the number of config groups loaded
     *
     * @return  void
     */
    public function testCountReflectsAllGroups()
    {
        $config = new Repository($this->multiGroupPath());

        // MultiGroup has: app, database, session, cache, mail
        $this->assertEquals(5, $config->count());
    }

    /**
     * Tests that bare key set() updates the group value in place —
     * both bare and dot-notation get() reflect the change
     *
     * @return  void
     */
    public function testBareKeySetUpdatesGroupValue()
    {
        $config = new Repository($this->multiGroupPath());

        // 'debug' exists in app group
        $this->assertEquals('1', $config->get('debug'));
        $this->assertEquals('1', $config->get('app.debug'));

        // Set via bare key
        $config->set('debug', '0');

        // Both access patterns reflect the change
        $this->assertEquals('0', $config->get('debug'));
        $this->assertEquals('0', $config->get('app.debug'));
    }

    /**
     * Tests that config values set via set() persist through multiple
     * get() calls — no lazy-loading or caching bugs
     *
     * @return  void
     */
    public function testSetValuePersistsAcrossGets()
    {
        $config = new Repository($this->multiGroupPath());

        $config->set('app.custom_setting', 'custom_value');

        // Multiple gets return the same value
        $this->assertEquals('custom_value', $config->get('app.custom_setting'));
        $this->assertEquals('custom_value', $config->get('app.custom_setting'));
        $this->assertEquals('custom_value', $config->get('custom_setting'));
    }
}
