<?php

namespace Potherca\Flysystem\Github;

/**
 * Tests for the Settings class
 *
 * @coversDefaultClass \Potherca\Flysystem\Github\Settings
 * @covers ::<!public>
 * @covers ::__construct
 */
class SettingsTest extends \PHPUnit_Framework_TestCase
{
    ////////////////////////////////// FIXTURES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    const MOCK_VENDOR_NAME = 'mock_vendor';
    const MOCK_PACKAGE_NAME = 'mock_package';
    const MOCK_BRANCH = 'mock_branch';
    const MOCK_REFERENCE = 'mock_reference';

    /** @var Settings */
    private $settings;

    /**
     *
     */
    final protected function setUp()
    {
        $this->settings = new Settings(
            $this->getMockRespositoryName(),
            $this->getMockCredentials(),
            self::MOCK_BRANCH,
            self::MOCK_REFERENCE
        );
    }

    /////////////////////////////////// TESTS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    /**
     * @covers ::__construct
     */
    final public function testSettingsShouldComplainWhenInstantiatedWithoutRepositoryName()
    {
        $this->setExpectedException(
            \PHPUnit_Framework_Error_Warning::class,
            sprintf('Missing argument %d for %s::__construct()', 1, Settings::class)
        );

        /** @noinspection PhpParamsInspection */
        new Settings();
    }

    /**
     * @covers ::getRepository
     */
    final public function testSettingsShouldContainRepositoryItWasGivenGivenWhenInstantiated()
    {
        $settings = $this->settings;

        $expected = $this->getMockRespositoryName();

        $actual = $settings->getRepository();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers ::__construct
     *
     * @dataProvider provideInvalidRepositoryNames
     *
     * @param string $name
     */
    final public function testSettingsShouldComplainWhenGivenInvalidRepositoryNames($name)
    {
        $this->setExpectedException(
            \InvalidArgumentException::class,
            sprintf(Settings::ERROR_INVALID_REPOSITORY_NAME, var_export($name, true))
        );
        new Settings($name);
    }

    /**
     * @covers ::getRepository
     */
    final public function testSettingsShouldOnlyNeedRepositoryNameWhenInstantiated()
    {
        $settings = new Settings($this->getMockRespositoryName());
        $this->assertInstanceOf(Settings::class, $settings);
    }

    /**
     * @covers ::getVendor
     */
    final public function testSettingsShouldContainVendorNameFromGivenRepositoryWhenInstantiated()
    {
        $settings = new Settings($this->getMockRespositoryName());

        $expected = self::MOCK_VENDOR_NAME;

        $actual = $settings->getVendor();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers ::getPackage
     */
    final public function testSettingsShouldContainPackageNameFromGivenRepositoryWhenInstantiated()
    {
        $settings = new Settings($this->getMockRespositoryName());

        $expected = self::MOCK_PACKAGE_NAME;

        $actual = $settings->getPackage();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers ::getCredentials
     */
    final public function testSettingsShouldContainEmptyCredentialsWhenInstantiatedWithoutCredentials()
    {
        $settings = new Settings($this->getMockRespositoryName());

        $expected = [];

        $actual = $settings->getCredentials();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers ::getCredentials
     */
    final public function testSettingsShouldContainCredentialsItWasGivenGivenWhenInstantiated()
    {
        $settings = $this->settings;

        $expected = $this->getMockCredentials();

        $actual = $settings->getCredentials();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers ::getBranch
     */
    final public function testSettingsShouldContainMasterAsBranchWhenInstantiatedWithoutBranch()
    {
        $settings = new Settings($this->getMockRespositoryName());

        $expected = 'master';

        $actual = $settings->getBranch();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers ::getBranch
     */
    final public function testSettingsShouldContainBranchItWasGivenGivenWhenInstantiated()
    {
        $settings = $this->settings;

        $expected = self::MOCK_BRANCH;

        $actual = $settings->getBranch();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers ::getReference
     */
    final public function testSettingsShouldContainHeadAsReferenceWhenInstantiatedWithoutReference()
    {
        $settings = new Settings($this->getMockRespositoryName());

        $expected = 'HEAD';

        $actual = $settings->getReference();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers ::getReference
     */
    final public function testSettingsShouldContaingetReferenceItWasGivenGivenWhenInstantiated()
    {
        $settings = $this->settings;

        $expected = self::MOCK_REFERENCE;

        $actual = $settings->getReference();

        $this->assertEquals($expected, $actual);
    }

    ////////////////////////////// MOCKS AND STUBS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    /**
     * @return string
     */
    private function getMockRespositoryName()
    {
        return self::MOCK_VENDOR_NAME . '/' . self::MOCK_PACKAGE_NAME;
    }

    /**
     * @return array
     */
    private function getMockCredentials()
    {
        return ['mock_type', 'mock_user', 'mock_password'];
    }

    /////////////////////////////// DATAPROVIDERS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    final public function provideInvalidRepositoryNames()
    {
        return [
            [''],
            [null],
            [true],
            [array()],
            ['foo'],
            ['/foo'],
            ['foo/'],
            ['foo//bar'],
            ['foo/bar/'],
            ['/foo/bar/'],
            ['foo/bar/baz'],
            ['/foo/bar/baz/'],
            ['foo/bar/baz/'],
            ['/foo/bar/baz'],
        ];
    }
}

/*EOF*/
