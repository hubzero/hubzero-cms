<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Tests {

    use Hubzero\Html\Builder\Behavior;
    use Hubzero\Tests\Concerns\InteractsWithBehaviorAssets;
    use PHPUnit\Framework\Attributes\PreserveGlobalState;
    use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
    use PHPUnit\Framework\Attributes\Test;
    use PHPUnit\Framework\TestCase;

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

            \App::set('request', new BehaviorAssetRequestStub());
            \App::set('config', new BehaviorAssetConfigStub());
            \App::set('filesystem', new BehaviorAssetFilesystemStub());
            \App::set('template', new BehaviorAssetTemplateStub());
            \App::set('document', new BehaviorAssetDocumentStub());

            $ref = new \ReflectionProperty(Behavior::class, 'loaded');
            $ref->setAccessible(true);
            $ref->setValue(null, []);
        }

        #[Test]
        public function inertiaBehaviorRegistersSharedTimelinePanelCssAndAlpineAssets(): void
        {
            Behavior::inertia();

            $document = \App::get('document');
            $this->assertScriptRegistered($document, '/core/assets/js/alpine/3.14.8/cdn.min.js');
            $this->assertScriptRegistered($document, '/core/assets/js/hubzero-debug-timeline.js');
            $this->assertScriptRegistered($document, '/core/assets/js/hubzero-debug-panel.js');
            $this->assertStylesheetRegistered($document, '/core/assets/css/hubzero-debug-panel.css');
        }

        #[Test]
        public function htmxAlpineBehaviorRegistersCoreProtocolAndDebugAssets(): void
        {
            Behavior::htmxalpine();

            $document = \App::get('document');
            $this->assertScriptRegistered($document, '/core/assets/js/htmx/2.0.4/htmx.min.js');
            $this->assertScriptRegistered($document, '/core/assets/js/alpine/3.14.8/cdn.min.js');
            $this->assertScriptRegistered($document, '/core/assets/js/htmx/hubzero-bootstrap.js');
            $this->assertScriptRegistered($document, '/core/assets/js/hubzero-debug-timeline.js');
            $this->assertScriptRegistered($document, '/core/assets/js/hubzero-debug-panel.js');
            $this->assertStylesheetRegistered($document, '/core/assets/css/hubzero-debug-panel.css');
        }
    }
}
