<?php

namespace Plugins\Citation\DefaultHandler;

use Hubzero\Plugin\Plugin;
use Hubzero\Facades\Event;

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Citations plugin class for default format
 */
class DefaultHandler extends Plugin
{
    /**
     * Affects constructor behavior. If true, language files will be loaded automatically.
     *
     * @var  boolean
     */
    protected $autoloadLanguage = true;

    /**
     * Return file type
     *
     * @return  void
     */
    public function onImportAcceptedFiles()
    {
        //return '.txt <small>(' . Lang::txt('PLG_CITATION_DEFAULT_FILE') . ')</small>';
    }

    /**
     * Import data from a file
     *
     * @param   \Symfony\Component\HttpFoundation\File\UploadedFile  $file  the uploaded file
     * @param   string|null   $scope
     * @param   integer|null  $scope_id
     * @return  array<mixed>|null
     */
    public function onImport($file, $scope = null, $scope_id = null)
    {
        // Array of acceptable file types
        $acceptable = ['txt'];
        // Get the file extension
        $extension = $file->getClientOriginalExtension();

        // Only process acceptable files
        if (!in_array($extension, $acceptable)) {
            return null;
        }

        // Get the file contents, uses temporary file (getFilename() is only its base name)
        $raw_contents = (string) file_get_contents($file->getPathname());

        // Check to see if this is endnote content
        if (preg_match('/%A|%0|%T/', $raw_contents)) {
            // Hand the same upload on as an EndNote file. This built an array,
            // with getName() and getClientSize() that UploadedFile does not
            // have, which no importer could take.
            $new_file = new \Symfony\Component\HttpFoundation\File\UploadedFile(
                $file->getPathname(),
                $file->getClientOriginalName() . '.enw',
                $file->getClientMimeType(),
                $file->getError(),
                true,
            );

            // The first importer that takes it answers; the rest say null
            foreach (Event::trigger('citation.onImport', [$new_file, $scope, $scope_id]) as $result) {
                if ($result) {
                    return $result;
                }
            }

            return null;
        }

        return null;
    }
}
