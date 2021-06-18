<?php

namespace Potherca\Flysystem\Github;

interface SettingsInterface
{
    /**
     * @return string
     */
    public function getBranch();

    /**
     * @return array
     */
    public function getCredentials();

    /**
     * @return string
     */
    public function getPackage();

    /**
     * @return string
     */
    public function getReference();

    /**
     * @return string
     */
    public function getRepository();

    /**
     * @return string
     */
    public function getVendor();
}
