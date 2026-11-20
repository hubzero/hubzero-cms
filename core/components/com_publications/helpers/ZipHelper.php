<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Helpers;

/**
 * Zip Archive helper
 */
class ZipHelper
{
    /**
     * Builds a hierarchical array of archive contents
     *
     * @param   string  $path
     * @return  array<mixed>
     */
    public static function getArchiveContents($path)
    {
        $archive  = self::getArchive($path);
        return self::parseArchiveContents($archive);
    }

    /**
     * Load bundle archive
     *
     * @param    string  $path
     * @return   \ZipArchive|null  \ZipArchive
     */
    protected static function getArchive($path)
    {
        $archive = null;

        if (file_exists($path)) {
            $archive = new \ZipArchive();
            $archive->open($path);
        }

        return $archive;
    }

    /**
     * Builds a hierarchical array of archive contents
     *
     * @param   \ZipArchive|null  $archive  \ZipArchive
     * @return  array<mixed>
     */
    protected static function parseArchiveContents($archive)
    {
        $parsedContents = [];
        $contents = self::readArchive($archive);

        foreach ($contents as $entry) {
            self::addEntryToContents($entry, $parsedContents);
        }

        return $parsedContents;
    }

    /**
     * Reads contents of a ZipArchive
     *
     * @param   \ZipArchive|mixed  $archive  \ZipArchive
     * @return  array<int, mixed>
     */
    protected static function readArchive($archive)
    {
        $contents = [];

        if ($archive instanceof \ZipArchive) {
            for ($i = 0; $i < $archive->numFiles; $i++) {
                $contents[] = $archive->statIndex($i);
            }
        }

        return $contents;
    }

    /**
     * Adds an entry to representational array
     *
     * @param   array<mixed>  $entry
     * @param   array<mixed>  $contents
     * @return  bool|null
     */
    protected static function addEntryToContents($entry, &$contents)
    {
        $directory = self::getEntryDirectory($entry);
        $entry['name'] = self::parseName($entry['name']);
        $entry['isDirectory'] = false;

        if ($directory === '') {
            $contents[] = $entry;
            return true;
        } else {
            if (!isset($contents[$directory])) {
                $contents[$directory] = [];
                $contents[$directory]['contents'] = [];
                $contents[$directory]['name'] = $directory;
                $contents[$directory]['isDirectory'] = true;
            }

            $contents[$directory]['contents'][] = $entry;
        }

        return null;
    }

    /**
     * Gets archive entry's directory
     *
     * @param   array<mixed>   $entry
     * @return  string
     */
    protected static function getEntryDirectory($entry)
    {
        $pathSegments = explode('/', $entry['name']);
        array_shift($pathSegments);
        array_pop($pathSegments);

        return implode($pathSegments);
    }

    /**
     * Removes path from entry's name
     *
     * @param   string  $name
     * @return  string
     */
    protected static function parseName($name)
    {
        $pathSegments = explode('/', $name);

        return array_pop($pathSegments);
    }
}
