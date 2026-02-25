<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses -- Test file with helper stubs
namespace Hubzero\Tests {

    use Hubzero\Html\Builder\Behavior;
    use Hubzero\Tests\Concerns\InteractsWithBehaviorAssets;
    use PHPUnit\Framework\Attributes\PreserveGlobalState;
    use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
    use PHPUnit\Framework\Attributes\Test;
    use PHPUnit\Framework\TestCase;

    class BehaviorAssetConfigStub
    {
        public function get(string $key, $default = null)
        {
            if ($key === 'debug') {
                return false;
            }

            return $default;
        }
    }

    class BehaviorAssetFilesystemStub
    {
        public function extension(string $file): string
        {
            return pathinfo($file, PATHINFO_EXTENSION);
        }
    }

    class BehaviorAssetRequestStub
    {
        public function root(bool $pathonly = false): string
        {
            return '';
        }

        public function base(bool $pathonly = false): string
        {
            return '';
        }
    }

    class BehaviorAssetTemplateStub
    {
        public string $template = 'myhub';
    }

    class BehaviorAssetDocumentStub
    {
        public array $scripts = [];
        public array $stylesheets = [];

        public function addScript(
            string $url,
            string $type = 'text/javascript',
            bool $defer = false,
            bool $async = false
        ): void {
            $this->scripts[] = $url;
        }

        public function addStylesheet(
            string $url,
            string $type = 'text/css',
            ?string $media = null,
            array $attribs = []
        ): void {
            $this->stylesheets[] = array(
                'url' => $url,
                'attribs' => $attribs
            );
        }

        public function instance(): object
        {
            return new \stdClass();
        }
    }

    class BehaviorAssetAppStub
    {
        private static array $state = [];

        public static function reset(): void
        {
            self::$state = [];
        }

        public static function has(string $key): bool
        {
            return array_key_exists($key, self::$state);
        }

        public static function get(string $key)
        {
            return self::$state[$key] ?? null;
        }

        public static function set(string $key, $value): void
        {
            self::$state[$key] = $value;
        }

        public static function isAdmin(): bool
        {
            return false;
        }
    }

    if (!class_exists('\\App', false)) {
        class_alias(BehaviorAssetAppStub::class, 'App');
    }

    #[RunTestsInSeparateProcesses]
    #[PreserveGlobalState(false)]
    class BehaviorAssetRegistrationTest extends TestCase
    {
        use InteractsWithBehaviorAssets;

        protected function setUp(): void
        {
            if (is_a('\\App', BehaviorAssetAppStub::class, true)) {
                BehaviorAssetAppStub::reset();
            }

            \Hubzero\Facades\App::set('request', new BehaviorAssetRequestStub());
            \Hubzero\Facades\App::set('config', new BehaviorAssetConfigStub());
            \Hubzero\Facades\App::set('filesystem', new BehaviorAssetFilesystemStub());
            \Hubzero\Facades\App::set('template', new BehaviorAssetTemplateStub());
            \Hubzero\Facades\App::set('document', new BehaviorAssetDocumentStub());

            $ref = new \ReflectionProperty(Behavior::class, 'loaded');
            $ref->setAccessible(true);
            $ref->setValue(null, array());
        }

        #[Test]
        public function inertiaBehaviorRegistersSharedTimelinePanelCssAndAlpineAssets(): void
        {
            Behavior::inertia();

            $document = \Hubzero\Facades\App::get('document');
            $this->assertScriptRegistered($document, '/core/assets/js/alpine/3.14.8/cdn.min.js');
            $this->assertScriptRegistered($document, '/core/assets/js/hubzero-debug-timeline.js');
            $this->assertScriptRegistered($document, '/core/assets/js/hubzero-debug-panel.js');
            $this->assertStylesheetRegistered($document, '/core/assets/css/hubzero-debug-panel.css');
        }

        #[Test]
        public function htmxAlpineBehaviorRegistersCoreProtocolAndDebugAssets(): void
        {
            Behavior::htmxalpine();

            $document = \Hubzero\Facades\App::get('document');
            $this->assertScriptRegistered($document, '/core/assets/js/htmx/2.0.4/htmx.min.js');
            $this->assertScriptRegistered($document, '/core/assets/js/alpine/3.14.8/cdn.min.js');
            $this->assertScriptRegistered($document, '/core/assets/js/htmx/hubzero-bootstrap.js');
            $this->assertScriptRegistered($document, '/core/assets/js/hubzero-debug-timeline.js');
            $this->assertScriptRegistered($document, '/core/assets/js/hubzero-debug-panel.js');
            $this->assertStylesheetRegistered($document, '/core/assets/css/hubzero-debug-panel.css');
        }
    }
}
