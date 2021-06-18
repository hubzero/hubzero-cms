<?php

namespace Potherca\Flysystem\Github;

use Github\Client;

class Settings implements SettingsInterface
{
    ////////////////////////////// CLASS PROPERTIES \\\\\\\\\\\\\\\\\\\\\\\\\\\\
    const AUTHENTICATE_USING_TOKEN = Client::AUTH_URL_TOKEN;
    const AUTHENTICATE_USING_PASSWORD = Client::AUTH_HTTP_PASSWORD;

    const BRANCH_MASTER = 'master';
    const REFERENCE_HEAD = 'HEAD';

    const ERROR_INVALID_REPOSITORY_NAME = 'Given Repository name "%s" should be in the format of "vendor/project"';

    /** @var string */
    private $branch;
    /** @var array */
    private $credentials;
    /** @var string */
    private $reference;
    /** @var string */
    private $repository;
    /** @var string */
    private $vendor;
    /** @var string */
    private $package;

    //////////////////////////// SETTERS AND GETTERS \\\\\\\\\\\\\\\\\\\\\\\\\\\
    /**
     * @return string
     */
    final public function getBranch()
    {
        return $this->branch;
    }

    /**
     * @return array
     */
    final public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * @return string
     */
    final public function getPackage()
    {
        return $this->package;
    }

    /**
     * @return string
     */
    final public function getReference()
    {
        return $this->reference;
    }

    /**
     * @return string
     */
    final public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return string
     */
    final public function getVendor()
    {
        return $this->vendor;
    }

    //////////////////////////////// PUBLIC API \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    final public function __construct(
        $repository,
        array $credentials = [],
        $branch = self::BRANCH_MASTER,
        $reference = self::REFERENCE_HEAD
    ) {
        $this->isValidRepositoryName($repository);

        $this->branch = (string) $branch;
        $this->credentials = $credentials;
        $this->reference = (string) $reference;
        $this->repository = (string) $repository;

        list($this->vendor, $this->package) = explode('/', $repository);
    }

    ////////////////////////////// UTILITY METHODS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    /**
     * @param $repository
     */
    private function isValidRepositoryName($repository)
    {
        if (is_string($repository) === false
            || substr_count($repository, '/') !== 1
            || substr($repository, 0, 1) === '/'
            || substr($repository, -1, 1) === '/'
        ) {
            $message = sprintf(
                self::ERROR_INVALID_REPOSITORY_NAME,
                var_export($repository, true)
            );
            throw new \InvalidArgumentException($message);
        }
    }
}

/*EOF*/
