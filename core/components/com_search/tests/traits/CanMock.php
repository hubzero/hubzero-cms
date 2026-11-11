<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Tests\Traits;

use Hubzero\Utility\Arr;
use Hubzero\Facades\Component;

trait CanMock
{
    public $mock;

    /**
     * Class being mocked, kept so method names can be checked against it
     *
     * @var  string
     */
    protected $mockClass;

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
     * @var  array
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
     * @return  string
     */
    protected function resolveClass($name)
    {
        // The map comes first: App, Lang and User are real facade aliases, but
        // their methods are reached through __callStatic and cannot be stubbed
        if (!isset(static::$mockClassMap[$name])) {
            return $name;
        }

        list($class, $file) = static::$mockClassMap[$name];

        if ($file && !class_exists($class, false)) {
            require_once Component::path('com_search') . '/' . $file;
        }

        return $class;
    }

    protected function setProps($args)
    {
        $props = $this->extractMockInstantiationData($args, 'props');

        $this->setPropNamesAndValues($props);
    }

    protected function setPropNamesAndValues($props)
    {
        foreach ($props as $name => $value) {
            $this->mock->$name = $value;
        }
    }

    protected function setMethods($args)
    {
        $methods = $this->extractMockInstantiationData($args, 'methods');

        $this->setMethodNames($methods);
        $this->setMethodReturnValues($methods);
    }

    protected function extractMockInstantiationData($args, $key)
    {
        $instantiationData = Arr::getValue($args, $key, []);

        $instantiationData = $this->mapInstantiationData($instantiationData);

        return $instantiationData;
    }

    protected function mapInstantiationData($instantiationData)
    {
        $mappedInstantiationData = [];

        foreach ($instantiationData as $name => $value) {
            $this->mapNameAndValue($mappedInstantiationData, $name, $value);
        }

        return $mappedInstantiationData;
    }

    protected function mapNameAndValue(&$mappedInstantiationData, $name, $value)
    {
        if (!is_string($name)) {
            $mappedInstantiationData[$value] = null;
        } else {
            $mappedInstantiationData[$name] = $value;
        }
    }

    protected function setMethodNames($methods)
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

    protected function setMethodReturnValues($methods)
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
