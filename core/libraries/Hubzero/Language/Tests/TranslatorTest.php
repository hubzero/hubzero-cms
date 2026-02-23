<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses -- Test file with helper stubs
namespace Hubzero\Language\Tests {

    use Hubzero\Language\Translator;
    use PHPUnit\Framework\Attributes\Test;
    use PHPUnit\Framework\TestCase;

    /**
     * A Translator subclass that lets us inject paths and bypass App dependencies.
     *
     * The real Translator constructor calls \App::get('config') and uses PATH_APP/PATH_CORE
     * constants, which are set by the project's autoloader. To test the Translator in isolation
     * we subclass it, skip the constructor, and inject state directly.
     */
    class TestableTranslator extends Translator
    {
        /**
         * Create a Translator without triggering the real constructor.
         * This avoids all filesystem and App facade dependencies.
         */
        public static function make(
            string $lang = 'en-GB',
            bool $debug = false,
            string $client = 'site'
        ): self {
            // Bypass parent constructor entirely
            $ref = new \ReflectionClass(self::class);
            $instance = $ref->newInstanceWithoutConstructor();

            $instance->strings = [];
            $instance->override = [];
            $instance->overridesMerged = false;
            $instance->lang = $lang;
            $instance->default = 'en-GB';
            $instance->debug = $debug;
            $instance->client = $client;
            $instance->paths = [];
            $instance->orphans = [];
            $instance->used = [];
            $instance->errorfiles = [];

            return $instance;
        }

        public function setStrings(array $strings): void
        {
            $this->strings = $strings;
        }

        public function getStrings(): array
        {
            return $this->strings;
        }

        public function setOverrideStrings(array $override): void
        {
            $this->override = $override;
        }

        /**
         * Override load() to bypass the \App::get('config')->get('debug_lang') call.
         * This makes the method testable without the App facade.
         */
        public function load(
            $extension = 'hubzero',
            $basePath = null,
            $lang = null,
            $reload = false,
            $default = true
        ) {
            if (!$lang) {
                $lang = $this->lang;
            }

            $path = self::getLanguagePath($basePath, $lang);

            $internal = $extension == 'hubzero' || $extension == '';
            $filename = $internal ? $lang : $lang . '.' . $extension;
            $filename = "$path/$filename.ini";

            $result = false;

            if (isset($this->paths[$extension][$filename]) && !$reload) {
                $result = $this->paths[$extension][$filename];
            } else {
                $result = $this->loadLanguage($filename, $extension);
            }

            return $result;
        }
    }

    class TranslatorTest extends TestCase
    {
        private string $fixtureDir;

        protected function setUp(): void
        {
            $this->fixtureDir = sys_get_temp_dir() . '/hubzero_lang_test_' . getmypid();
            $this->removeDir($this->fixtureDir);
            mkdir($this->fixtureDir, 0755, true);
        }

        protected function tearDown(): void
        {
            $this->removeDir($this->fixtureDir);
        }

        private function removeDir(string $dir): void
        {
            if (!is_dir($dir)) {
                return;
            }
            $items = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($items as $item) {
                $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
            }
            rmdir($dir);
        }

        private function writeIni(string $relativePath, array $entries): string
        {
            $path = $this->fixtureDir . '/' . $relativePath;
            $dir = dirname($path);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $lines = [];
            foreach ($entries as $key => $value) {
                $lines[] = "$key=\"$value\"";
            }
            file_put_contents($path, implode("\n", $lines) . "\n");
            return $path;
        }

        // -----------------------------------------------------------------
        //  translate() basics
        // -----------------------------------------------------------------

        #[Test]
        public function translateReturnsValueForKnownKey(): void
        {
            $t = TestableTranslator::make();
            $t->setStrings(['HELLO' => 'Hello', 'GOODBYE' => 'Goodbye']);

            $this->assertSame('Hello', $t->translate('HELLO'));
            $this->assertSame('Goodbye', $t->translate('GOODBYE'));
        }

        #[Test]
        public function translateReturnsKeyItselfWhenMissing(): void
        {
            $t = TestableTranslator::make();

            $this->assertSame('NO_SUCH_KEY', $t->translate('NO_SUCH_KEY'));
        }

        #[Test]
        public function translateIsCaseInsensitive(): void
        {
            $t = TestableTranslator::make();
            $t->setStrings(['HELLO' => 'Hello']);

            $this->assertSame('Hello', $t->translate('hello'));
            $this->assertSame('Hello', $t->translate('Hello'));
            $this->assertSame('Hello', $t->translate('HELLO'));
        }

        #[Test]
        public function translateEmptyStringReturnsEmpty(): void
        {
            $t = TestableTranslator::make();

            $this->assertSame('', $t->translate(''));
        }

        #[Test]
        public function translateDebugModeWrapsFoundKey(): void
        {
            $t = TestableTranslator::make(debug: true);
            $t->setStrings(['HELLO' => 'Hello']);

            $this->assertSame('**Hello**', $t->translate('HELLO'));
        }

        #[Test]
        public function translateDebugModeWrapsMissingKey(): void
        {
            $t = TestableTranslator::make(debug: true);

            $this->assertSame('??MISSING??', $t->translate('MISSING'));
        }

        #[Test]
        public function translateJsSafeEscapesQuotes(): void
        {
            $t = TestableTranslator::make();
            $t->setStrings(['MSG' => 'He said "hello"']);

            $result = $t->translate('MSG', true);

            $this->assertSame('He said \\"hello\\"', $result);
        }

        #[Test]
        public function translateInterpretsBackslashes(): void
        {
            $t = TestableTranslator::make();
            $t->setStrings(['MSG' => 'Line1\\nLine2']);

            $result = $t->translate('MSG', false, true);

            $this->assertSame("Line1\nLine2", $result);
        }

        #[Test]
        public function translateBackslashInterpretationSkippedWhenJsSafe(): void
        {
            $t = TestableTranslator::make();
            $t->setStrings(['MSG' => 'Line1\\nLine2']);

            $result = $t->translate('MSG', true);

            // jsSafe takes precedence, no backslash interpretation
            $this->assertStringContainsString('\\n', $result);
        }

        // -----------------------------------------------------------------
        //  Deferred override merge
        // -----------------------------------------------------------------

        #[Test]
        public function overridesMergedOnFirstTranslate(): void
        {
            $t = TestableTranslator::make();
            $t->setStrings(['HELLO' => 'Hello']);
            $t->setOverrideStrings(['HELLO' => 'Overridden']);

            // Before first translate, strings still has original
            $this->assertSame('Hello', $t->getStrings()['HELLO']);

            // translate() triggers the merge
            $result = $t->translate('HELLO');

            $this->assertSame('Overridden', $result);
            // After translate, strings array is updated
            $this->assertSame('Overridden', $t->getStrings()['HELLO']);
        }

        #[Test]
        public function overridesOnlyMergeOnce(): void
        {
            $t = TestableTranslator::make();
            $t->setStrings(['A' => 'base']);
            $t->setOverrideStrings(['A' => 'override']);

            // First translate triggers merge
            $t->translate('A');

            // Manually set strings back to test that merge doesn't re-run
            $t->setStrings(array_merge($t->getStrings(), ['A' => 'manually changed']));

            // Second translate should NOT re-merge (overridesMerged is true)
            $this->assertSame('manually changed', $t->translate('A'));
        }

        #[Test]
        public function loadLanguageResetsMergeFlag(): void
        {
            $t = TestableTranslator::make();
            $t->setStrings(['A' => 'base']);
            $t->setOverrideStrings(['A' => 'override']);

            // First translate — merges overrides
            $this->assertSame('override', $t->translate('A'));

            // Load a new language file — should reset the merge flag
            $this->writeIni('comp/language/en-GB/en-GB.com_test.ini', [
                'COM_TEST' => 'Test Component',
            ]);
            $t->load('com_test', $this->fixtureDir . '/comp');

            // Override should re-apply after the new load
            $this->assertSame('override', $t->translate('A'));
            $this->assertSame('Test Component', $t->translate('COM_TEST'));
        }

        // -----------------------------------------------------------------
        //  hasKey() with overrides
        // -----------------------------------------------------------------

        #[Test]
        public function hasKeyFindsBaseStrings(): void
        {
            $t = TestableTranslator::make();
            $t->setStrings(['HELLO' => 'Hello']);

            $this->assertTrue($t->hasKey('HELLO'));
            $this->assertTrue($t->hasKey('hello'));
            $this->assertFalse($t->hasKey('MISSING'));
            $this->assertFalse($t->hasKey(''));
        }

        #[Test]
        public function hasKeyFindsOverrideKeys(): void
        {
            $t = TestableTranslator::make();
            $t->setOverrideStrings(['OVERRIDE_ONLY' => 'Exists']);

            // hasKey should trigger deferred merge for override keys
            $this->assertTrue($t->hasKey('OVERRIDE_ONLY'));
        }

        // -----------------------------------------------------------------
        //  loadOverride()
        // -----------------------------------------------------------------

        #[Test]
        public function loadOverrideLoadsIniFile(): void
        {
            $file = $this->writeIni('override.ini', [
                'KEY_A' => 'Value A',
                'KEY_B' => 'Value B',
            ]);

            $t = TestableTranslator::make();
            $result = $t->loadOverride($file);

            $this->assertTrue($result);
            $this->assertSame('Value A', $t->translate('KEY_A'));
            $this->assertSame('Value B', $t->translate('KEY_B'));
        }

        #[Test]
        public function loadOverrideMergesMultipleFiles(): void
        {
            $file1 = $this->writeIni('over1.ini', ['A' => 'First', 'B' => 'First']);
            $file2 = $this->writeIni('over2.ini', ['B' => 'Second', 'C' => 'Second']);

            $t = TestableTranslator::make();
            $t->loadOverride($file1);
            $t->loadOverride($file2);

            // Last loaded file wins for shared keys
            $this->assertSame('First', $t->translate('A'));
            $this->assertSame('Second', $t->translate('B'));
            $this->assertSame('Second', $t->translate('C'));
        }

        #[Test]
        public function loadOverrideReturnsFalseForMissingFile(): void
        {
            $t = TestableTranslator::make();

            $this->assertFalse($t->loadOverride('/nonexistent/file.ini'));
        }

        #[Test]
        public function loadOverrideReturnsFalseForEmptyFile(): void
        {
            $file = $this->fixtureDir . '/empty.ini';
            file_put_contents($file, "; just a comment\n");

            $t = TestableTranslator::make();

            $this->assertFalse($t->loadOverride($file));
        }

        #[Test]
        public function loadOverrideResetsMergeFlag(): void
        {
            $t = TestableTranslator::make();
            $t->setStrings(['HELLO' => 'Base']);

            // First override
            $file1 = $this->writeIni('over1.ini', ['HELLO' => 'Override 1']);
            $t->loadOverride($file1);

            // Trigger merge
            $this->assertSame('Override 1', $t->translate('HELLO'));

            // Second override after merge — should reset flag and re-merge
            $file2 = $this->writeIni('over2.ini', ['HELLO' => 'Override 2']);
            $t->loadOverride($file2);

            $this->assertSame('Override 2', $t->translate('HELLO'));
        }

        // -----------------------------------------------------------------
        //  Override priority: template > system > base
        // -----------------------------------------------------------------

        #[Test]
        public function overridePriorityOrder(): void
        {
            $t = TestableTranslator::make();
            $t->setStrings([
                'BASE_ONLY' => 'From Base',
                'BASE_AND_SYS' => 'From Base',
                'BASE_AND_TPL' => 'From Base',
                'ALL_THREE' => 'From Base',
            ]);

            // System override loaded first (lower priority)
            $sysFile = $this->writeIni('sys.ini', [
                'BASE_AND_SYS' => 'From System',
                'ALL_THREE' => 'From System',
            ]);
            $t->loadOverride($sysFile);

            // Template override loaded second (higher priority)
            $tplFile = $this->writeIni('tpl.ini', [
                'BASE_AND_TPL' => 'From Template',
                'ALL_THREE' => 'From Template',
            ]);
            $t->loadOverride($tplFile);

            $this->assertSame('From Base', $t->translate('BASE_ONLY'));
            $this->assertSame('From System', $t->translate('BASE_AND_SYS'));
            $this->assertSame('From Template', $t->translate('BASE_AND_TPL'));
            $this->assertSame('From Template', $t->translate('ALL_THREE'));
        }

        // -----------------------------------------------------------------
        //  load() — extension language files
        // -----------------------------------------------------------------

        #[Test]
        public function loadComponentLanguageFile(): void
        {
            $this->writeIni('comp/language/en-GB/en-GB.com_blog.ini', [
                'COM_BLOG_TITLE' => 'Blog',
                'COM_BLOG_NEW' => 'New Entry',
            ]);

            $t = TestableTranslator::make();
            $result = $t->load('com_blog', $this->fixtureDir . '/comp');

            $this->assertTrue($result);
            $this->assertSame('Blog', $t->translate('COM_BLOG_TITLE'));
            $this->assertSame('New Entry', $t->translate('COM_BLOG_NEW'));
        }

        #[Test]
        public function loadReturnsFalseForMissingFile(): void
        {
            $t = TestableTranslator::make();
            $result = $t->load('com_nonexistent', $this->fixtureDir . '/nope');

            $this->assertFalse($result);
        }

        #[Test]
        public function loadDoesNotReloadByDefault(): void
        {
            $file = $this->writeIni('comp/language/en-GB/en-GB.com_test.ini', [
                'COM_TEST' => 'Original',
            ]);

            $t = TestableTranslator::make();
            $t->load('com_test', $this->fixtureDir . '/comp');
            $this->assertSame('Original', $t->translate('COM_TEST'));

            // Change file content
            file_put_contents($file, 'COM_TEST="Changed"' . "\n");

            // Second load without reload flag — should return cached result
            $t->load('com_test', $this->fixtureDir . '/comp');
            $this->assertSame('Original', $t->translate('COM_TEST'));
        }

        #[Test]
        public function loadWithReloadFlagReloads(): void
        {
            $file = $this->writeIni('comp/language/en-GB/en-GB.com_test.ini', [
                'COM_TEST' => 'Original',
            ]);

            $t = TestableTranslator::make();
            $t->load('com_test', $this->fixtureDir . '/comp');

            file_put_contents($file, 'COM_TEST="Changed"' . "\n");

            $t->load('com_test', $this->fixtureDir . '/comp', null, true);
            $this->assertSame('Changed', $t->translate('COM_TEST'));
        }

        #[Test]
        public function loadMergesIntoExistingStrings(): void
        {
            $this->writeIni('comp1/language/en-GB/en-GB.com_a.ini', ['KEY_A' => 'A']);
            $this->writeIni('comp2/language/en-GB/en-GB.com_b.ini', ['KEY_B' => 'B']);

            $t = TestableTranslator::make();
            $t->load('com_a', $this->fixtureDir . '/comp1');
            $t->load('com_b', $this->fixtureDir . '/comp2');

            $this->assertSame('A', $t->translate('KEY_A'));
            $this->assertSame('B', $t->translate('KEY_B'));
        }

        #[Test]
        public function laterLoadOverridesEarlierKeys(): void
        {
            $this->writeIni('comp1/language/en-GB/en-GB.com_a.ini', ['SHARED' => 'First']);
            $this->writeIni('comp2/language/en-GB/en-GB.com_b.ini', ['SHARED' => 'Second']);

            $t = TestableTranslator::make();
            $t->load('com_a', $this->fixtureDir . '/comp1');
            $t->load('com_b', $this->fixtureDir . '/comp2');

            $this->assertSame('Second', $t->translate('SHARED'));
        }

        // -----------------------------------------------------------------
        //  parseIniFile()
        // -----------------------------------------------------------------

        #[Test]
        public function parseIniFileHandlesStandardEntries(): void
        {
            $file = $this->writeIni('standard.ini', [
                'KEY1' => 'Simple value',
                'KEY2' => 'Value with (parentheses)',
            ]);

            $result = Translator::parseIniFile($file);

            $this->assertIsArray($result);
            $this->assertSame('Simple value', $result['KEY1']);
            $this->assertSame('Value with (parentheses)', $result['KEY2']);
        }

        #[Test]
        public function parseIniFileHandlesEscapedQuotes(): void
        {
            $file = $this->fixtureDir . '/quotes.ini';
            // Write raw content (not through writeIni which adds its own quotes)
            file_put_contents($file, 'MSG="He said \"hello\""' . "\n");

            $result = Translator::parseIniFile($file);

            $this->assertSame('He said "hello"', $result['MSG']);
        }

        #[Test]
        public function parseIniFileReturnsEmptyForMissingFile(): void
        {
            $result = Translator::parseIniFile('/nonexistent/file.ini');
            $this->assertSame([], $result);
        }

        #[Test]
        public function parseIniFileReturnsEmptyForEmptyFile(): void
        {
            $file = $this->fixtureDir . '/empty.ini';
            file_put_contents($file, '');

            $result = Translator::parseIniFile($file);
            $this->assertSame([], $result);
        }

        // -----------------------------------------------------------------
        //  txt() — sprintf wrapper
        // -----------------------------------------------------------------

        #[Test]
        public function txtWithNoArgsCallsTranslate(): void
        {
            $t = TestableTranslator::make();
            $t->setStrings(['HELLO' => 'Hello']);

            $this->assertSame('Hello', $t->txt('HELLO'));
        }

        #[Test]
        public function txtWithSprintfArgs(): void
        {
            $t = TestableTranslator::make();
            $t->setStrings([
                'GREETING' => 'Hello, %s!',
                'COUNT' => '%d items found',
                'MULTI' => '%s has %d items',
            ]);

            $this->assertSame('Hello, World!', $t->txt('GREETING', 'World'));
            $this->assertSame('5 items found', $t->txt('COUNT', 5));
            $this->assertSame('Blog has 3 items', $t->txt('MULTI', 'Blog', 3));
        }

        #[Test]
        public function txtWithBooleanSecondArgPassesToTranslate(): void
        {
            $t = TestableTranslator::make();
            $t->setStrings(['MSG' => 'He said "hi"']);

            // txt('MSG', true) should pass jsSafe=true to translate
            $result = $t->txt('MSG', true);
            $this->assertStringContainsString('\\', $result);
        }

        // -----------------------------------------------------------------
        //  Getters and setters
        // -----------------------------------------------------------------

        #[Test]
        public function getLanguageReturnsCurrentCode(): void
        {
            $t = TestableTranslator::make('fr-FR');

            $this->assertSame('fr-FR', $t->getLanguage());
        }

        #[Test]
        public function getDefaultReturnsDefaultCode(): void
        {
            $t = TestableTranslator::make();

            $this->assertSame('en-GB', $t->getDefault());
        }

        #[Test]
        public function setDefaultChangesDefault(): void
        {
            $t = TestableTranslator::make();
            $t->setDefault('fr-FR');

            $this->assertSame('fr-FR', $t->getDefault());
        }

        #[Test]
        public function setDebugTogglesDebugMode(): void
        {
            $t = TestableTranslator::make();
            $this->assertFalse($t->getDebug());

            $t->setDebug(true);
            $this->assertTrue($t->getDebug());
        }

        #[Test]
        public function getOrphansTracksInDebugMode(): void
        {
            $t = TestableTranslator::make(debug: true);

            $t->translate('NEVER_DEFINED');

            $orphans = $t->getOrphans();
            $this->assertArrayHasKey('NEVER_DEFINED', $orphans);
        }

        #[Test]
        public function getUsedTracksInDebugMode(): void
        {
            $t = TestableTranslator::make(debug: true);
            $t->setStrings(['HELLO' => 'Hello']);

            $t->translate('HELLO');

            $used = $t->getUsed();
            $this->assertArrayHasKey('HELLO', $used);
        }

        // -----------------------------------------------------------------
        //  getPaths()
        // -----------------------------------------------------------------

        #[Test]
        public function getPathsTracksLoadedFiles(): void
        {
            $this->writeIni('comp/language/en-GB/en-GB.com_test.ini', [
                'COM_TEST' => 'Test',
            ]);

            $t = TestableTranslator::make();
            $t->load('com_test', $this->fixtureDir . '/comp');

            $paths = $t->getPaths('com_test');
            $this->assertIsArray($paths);
            $this->assertNotEmpty($paths);

            // The loaded file should be marked as successful
            $values = array_values($paths);
            $this->assertTrue($values[0]);
        }

        #[Test]
        public function getPathsReturnsNullForUnloadedExtension(): void
        {
            $t = TestableTranslator::make();

            $this->assertNull($t->getPaths('com_never_loaded'));
        }

        // -----------------------------------------------------------------
        //  getLanguagePath() static helper
        // -----------------------------------------------------------------

        #[Test]
        public function getLanguagePathBuildsCorrectPath(): void
        {
            $result = Translator::getLanguagePath('/some/base', 'en-GB');

            $this->assertSame('/some/base' . DS . 'language' . DS . 'en-GB', $result);
        }

        #[Test]
        public function getLanguagePathWithoutLanguage(): void
        {
            $result = Translator::getLanguagePath('/some/base');

            $this->assertSame('/some/base' . DS . 'language', $result);
        }

        // -----------------------------------------------------------------
        //  Transliterate
        // -----------------------------------------------------------------

        #[Test]
        public function transliterateConvertsAccentedChars(): void
        {
            $t = TestableTranslator::make();

            $result = $t->transliterate('café résumé');

            $this->assertSame('cafe resume', $result);
        }

        #[Test]
        public function customTransliteratorCanBeSet(): void
        {
            $t = TestableTranslator::make();
            $t->setTransliterator(function ($str) {
                return strtoupper($str);
            });

            $this->assertSame('HELLO', $t->transliterate('hello'));
            $this->assertNotNull($t->getTransliterator());
        }

        // -----------------------------------------------------------------
        //  exists() static method
        // -----------------------------------------------------------------

        #[Test]
        public function existsReturnsFalseForEmptyLang(): void
        {
            $this->assertFalse(Translator::exists(''));
            $this->assertFalse(Translator::exists(null));
        }

        #[Test]
        public function existsReturnsTrueForExistingDir(): void
        {
            $langDir = $this->fixtureDir . '/language/en-GB';
            mkdir($langDir, 0755, true);

            $this->assertTrue(Translator::exists('en-GB', $this->fixtureDir));
        }

        #[Test]
        public function existsReturnsFalseForMissingDir(): void
        {
            $this->assertFalse(Translator::exists('xx-XX', $this->fixtureDir));
        }

        // -----------------------------------------------------------------
        //  Plural suffixes
        // -----------------------------------------------------------------

        #[Test]
        public function defaultPluralSuffixReturnsCount(): void
        {
            $t = TestableTranslator::make();

            $this->assertSame(['0'], $t->getPluralSuffixes(0));
            $this->assertSame(['5'], $t->getPluralSuffixes(5));
        }

        #[Test]
        public function customPluralSuffixCallback(): void
        {
            $t = TestableTranslator::make();
            $t->setPluralSuffixesCallback(function ($count) {
                return $count == 1 ? ['ONE'] : ['OTHER'];
            });

            $this->assertSame(['ONE'], $t->getPluralSuffixes(1));
            $this->assertSame(['OTHER'], $t->getPluralSuffixes(2));
        }

        // -----------------------------------------------------------------
        //  script() — JavaScript string storage
        // -----------------------------------------------------------------

        #[Test]
        public function scriptStoresTranslatedStrings(): void
        {
            $t = TestableTranslator::make();
            $t->setStrings(['JS_MSG' => 'Hello JS']);

            $result = $t->script('JS_MSG');

            $this->assertArrayHasKey('JS_MSG', $result);
            $this->assertSame('Hello JS', $result['JS_MSG']);
        }

        #[Test]
        public function scriptWithNullReturnsAllStored(): void
        {
            $t = TestableTranslator::make();
            $t->setStrings(['A' => 'AA']);
            $t->script('A');

            $result = $t->script(null);
            $this->assertArrayHasKey('A', $result);
        }
    }
}
