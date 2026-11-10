<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component\Tests {

    use Hubzero\Component\AbstractComponent;
    use Hubzero\Inertia\InertiaService;
    use PHPUnit\Framework\Attributes\PreserveGlobalState;
    use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
    use PHPUnit\Framework\Attributes\Test;
    use PHPUnit\Framework\TestCase;

    if (!class_exists('\\App', false)) {
        class_alias(AppStub::class, 'App');
    }

    #[RunTestsInSeparateProcesses]
    #[PreserveGlobalState(false)]
    class InertiaComponentBootstrapTest extends TestCase
    {
        protected function setUp(): void
        {
            if (!class_exists(AbstractComponent::class)) {
                $this->markTestSkipped(
                    'Requires Hubzero\\Component\\AbstractComponent (class-based component refactor); not present on '
                        . '2.4-main.',
                );
            }
            if (is_a('\\App', AppStub::class, true)) {
                AppStub::reset();
            }
        }

        #[Test]
        public function bootRegistersAndConfiguresInertiaForEnabledComponent(): void
        {
            $container = new ContainerStub();
            \App::set('app', $container);

            $component = new EnabledInertiaComponent();
            $component->boot();

            $this->assertTrue($component->registered);
            $this->assertTrue($container->has('inertia'));

            $service = $container->get('inertia');
            $this->assertInstanceOf(InertiaService::class, $service);
            $this->assertSame('enabled', $service->getShared('marker'));
        }

        #[Test]
        public function bootSkipsInertiaSetupForDisabledComponent(): void
        {
            $container = new ContainerStub();
            \App::set('app', $container);

            $component = new DisabledInertiaComponent();
            $component->boot();

            $this->assertFalse($component->registered);
            $this->assertFalse($container->has('inertia'));
        }
    }
}
