<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Tests\Traits;

use Hubzero\Utility\Arr;

// phpcs:ignore Squiz.Classes.ValidClassName.NotCamelCaps
trait canMock
{
    public $mock;

    public function mock($args = [])
    {
        $this->mock = $this->getMockBuilder($args['class']);

        $this->_setMethods($args);
        $this->_setProps($args);

        return $this->mock;
    }

    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _setProps($args)
    {
        $props = $this->_extractMockInstantiationData($args, 'props');

        $this->_setPropNamesAndValues($props);
    }

    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _setPropNamesAndValues($props)
    {
        foreach ($props as $name => $value) {
            $this->mock->$name = $value;
        }
    }

    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _setMethods($args)
    {
        $methods = $this->_extractMockInstantiationData($args, 'methods');

        $this->_setMethodNames($methods);
        $this->_setMethodReturnValues($methods);
    }

    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _extractMockInstantiationData($args, $key)
    {
        $instantiationData = Arr::getValue($args, $key, []);

        $instantiationData = $this->_mapInstantiationData($instantiationData);

        return $instantiationData;
    }

    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _mapInstantiationData($instantiationData)
    {
        $mappedInstantiationData = [];

        foreach ($instantiationData as $name => $value) {
            $this->_mapNameAndValue($mappedInstantiationData, $name, $value);
        }

        return $mappedInstantiationData;
    }

    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _mapNameAndValue(&$mappedInstantiationData, $name, $value)
    {
        if (!is_string($name)) {
            $mappedInstantiationData[$value] = null;
        } else {
            $mappedInstantiationData[$name] = $value;
        }
    }

    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _setMethodNames($methods)
    {
        $methodNames = array_keys($methods);

        $this->mock->setMethods($methodNames);

        $this->mock = $this->mock->getMock();
    }

    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _setMethodReturnValues($methods)
    {
        foreach ($methods as $name => $returnValue) {
            $this->mock->method($name)->willReturn($returnValue);
        }
    }
}
