<?php

namespace Potherca\Flysystem\Github;

use Github\Api\ApiInterface;
use Github\Api\GitData\Trees;
use Github\Api\Repository\Commits;
use Github\Api\Repository\Contents;
use Github\Client;
use Github\Exception\RuntimeException;

/**
 * Tests for the Api class
 *
 * @coversDefaultClass \Potherca\Flysystem\Github\Api
 * @covers ::<!public>
 * @covers ::__construct
 */
class ApiTest extends \PHPUnit_Framework_TestCase
{
    ////////////////////////////////// FIXTURES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    const MOCK_FILE_PATH = '/path/to/mock/file';
    const MOCK_FILE_CONTENTS = 'Mock file contents';

    /** @var Api */
    private $api;
    /** @var Client|\PHPUnit_Framework_MockObject_MockObject */
    private $mockClient;
    /** @var Settings|\PHPUnit_Framework_MockObject_MockObject */
    private $mockSettings;

    /**
     *
     */
    protected function setUp()
    {
        $this->mockClient = $this->getMockClient();
        $this->mockSettings = $this->getMockSettings();

        $this->api = new Api($this->mockClient, $this->mockSettings);
    }

    /////////////////////////////////// TESTS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    /**
     * @uses Potherca\Flysystem\Github\Api::exists
     */
    final public function testApiShouldComplainWhenInstantiatedWithoutClient()
    {
        $message = sprintf(
            'Argument %d passed to %s::__construct() must be an instance of %s',
            1,
            Api::class,
            Client::class
        );

        $this->setExpectedException(
            \PHPUnit_Framework_Error::class,
            $message
        );

        /** @noinspection PhpParamsInspection */
        new Api();
    }

    /**
     * @coversNothing
     */
    final public function testApiShouldComplainWhenInstantiatedWithoutSettings()
    {
        $message = sprintf(
            'Argument %d passed to %s::__construct() must implement interface %s',
            2,
            Api::class,
            SettingsInterface::class
        );

        $this->setExpectedException(
            \PHPUnit_Framework_Error::class,
            $message
        );

        /** @noinspection PhpParamsInspection */
        new Api($this->getMockClient());
    }

    /**
     * @covers ::getFileContents
     */
    final public function testApiShouldUseValuesFromSettingsWhenAskingClientForFileContent()
    {
        $api = $this->api;

        $expected = self::MOCK_FILE_CONTENTS;

        $mockVendor = 'vendor';
        $mockPackage = 'package';
        $mockReference = 'reference';

        $this->prepareMockSettings([
            'getVendor' => $mockVendor,
            'getPackage' => $mockPackage,
            'getReference' => $mockReference,
        ]);

        $this->prepareMockApi(
            'download',
            $api::API_REPO,
            [$mockVendor, $mockPackage, self::MOCK_FILE_PATH, $mockReference],
            $expected
        );

        $actual = $api->getFileContents(self::MOCK_FILE_PATH);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers ::exists
     */
    final public function testApiShouldUseValuesFromSettingsWhenAskingClientIfFileExists()
    {
        $api = $this->api;

        $expected = self::MOCK_FILE_CONTENTS;

        $mockVendor = 'vendor';
        $mockPackage = 'package';
        $mockReference = 'reference';

        $this->prepareMockSettings([
            'getVendor' => $mockVendor,
            'getPackage' => $mockPackage,
            'getReference' => $mockReference,
        ]);

        $this->prepareMockApi(
            'exists',
            $api::API_REPO,
            [$mockVendor, $mockPackage, self::MOCK_FILE_PATH, $mockReference],
            $expected
        );

        $actual = $api->exists(self::MOCK_FILE_PATH);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers ::getLastUpdatedTimestamp
     */
    final public function testApiShouldUseValuesFromSettingsWhenAskingClientForLastUpdatedTimestamp()
    {
        $api = $this->api;

        $expected = ['timestamp' => 1420070400];

        $this->prepareFixturesForTimeStamp();

        $actual = $api->getLastUpdatedTimestamp(self::MOCK_FILE_PATH);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers ::getCreatedTimestamp
     */
    final public function testApiShouldUseValuesFromSettingsWhenAskingClientForCreatedTimestamp()
    {
        $api = $this->api;

        $expected = ['timestamp' => 1362268800];

        $this->prepareFixturesForTimeStamp();

        $actual = $api->getCreatedTimestamp(self::MOCK_FILE_PATH);

        $this->assertEquals($expected, $actual);
    }
    /**
     * @covers ::getMetaData
     */
    final public function testApiShouldUseValuesFromSettingsWhenAskingClientForFileInfo()
    {
        $api = $this->api;

        $expected = self::MOCK_FILE_CONTENTS;

        $mockVendor = 'vendor';
        $mockPackage = 'package';
        $mockReference = 'reference';

        $this->prepareMockSettings([
            'getVendor' => $mockVendor,
            'getPackage' => $mockPackage,
            'getReference' => $mockReference,
        ]);

        $this->prepareMockApi(
            'show',
            $api::API_REPO,
            [$mockVendor, $mockPackage, self::MOCK_FILE_PATH, $mockReference],
            $expected
        );

        $actual = $api->getMetaData(self::MOCK_FILE_PATH);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers ::getMetaData
     */
    final public function testApiShouldAccountForFileNotExistingWhenAskingInfoForFile()
    {
        $api = $this->api;

        $expected = false;

        $this->mockClient->expects($this->exactly(1))
        ->method('api')
        ->willThrowException(new RuntimeException(Api::ERROR_NOT_FOUND));

        $actual = $api->getMetaData(self::MOCK_FILE_PATH);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers ::getMetaData
     */
    final public function testApiShouldPassOtherRuntimeExceptionsWhenAskingInfoForFileCausesRuntimeException()
    {
        $api = $this->api;

        $this->setExpectedException(RuntimeException::class, self::MOCK_FILE_CONTENTS);

        $expected = false;

        $this->mockClient->expects($this->exactly(1))
        ->method('api')
        ->willThrowException(new RuntimeException(self::MOCK_FILE_CONTENTS));

        $actual = $api->getMetaData(self::MOCK_FILE_PATH);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers ::getMetaData
     */
    final public function testApiShouldPassOnExceptionsWhenAskingInfoForFileCausesAnException()
    {
        $api = $this->api;

        $this->setExpectedException(\RuntimeException::class, Api::ERROR_NOT_FOUND);

        $expected = false;

        $this->mockClient->expects($this->exactly(1))
        ->method('api')
        ->willThrowException(new \RuntimeException(Api::ERROR_NOT_FOUND));

        $actual = $api->getMetaData(self::MOCK_FILE_PATH);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers ::getRecursiveMetadata
     *
     * @dataProvider provideExpectedMetadata
     *
     * @param string $path
     * @param array $expected
     * @param bool $recursive
     * @param bool $truncated
     */
    final public function testApiShouldRetrieveExpectedMetadataWhenAskedTogetRecursiveMetadata(
        $path,
        $expected,
        $recursive,
        $truncated
    ) {
        $api = $this->api;

        $mockVendor = 'vendor';
        $mockPackage = 'package';
        $mockReference = 'reference';

        $this->prepareMockSettings([
            'getVendor' => $mockVendor,
            'getPackage' => $mockPackage,
            'getReference' => $mockReference,
        ]);

        $this->prepareMockApi(
            'show',
            $api::API_GIT_DATA,
            [$mockVendor, $mockPackage, $mockReference, $recursive],
            $this->getMockApiTreeResponse($truncated, $api),
            Trees::class
        );

        $actual = $api->getRecursiveMetadata($path, $recursive);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers ::guessMimeType
     *
     * @uses League\Flysystem\Util\MimeType
     */
    final public function testApiShouldUseFileExtensionToGuessMimeTypeWhenExtensionIsAvailable()
    {
        $api = $this->api;

        $expected = 'image/png';

        $this->mockClient->expects($this->never())->method('api');

        $actual = $api->guessMimeType(self::MOCK_FILE_PATH.'.png');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers ::guessMimeType
     *
     * @uses League\Flysystem\Util\MimeType
     *
     * @uses Potherca\Flysystem\Github\Api::getFileContents
     */
    final public function testApiShouldUseFileContentsToGuessMimeTypeWhenExtensionUnavailable()
    {
        $api = $this->api;

        $expected = 'image/png';

        $mockVendor = 'vendor';
        $mockPackage = 'package';
        $mockReference = 'reference';

        $this->prepareMockSettings([
            'getVendor' => $mockVendor,
            'getPackage' => $mockPackage,
            'getReference' => $mockReference,
        ]);

        $image = imagecreatetruecolor(1,1);
        ob_start();
        imagepng($image);
        $contents = ob_get_contents();
        ob_end_clean();
        imagedestroy($image);

        $this->prepareMockApi(
            'download',
            $api::API_REPO,
            [$mockVendor, $mockPackage, self::MOCK_FILE_PATH, $mockReference],
            $contents
        );

        $actual = $api->guessMimeType(self::MOCK_FILE_PATH);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @uses Potherca\Flysystem\Github\Api::exists
     */
    final public function testApiShouldUseCredentialsWhenTheyHaveBeenGiven()
    {
        $api = $this->api;

        $mockVendor = 'vendor';
        $mockPackage = 'package';
        $mockReference = 'reference';

        $this->prepareMockSettings([
            'getVendor' => $mockVendor,
            'getPackage' => $mockPackage,
            'getReference' => $mockReference,
            'getCredentials' => ['foo']
        ]);

        $this->prepareMockApi(
            'exists',
            $api::API_REPO,
            [$mockVendor, $mockPackage, self::MOCK_FILE_PATH, $mockReference],
            ''
        );

        $this->mockClient->expects($this->exactly(1))
            ->method('authenticate')
        ;

        $api->exists(self::MOCK_FILE_PATH);
    }

    ////////////////////////////// MOCKS AND STUBS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    /**
     * @return Client|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockClient()
    {
        return $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return Settings|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockSettings()
    {
        return $this->getMockBuilder(SettingsInterface::class)
            ->getMock();
    }

    /**
     * @param string $method
     * @param string $apiName
     * @param array $apiParameters
     * @param mixed $apiOutput
     * @param string $repositoryClass
     */
    private function prepareMockApi($method, $apiName, $apiParameters, $apiOutput, $repositoryClass = Contents::class)
    {

        $parts = explode('\\', $repositoryClass);
        $repositoryName = strtolower(array_pop($parts));

        $mockApi = $this->getMockBuilder(ApiInterface::class)
            ->setMethods([$repositoryName, 'getPerPage', 'setPerPage'])
            ->getMock()
        ;

        $mockRepository = $this->getMockBuilder($repositoryClass)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $mockRepository->expects($this->exactly(1))
            ->method($method)
            ->withAnyParameters()
            ->willReturnCallback(function () use ($apiParameters, $apiOutput) {
                $this->assertEquals($apiParameters, func_get_args());
                return $apiOutput;
            })
        ;

        $mockApi->expects($this->exactly(1))
            ->method($repositoryName)
            ->willReturn($mockRepository)
        ;

        $this->mockClient->expects($this->exactly(1))
            ->method('api')
            ->with($apiName)
            ->willReturn($mockApi)
        ;
    }

    /**
     * @param array $expectations
     */
    private function prepareMockSettings(array $expectations)
    {
        foreach ($expectations as $methodName => $returnValue) {
            $this->mockSettings->expects($this->exactly(1))
                ->method($methodName)
                ->willReturn($returnValue)
            ;
        }
    }

    /**
     * @param $truncated
     * @param $api
     * @return array
     */
    private function getMockApiTreeResponse($truncated, $api)
    {
        return [
            $api::KEY_TREE => [
                [
                    'path' => self::MOCK_FILE_PATH,
                    'mode' => '100644',
                    'type' => 'tree',
                    'size' => 57,
                ],
                [
                    'path' => self::MOCK_FILE_PATH . 'Foo',
                    'basename' => self::MOCK_FILE_PATH . 'Foo',
                    'mode' => '100644',
                    'type' => 'blob',
                    'size' => 57,
                ],
                [
                    'path' => self::MOCK_FILE_PATH . '/Bar',
                    'name' => self::MOCK_FILE_PATH . '/Bar',
                    'mode' => '100644',
                    'type' => 'blob',
                    'size' => 57,
                ],
                [
                    'path' => 'some/other/file',
                    'mode' => '100644',
                    'type' => 'blob',
                    'size' => 747,
                ],
            ],
            'truncated' => $truncated,
        ];
    }

    private function prepareFixturesForTimeStamp()
    {
        date_default_timezone_set('UTC');

        $mockVendor = 'vendor';
        $mockPackage = 'package';
        $mockBranch = 'branch';

        $this->prepareMockSettings([
            'getVendor' => $mockVendor,
            'getPackage' => $mockPackage,
            'getBranch' => $mockBranch,
        ]);

        $apiParameters = [
            $mockVendor,
            $mockPackage,
            [
                'sha' => $mockBranch,
                'path' => self::MOCK_FILE_PATH
            ]

        ];

        $apiOutput = [
            ['commit' => ['committer' => ['date' => '20150101']]],
            ['commit' => ['committer' => ['date' => '20140202']]],
            ['commit' => ['committer' => ['date' => '20130303']]],
        ];

        $this->prepareMockApi(
            'all',
            Api::API_REPO,
            $apiParameters,
            $apiOutput,
            Commits::class
        );
    }

    /////////////////////////////// DATAPROVIDERS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    /**
     * @return array
     */
    final public  function provideExpectedMetadata()
    {
        return [
            'Filepath, not recursive, not truncated' => [
                self::MOCK_FILE_PATH,
                [
                    [
                        'path' => '/path/to/mock/file',
                        'mode' => 100644,
                        'type' => 'dir',
                        'size' => 57,
                        'name' => '/path/to/mock/file',
                        'contents' => null,
                        'stream' => null,
                        'timestamp' => null,
                        'visibility' => 'public'
                    ],
                    [
                        'path' => '/path/to/mock/fileFoo',
                        'basename' => '/path/to/mock/fileFoo',
                        'mode' => 100644,
                        'type' => 'file',
                        'size' => 57,
                        'name' => '/path/to/mock/fileFoo',
                        'contents' => null,
                        'stream' => null,
                        'timestamp' => null,
                        'visibility' => 'public'
                    ]
                ],
                false,
                false
            ],
            'Filepath, recursive, not truncated' => [
                self::MOCK_FILE_PATH,
                [
                    [
                        'path' => '/path/to/mock/file',
                        'mode' => 100644,
                        'type' => 'dir',
                        'size' => 57,
                        'name' => '/path/to/mock/file',
                        'contents' => null,
                        'stream' => null,
                        'timestamp' => null,
                        'visibility' => 'public'
                    ],
                    [
                        'path' => '/path/to/mock/fileFoo',
                        'basename' => '/path/to/mock/fileFoo',
                        'mode' => 100644,
                        'type' => 'file',
                        'size' => 57,
                        'name' => '/path/to/mock/fileFoo',
                        'contents' => null,
                        'stream' => null,
                        'timestamp' => null,
                        'visibility' => 'public'
                    ],
                    [
                        'path' => '/path/to/mock/file/Bar',
                        'mode' => 100644,
                        'type' => 'file',
                        'size' => 57,
                        'name' => '/path/to/mock/file/Bar',
                        'contents' => null,
                        'stream' => null,
                        'timestamp' => null,
                        'visibility' => 'public'
                    ]
                ],
                true,
                false
            ],
            'Filepath, not recursive, truncated' => [
                self::MOCK_FILE_PATH,
                [
                    [
                        'path' => '/path/to/mock/file',
                        'mode' => 100644,
                        'type' => 'dir',
                        'size' => 57,
                        'name' => '/path/to/mock/file',
                        'contents' => null,
                        'stream' => null,
                        'timestamp' => null,
                        'visibility' => 'public'
                    ],
                    [
                        'path' => '/path/to/mock/fileFoo',
                        'basename' => '/path/to/mock/fileFoo',
                        'mode' => 100644,
                        'type' => 'file',
                        'size' => 57,
                        'name' => '/path/to/mock/fileFoo',
                        'contents' => null,
                        'stream' => null,
                        'timestamp' => null,
                        'visibility' => 'public'
                    ]
                ],
                false,
                true
            ],
            'No Filepath, recursive, not truncated' => [
                '',
                [
                    [
                        'path' => '/path/to/mock/file',
                        'mode' => 100644,
                        'type' => 'dir',
                        'size' => 57,
                        'name' => '/path/to/mock/file',
                        'contents' => null,
                        'stream' => null,
                        'timestamp' => null,
                        'visibility' => 'public'
                    ],
                    [
                        'path' => '/path/to/mock/fileFoo',
                        'basename' => '/path/to/mock/fileFoo',
                        'mode' => 100644,
                        'type' => 'file',
                        'size' => 57,
                        'name' => '/path/to/mock/fileFoo',
                        'contents' => null,
                        'stream' => null,
                        'timestamp' => null,
                        'visibility' => 'public'
                    ],
                    [
                        'path' => '/path/to/mock/file/Bar',
                        'mode' => 100644,
                        'type' => 'file',
                        'size' => 57,
                        'name' => '/path/to/mock/file/Bar',
                        'contents' => null,
                        'stream' => null,
                        'timestamp' => null,
                        'visibility' => 'public'
                    ],
                    [
                        'path' => 'some/other/file',
                        'mode' => 100644,
                        'type' => 'file',
                        'size' => 747,
                        'name' => 'some/other/file',
                        'contents' => null,
                        'stream' => null,
                        'timestamp' => null,
                        'visibility' => 'public'
                    ]
                ],
                true,
                false
            ],
            'No Filepath, recursive, truncated' => [
                '',
                [
                    [
                        'path' => '/path/to/mock/file',
                        'mode' => 100644,
                        'type' => 'dir',
                        'size' => 57,
                        'name' => '/path/to/mock/file',
                        'contents' => null,
                        'stream' => null,
                        'timestamp' => null,
                        'visibility' => 'public'
                    ],
                    [
                        'path' => '/path/to/mock/fileFoo',
                        'basename' => '/path/to/mock/fileFoo',
                        'mode' => 100644,
                        'type' => 'file',
                        'size' => 57,
                        'name' => '/path/to/mock/fileFoo',
                        'contents' => null,
                        'stream' => null,
                        'timestamp' => null,
                        'visibility' => 'public'
                    ],
                    [
                        'path' => '/path/to/mock/file/Bar',
                        'mode' => 100644,
                        'type' => 'file',
                        'size' => 57,
                        'name' => '/path/to/mock/file/Bar',
                        'contents' => null,
                        'stream' => null,
                        'timestamp' => null,
                        'visibility' => 'public'
                    ],
                    [
                        'path' => 'some/other/file',
                        'mode' => 100644,
                        'type' => 'file',
                        'size' => 747,
                        'name' => 'some/other/file',
                        'contents' => null,
                        'stream' => null,
                        'timestamp' => null,
                        'visibility' => 'public'
                    ]
                ],
                true,
                true
            ],
            'No Filepath, not recursive, truncated' => [
                '',
                [
                    [
                        'name' => null,
                        'visibility' => null,
                        'contents' => null,
                        'stream' => null,
                        'timestamp' => null
                    ]
                ],
                false,
                true
            ],
            'No Filepath, not recursive, not truncated' => [
                '',
                [
                    [
                        'name' => null,
                        'visibility' => null,
                        'contents' => null,
                        'stream' => null,
                        'timestamp' => null,
                    ]
                ],
                false,
                false
            ],
        ];
    }
}
