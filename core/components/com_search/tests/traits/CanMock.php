<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Tests\Traits;

use Hubzero\Utility\Arr;
use Hubzero\Facades\Component;

/**
 * @phpstan-require-extends \PHPUnit\Framework\TestCase
 */
trait CanMock
{
    /**
     * @var  mixed
     */
    public $mock;

    /**
     * Class being mocked, kept so method names can be checked against it
     *
     * @var  class-string
     */
    protected $mockClass;

    /**
     * @param   array<string, mixed>  $args
     * @return  mixed
     */
    public function mock($args = [])
    {
        $this->mockClass = $this->resolveClass($args['class']);
        $this->mock      = $this->getMockBuilder($this->mockClass);

        $this->setMethods($args);
        $this->setProps($args);

        return $this->mock;
    }

    /**
     * Short mock names to the classes they actually refer to
     *
     * Each entry is [fully qualified class, file to require relative to the
     * component root, or null when the class is autoloaded].
     *
     * @var  array<string, array{0: string, 1: string|null}>
     */
    protected static $mockClassMap = [
        'App'                => ['Components\\Search\\Tests\\Doubles\\AppDouble', 'tests/doubles/AppDouble.php'],
        'Lang'               => ['Components\\Search\\Tests\\Doubles\\LangDouble', 'tests/doubles/LangDouble.php'],
        'User'               => ['Components\\Search\\Tests\\Doubles\\UserDouble', 'tests/doubles/UserDouble.php'],
        'Notify'             => ['Components\\Search\\Tests\\Doubles\\NotifyDouble', 'tests/doubles/NotifyDouble.php'],
        'Rows'               => ['Hubzero\\Database\\Rows', null],
        'Relational'         => ['Hubzero\\Database\\Relational', null],
        'Boost'              => ['Components\\Search\\Tests\\Doubles\\BoostDouble', 'tests/doubles/BoostDouble.php'],
        'Map'                => [
            'Components\\Search\\Helpers\\BoostDocumentTypeMap',
            'helpers/boostDocumentTypeMap.php',
        ],
        'ErrorMessageHelper' => ['Components\\Search\\Helpers\\ErrorMessageHelper', 'helpers/errorMessageHelper.php'],
        'SolariumBoostQuery' => [
            'Components\\Search\\Tests\\Doubles\\SolariumBoostQueryDouble',
            'tests/doubles/SolariumBoostQueryDouble.php',
        ],
        'BoostQueries'       => [
            'Components\\Search\\Tests\\Doubles\\SolariumBoostQueriesDouble',
            'tests/doubles/SolariumBoostQueriesDouble.php',
        ],
        'Controller'         => [
            'Components\\Search\\Tests\\Doubles\\ControllerDouble',
            'tests/doubles/ControllerDouble.php',
        ],
        'Type'               => ['Components\\Search\\Tests\\Doubles\\TypeDouble', 'tests/doubles/TypeDouble.php'],
    ];

    /**
     * Turn the short name a test passes into a loadable class
     *
     * These tests were written against a PHPUnit that would generate a class
     * for any unknown name. Since PHPUnit 10 the type has to exist, so map the
     * short names onto the real classes and load their files where the
     * component is not autoloaded.
     *
     * @param   string  $name
     * @return  class-string
     */
    protected function resolveClass($name)
    {
        $class = $name;

        // The map comes first: App, Lang and User are real facade aliases, but
        // their methods are reached through __callStatic and cannot be stubbed
        if (isset(static::$mockClassMap[$name])) {
            list($class, $file) = static::$mockClassMap[$name];

            if ($file && !class_exists($class, false)) {
                require_once Component::path('com_search') . '/' . $file;
            }
        }

        if (!class_exists($class) && !interface_exists($class)) {
            throw new \InvalidArgumentException('No class or interface to mock: ' . $class);
        }

        return $class;
    }

    /**
     * @param   array<string, mixed>  $args
     */
    protected function setProps($args): void
    {
        $props = $this->extractMockInstantiationData($args, 'props');

        $this->setPropNamesAndValues($props);
    }

    /**
     * @param   array<string, mixed>  $props
     */
    protected function setPropNamesAndValues($props): void
    {
        foreach ($props as $name => $value) {
            $this->mock->$name = $value;
        }
    }

    /**
     * @param   array<string, mixed>  $args
     */
    protected function setMethods($args): void
    {
        $methods = $this->extractMockInstantiationData($args, 'methods');

        $this->setMethodNames($methods);
        $this->setMethodReturnValues($methods);
    }

    /**
     * @param   array<string, mixed>  $args
     * @param   string  $key
     * @return  array<mixed>
     */
    protected function extractMockInstantiationData($args, $key)
    {
        $instantiationData = Arr::getValue($args, $key, []);

        return $this->mapInstantiationData($instantiationData);
    }

    /**
     * @param   array<int|string, mixed>  $instantiationData
     * @return  array<mixed>
     */
    protected function mapInstantiationData($instantiationData)
    {
        $mappedInstantiationData = [];

        foreach ($instantiationData as $name => $value) {
            $this->mapNameAndValue($mappedInstantiationData, $name, $value);
        }

        return $mappedInstantiationData;
    }

    /**
     * @param   array<mixed>  $mappedInstantiationData
     * @param   int|string  $name
     * @param   mixed  $value
     */
    protected function mapNameAndValue(&$mappedInstantiationData, $name, $value): void
    {
        if (!is_string($name)) {
            $mappedInstantiationData[$value] = null;
        } else {
            $mappedInstantiationData[$name] = $value;
        }
    }

    /**
     * @param   array<string, mixed>  $methods
     */
    protected function setMethodNames($methods): void
    {
        // Only declared methods can be stubbed: MockBuilder::addMethods(),
        // which invented missing ones, is deprecated and goes in PHPUnit 12.
        // A method a test needs belongs on the double in tests/doubles.
        foreach (array_keys($methods) as $name) {
            if (!method_exists($this->mockClass, $name)) {
                throw new \LogicException(
                    "{$this->mockClass} has no {$name}() to stub; declare it on the double in tests/doubles",
                );
            }
        }

        if (!empty($methods)) {
            $this->mock->onlyMethods(array_keys($methods));
        }

        $this->mock = $this->mock->getMock();
    }

    /**
     * @param   array<string, mixed>  $methods
     */
    protected function setMethodReturnValues($methods): void
    {
        foreach ($methods as $name => $returnValue) {
            // A bare method name carries no return value and only means "stub
            // this out". Configuring willReturn(null) for it would win over any
            // expectation the test sets up afterwards.
            if ($returnValue === null) {
                continue;
            }

            $this->mock->method($name)->willReturn($returnValue);
        }
    }
}
