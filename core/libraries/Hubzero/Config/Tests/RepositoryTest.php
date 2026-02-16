<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Config\Tests;

use PHPUnit\Framework\TestCase;
use Hubzero\Config\Repository;
use Hubzero\Config\FileLoader;
use stdClass;

/**
 * Repository tests
 */
class RepositoryTest extends TestCase
{
    /**
     * Tests the constructor sets loader and client
     *
     * @return  void
     **/
    public function testConstructor()
    {
        $basePath = __DIR__ . '/Files';
        $loader = new FileLoader($basePath, $basePath);
        $data = new Repository('site', $loader);

        // Test set and get Client
        $this->assertEquals($data->getClient(), 'site');

        $data->setClient('api');

        $this->assertEquals($data->getClient(), 'api');

        // Test that a loader was set
        $this->assertInstanceOf('Hubzero\Config\FileLoader', $data->getLoader());
        $this->assertEquals($basePath . DIRECTORY_SEPARATOR . 'config', $data->getLoader()->getConfigPath());

        // Test setting a different loader
        $basePath2 = __DIR__ . '/Files';
        $loader2 = new FileLoader($basePath2, $basePath2);

        // Set by method
        $data->setLoader($loader2);

        $this->assertInstanceOf('Hubzero\Config\FileLoader', $data->getLoader());
        $this->assertEquals($basePath2 . DIRECTORY_SEPARATOR . 'config', $data->getLoader()->getConfigPath());

        // Set by constructor
        $data = new Repository('files', $loader2);

        $this->assertInstanceOf('Hubzero\Config\FileLoader', $data->getLoader());
        $this->assertEquals($basePath2 . DIRECTORY_SEPARATOR . 'config', $data->getLoader()->getConfigPath());
    }

    /**
     * Tests get()
     *
     * @return  void
     **/
    public function testSetAndGet()
    {
        $basePath = __DIR__ . '/Files';
        $loader = new FileLoader($basePath, $basePath);

        $data = new Repository('site', $loader);

        // Test that default value is returned
        $this->assertEquals($data->get('foo'), null);
        $this->assertEquals($data->get('foo', 'one'), 'one');
        $this->assertEquals($data->get('lorem.ipsum.dolor', 'baz'), 'baz');
        $this->assertEquals($data->get('app.application_env'), 'development');
        $this->assertEquals($data->get('application_env'), 'development');

        $loader = new FileLoader($basePath, $basePath);

        $data = new Repository('api', $loader);
        $this->assertEquals($data->get('app.application_env'), 'production');

        // Test correct value is returned
        $data->set('foo', 'bar');

        $this->assertEquals($data->get('foo'), 'bar');

        $data->set('lorem', new stdClass());
        $data->set('lorem.ipsum', 'sham');

        $this->assertEquals($data->get('lorem.ipsum'), 'sham');

        $data['foo'] = 'lorem';

        $this->assertEquals($data->get('', 'lorem'), 'lorem');
        $this->assertEquals($data->get('foo'), 'lorem');
        $this->assertEquals($data['foo'], 'lorem');
        $this->assertEquals($data->get('fake.path', 'lorem'), 'lorem');

        $data['lorem.ipsum'] = 'ipsum';

        $this->assertEquals($data->get('lorem.ipsum'), 'ipsum');
        $this->assertEquals($data['lorem.ipsum'], 'ipsum');
        $this->assertEquals($data->get('lorem.dolor', 'mit'), 'mit');

        $data['lorem'] = array('ipsum' => 'dolor');

        $this->assertEquals($data->get('ipsum'), 'dolor');

        $data->set('lorem.ipsum', array('dolor' => 'mit'));

        $this->assertEquals($data->get('lorem.ipsum.dolor'), 'mit');

        $data->set('lorem', array('ipsum' => 'dolor'));
        $data->set('lorem.dolor.foo', 'bar');

        $this->assertEquals($data->get('lorem.dolor.foo'), 'bar');
    }
}
