<?php

namespace Potherca\Flysystem\Github;

interface ApiInterface
{
    const KEY_CONTENTS = 'contents';

    /**
     * @param string $path
     *
     * @return bool
     */
    public function exists($path);

    /**
     * @param $path
     *
     * @return null|string
     *
     * @throws \Github\Exception\ErrorException
     */
    public function getFileContents($path);

    /**
     * @param string $path
     *
     * @return array
     */
    public function getLastUpdatedTimestamp($path);

    /**
     * @param string $path
     *
     * @return array|bool
     */
    public function getMetaData($path);

    /**
     * @param string $path
     * @param bool $recursive
     *
     * @return array
     */
    public function getRecursiveMetadata($path, $recursive);

    /**
     * @param string $path
     *
     * @return null|string
     */
    public function guessMimeType($path);
}
