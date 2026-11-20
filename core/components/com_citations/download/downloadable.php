<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Citations\Download;

/**
 * Abstract class for citations download
 */
abstract class Downloadable
{
    /**
     * Mime type
     *
     * @var string
     */
    protected $mime = '';

    /**
     * File extension
     *
     * @var string
     */
    protected $extension = '';

    /**
     * Set the mime type
     *
     * @param      string $mime Value to set
     * @return     void
     */
    public function setMimeType($mime)
    {
        $this->mime = trim($mime);
    }

    /**
     * Get the mime type
     *
     * @return     string
     */
    public function getMimeType()
    {
        return $this->mime;
    }

    /**
     * Set the file extension
     *
     * @param      string $ext Value to set
     * @return     void
     */
    public function setExtension($ext)
    {
        $this->extension = trim($ext);
    }

    /**
     * Get the file extension
     *
     * @return     string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Format the file
     *
     * @param   mixed  $row
     * @return     string
     */
    public function format($row)
    {
        return '';
    }
}
