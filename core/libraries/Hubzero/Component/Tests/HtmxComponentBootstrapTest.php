<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component\Tests {

    use Hubzero\Component\AbstractComponent;
    use Hubzero\Htmx\HtmxService;
    use PHPUnit\Framework\Attributes\PreserveGlobalState;
    use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
    use PHPUnit\Framework\Attributes\Test;
    use PHPUnit\Framework\TestCase;

    if (!class_exists('\\App', false)) {
        class_alias(HtmxAppStub::class, 'App');
    }

    #[RunTestsInSeparateProcesses]
    #[PreserveGlobalState(false)]
    class HtmxComponentBootstrapTest extends TestCase
    {
        protected function setUp(): void
        {
            if (!class_exists(AbstractComponent::class)) {
                $this->markTestSkipped(
                    'Requires Hubzero\\Component\\AbstractComponent (class-based component refactor); not present on 2.4-main.',
                );
            }
            if (is_a('\\App', HtmxAppStub::class, true)) {
                HtmxAppStub::reset();
            }

            \App::set('response', new HtmxResponseStub());
        }

        #[Test]
        public function bootRegistersHtmxForEnabledComponent(): void
        {
            $container = new HtmxContainerStub();
            \App::set('app', $container);

            $component = new EnabledHtmxComponent();
            $component->boot();

            $this->assertTrue($component->registered);
            $this->assertTrue($container->has('htmx'));

            $service = $container->get('htmx');
            $this->assertInstanceOf(HtmxService::class, $service);
            $this->assertSame('enabled', $service->getState('marker'));
            $this->assertStringContainsString('HX-Request', \App::get('response')->headers->get('Vary'));
        }

        #[Test]
        public function bootSkipsHtmxSetupForDisabledComponent(): void
        {
            $container = new HtmxContainerStub();
            \App::set('app', $container);

            $component = new DisabledHtmxComponent();
            $component->boot();

            $this->assertFalse($component->registered);
            $this->assertFalse($container->has('htmx'));
        }

        #[Test]
        public function traitRegistersDefaultHtmxSecurityState(): void
        {
            $container = new HtmxContainerStub();
            \App::set('app', $container);

            $component = new TraitDefaultHtmxComponent();
            $component->boot();

            $service = $container->get('htmx');
            $htmxState = $service->getState('htmx');

            $this->assertIsArray($htmxState);
            $this->assertFalse($htmxState['security']['allowEval']);
            $this->assertFalse($htmxState['security']['allowScriptTags']);
            $this->assertSame(20, $htmxState['security']['historyCacheSize']);
        }

        #[Test]
        public function traitAllowsOverridingHtmxSecurityState(): void
        {
            $container = new HtmxContainerStub();
            \App::set('app', $container);

            $component = new TraitCustomHtmxComponent();
            $component->boot();

            $service = $container->get('htmx');
            $htmxState = $service->getState('htmx');

            $this->assertIsArray($htmxState);
            $this->assertTrue($htmxState['security']['allowEval']);
            $this->assertTrue($htmxState['security']['allowScriptTags']);
            $this->assertSame(5, $htmxState['security']['historyCacheSize']);
        }
    }
}
