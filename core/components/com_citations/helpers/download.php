<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Citations\Helpers;

use Components\Citations\Download\Downloadable;
use Exception;
use Hubzero\Facades\Lang;

/**
 * Citations class for downloading a citation of a specific file type
 */
class Download
{
    /**
     * Active formatter instance
     *
     * @var object
     */
    protected $formatter;

    /**
     * Download formt
     *
     * @var string
     */
    protected $format = 'Bibtex';

    /**
     * Citation object
     *
     * @var \Components\Citations\Models\Citation
     */
    protected $reference;

    /**
     * List of formatters
     *
     * @var array<string, \Components\Citations\Download\Downloadable>
     */
    protected $formatters = [];

    /**
     * Mime type
     *
     * @var string
     */
    protected $mime = '';

    /**
     * File extension
     *
     * @var  string
     */
    protected $extension = '';

    /**
     * Constructor
     *
     * @param   \Components\Citations\Models\Citation|null  $reference
     * @param   string  $format
     * @return  void
     */
    public function __construct($reference = null, $format = 'Bibtex')
    {
        $this->setFormat($format);
        $this->setReference($reference);
    }

    /**
     * Set the format
     *
     * @param   string  $format
     * @return  void
     */
    public function setFormat($format)
    {
        $this->format = ucfirst(trim(strtolower($format)));
    }

    /**
     * Get the format
     *
     * @return  string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set the reference
     *
     * @param   \Components\Citations\Models\Citation|null  $reference
     * @return  void
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * Get the reference
     *
     * @return  \Components\Citations\Models\Citation|null  Null until one is set
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set the mime type
     *
     * @param   string  $mime
     * @return  void
     */
    public function setMimeType($mime)
    {
        $this->mime = $mime;
    }

    /**
     * Get the mime type
     *
     * @return  string
     */
    public function getMimeType()
    {
        return $this->mime;
    }

    /**
     * Set the extension
     *
     * @param   string  $extension
     * @return  void
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
    }

    /**
     * Get the extension
     *
     * @return  string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set formatter for specified format
     *
     * @param   \Components\Citations\Download\Downloadable  $formatter
     * @param   string  $format
     * @return  void
     */
    public function setFormatter($formatter, $format = '')
    {
        $format = ($format) ? $format : $this->format;

        $this->formatters[$format] = $formatter;
    }

    /**
     * Get the formatter object, if set
     *
     * @param   string  $format  Format to get
     * @return  \Components\Citations\Download\Downloadable|null
     */
    public function getFormatter($format = '')
    {
        $format = ($format) ? $format : $this->format;

        return (isset($this->formatters[$format])) ? $this->formatters[$format] : null;
    }

    /**
     * Format a record
     *
     * @param   \Components\Citations\Models\Citation|false|null  $reference  Record to format
     * @return  string
     */
    public function formatReference($reference = null)
    {
        if (!$reference) {
            $reference = $this->getReference();
        }

        if (!$reference) {
            return '';
        }

        $format = $this->getFormat();

        $formatter = $this->getFormatter($format);

        if (!$formatter) {
            $cls = '\\Components\\Citations\\Download\\' . $format;

            if (!class_exists($cls)) {
                throw new Exception(Lang::txt('Download format unavailable.'), 500);
            }

            $formatter = new $cls();

            if (!$formatter instanceof Downloadable) {
                throw new Exception(Lang::txt('Invalid download formatter specified.'), 500);
            }

            $this->setFormatter($formatter, $format);
            $this->setExtension($formatter->getExtension());
            $this->setMimeType($formatter->getMimeType());
        } else {
            $this->setExtension($formatter->getExtension());
            $this->setMimeType($formatter->getMimeType());
        }

        return $formatter->format($reference);
    }
}
