<?php


namespace Potherca\Flysystem\Github;

/**
 * Tests for the  GithubAdapter class
 *
 * @coversDefaultClass \Potherca\Flysystem\Github\GithubAdapter
 * @covers ::<!public>
 * @covers ::__construct
 * @covers ::getApi
 */
class GithubAdapterTest extends \PHPUnit_Framework_TestCase
{
    ////////////////////////////////// FIXTURES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    const MOCK_FILE_PATH = '/path/to/mock/file';

    /** @var GithubAdapter  */
    private $adapter;
    /** @var ApiInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $mockClient;

    /**
     *
     */
    protected function setup()
    {
        $this->mockClient = $this->getMock(ApiInterface::class);
        $this->adapter = new GithubAdapter($this->mockClient);
    }

    /////////////////////////////////// TESTS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    /**
     * @covers ::has
     * @covers ::read
     * @covers ::listContents
     * @covers ::getMetadata
     * @covers ::getSize
     * @covers ::getMimetype
     * @covers ::getTimestamp
     * @covers ::getVisibility
     *
     * @dataProvider provideReadMethods
     *
     * @param $method
     * @param $apiMethod
     * @param $parameters
     */
    final public function testAdapterShouldPassParameterToClient($method, $apiMethod, $parameters)
    {
        $mocker = $this->mockClient->expects($this->exactly(1))
            ->method($apiMethod);

        $mocker->getMatcher()->parametersMatcher = new \PHPUnit_Framework_MockObject_Matcher_Parameters($parameters);

        call_user_func_array([$this->adapter, $method], $parameters);
    }

    ////////////////////////////// MOCKS AND STUBS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    /////////////////////////////// DATAPROVIDERS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    final public function provideReadMethods()
    {
        return [
            ['has', 'exists', [self::MOCK_FILE_PATH]],
            ['read', 'getFileContents', [self::MOCK_FILE_PATH]],
            ['listContents', 'getRecursiveMetadata', [self::MOCK_FILE_PATH, true]],
            ['getMetadata', 'getMetadata', [self::MOCK_FILE_PATH]],
            ['getSize', 'getMetadata', [self::MOCK_FILE_PATH]],
            ['getMimetype', 'guessMimeType', [self::MOCK_FILE_PATH]],
            ['getTimestamp', 'getLastUpdatedTimestamp', [self::MOCK_FILE_PATH]],
            ['getVisibility', 'getRecursiveMetadata', [self::MOCK_FILE_PATH]],
        ];
    }
}
