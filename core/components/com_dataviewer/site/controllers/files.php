<?php

/**
 * File download and gallery controller for the Dataviewer component.
 *
 * Handles file streaming, ZIP downloads, and gallery views.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Site\Controllers;

use Components\Dataviewer\Site\DvConfig;
use Components\Dataviewer\Site\Helpers\ConfigFactory;
use Hubzero\Component\SiteController;
use Hubzero\Facades\Request;
use Hubzero\View\Blade;

class Files extends SiteController
{
    /**
     * View engines this controller accepts.
     *
     * @var  array
     */
    protected $viewEngines = ['blade', 'php'];

    /**
     * CSS frameworks this controller accepts.
     *
     * @var  array
     */
    protected $cssFrameworks = ['daisyui', 'classic'];

    /**
     * Supported image extensions for gallery scanning.
     *
     * @var  array
     */
    private static $imageTypes = ['png', 'gif', 'jpg', 'jpeg'];

    /**
     * Execute the controller.
     *
     * @return  void
     */
    public function execute()
    {
        $this->registerTask('stream_file', 'download');
        $this->registerTask('file', 'download');

        parent::execute();
    }

    /**
     * Download or display a file (default task).
     *
     * @return  void
     */
    public function downloadTask()
    {
        // Delegates to legacy static class for file streaming.
        // TODO Phase 6: Replace session-based tokens with HMAC-signed URLs.
        \Components\Dataviewer\Site\View\File::render();
    }

    /**
     * Display the image gallery.
     *
     * Renders a standalone HTML page (not embedded in site template)
     * with thumbnails, a viewer, and toolbar.
     *
     * @return  void
     */
    public function galleryTask()
    {
        $hash = Request::getString('hash', '');

        // Resolve gallery path from session
        $galleryList = \Hubzero\Facades\Session::get('dv.gallery.list', []);
        if (!isset($galleryList[$hash])) {
            \Hubzero\Facades\App::abort(
                404,
                \Hubzero\Facades\Lang::txt('COM_DATAVIEWER_ERROR_INVALID_GALLERY')
            );
            return;
        }

        $httpPath = $galleryList[$hash];
        $pathParts = explode('/site/', $httpPath);
        $realPath = '/site/' . ($pathParts[1] ?? '');

        $absPath = PATH_ROOT . $realPath;
        if (!is_dir($absPath)) {
            \Hubzero\Facades\App::abort(404, \Hubzero\Facades\Lang::txt('COM_DATAVIEWER_ERROR_MISSING_IMAGES'));
            return;
        }

        // Path traversal guard
        $resolvedPath = realpath($absPath);
        if (
            $resolvedPath === false
            || strpos($resolvedPath, realpath(PATH_ROOT)) !== 0
        ) {
            \Hubzero\Facades\App::abort(403, \Hubzero\Facades\Lang::txt('COM_DATAVIEWER_ERROR_ACCESS_DENIED'));
            return;
        }

        // Scan for images
        $fileList = self::scanForImages($resolvedPath);

        $imageList = [];
        $imageViewer = [];

        foreach ($fileList as $file) {
            $pi = pathinfo($file);
            $dirName = str_replace(PATH_ROOT, '', $pi['dirname']);
            $ext = strtolower($pi['extension']);

            $descFile = $pi['dirname'] . '/' . $pi['filename'] . '.txt';
            $desc = '';
            if (file_exists($descFile)) {
                $desc = htmlentities(
                    file_get_contents($descFile),
                    ENT_QUOTES,
                    'UTF-8'
                );
            }

            if (in_array($ext, self::$imageTypes)) {
                $basename = htmlspecialchars(
                    $pi['basename'],
                    ENT_QUOTES,
                    'UTF-8'
                );
                $imageList[] = '<img title="' . $desc
                    . '" alt="' . $basename
                    . '" src="' . $dirName . '/small/' . $basename . '" />';
                $imageViewer[] = '<a title="Click to view the original image."'
                    . ' target="_blank" href="' . $dirName . '/' . $basename
                    . '"><img alt="' . $basename . '" src="' . $dirName
                    . '/medium/' . $basename
                    . '" style="display:none;" /></a>';
            }
        }

        // Build asset path
        $config = ConfigFactory::build();
        $htmlPath = $config['html_path'];

        // Render standalone Blade template and output directly
        $templatePath = dirname(__DIR__)
            . '/views/files/tmpl/gallery.blade.php';

        $html = Blade::render($templatePath, [
            'imageList'   => $imageList,
            'imageViewer' => $imageViewer,
            'htmlPath'    => $htmlPath,
        ]);

        echo $html;
        \Hubzero\Facades\App::close();
    }

    /**
     * Display task — not used, redirects to download.
     *
     * @return  void
     */
    public function displayTask()
    {
        $this->downloadTask();
    }

    /**
     * Recursively scan a directory for image files, excluding
     * 'small' and 'medium' thumbnail subdirectories.
     *
     * @param   string  $dir  Directory to scan
     * @return  array   List of absolute file paths
     */
    private static function scanForImages(string $dir): array
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $dir,
                \RecursiveDirectoryIterator::SKIP_DOTS
            )
        );

        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $path = $file->getPathname();

            // Skip thumbnail directories
            if (
                strpos($path, '/small/') !== false
                || strpos($path, '/medium/') !== false
            ) {
                continue;
            }

            $ext = strtolower($file->getExtension());
            if (in_array($ext, self::$imageTypes)) {
                $files[] = $path;
            }
        }

        sort($files);
        return $files;
    }
}
