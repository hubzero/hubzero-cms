<?php

/**
 * File streaming handler for the Dataviewer component.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Site\View;

use Components\Dataviewer\Site\DvConfig;

class File
{
    /**
     * MIME type map for inline-displayable files.
     *
     * @var  array
     */
    private static $inlineMimes = [
        'jpeg' => 'image/jpeg',
        'jpg'  => 'image/jpeg',
        'png'  => 'image/png',
        'gif'  => 'image/gif',
        'pdf'  => 'application/pdf',
    ];

    /**
     * Stream a file to the browser.
     *
     * @return  void
     */
    public static function render()
    {
        $hash = \Hubzero\Facades\Request::getString('hash');
        $hashList = \Hubzero\Facades\Request::getString('hash_list');

        if ($hash != '') {
            $dlList = \Hubzero\Facades\Session::get('dv.file_download.list', []);
            if (!isset($dlList[$hash])) {
                \Hubzero\Facades\App::abort(403, \Hubzero\Facades\Lang::txt('COM_DATAVIEWER_ERROR_INVALID_DOWNLOAD'));
                return;
            }
            $file = $dlList[$hash];
            $fileName = basename($file);
            $fullPath = $file;
        } elseif ($hashList != '') {
            \Components\Dataviewer\Site\Lib\Dl::zipFiles($hashList);
            return;
        } else {
            $basePath = DvConfig::$dv_conf['base_path'];
            $file = \Hubzero\Facades\Request::getString('f', '');
            if ($file === '') {
                \Hubzero\Facades\App::abort(400, \Hubzero\Facades\Lang::txt('COM_DATAVIEWER_ERROR_NO_FILE'));
                return;
            }
            $fileName = basename($file);
            $fullPath = $basePath . $file;

            // Path traversal protection: ensure resolved path
            // stays within the base path
            $realBase = realpath($basePath);
            $realFull = realpath($fullPath);
            if (
                $realBase === false
                || $realFull === false
                || strpos($realFull, $realBase . DIRECTORY_SEPARATOR) !== 0
            ) {
                \Hubzero\Facades\App::abort(403, \Hubzero\Facades\Lang::txt('COM_DATAVIEWER_ERROR_ACCESS_DENIED'));
                return;
            }
            $fullPath = $realFull;
        }

        if (!$file || !file_exists($fullPath) || !is_file($fullPath)) {
            \Hubzero\Facades\App::abort(404, \Hubzero\Facades\Lang::txt('COM_DATAVIEWER_ERROR_FILE_NOT_FOUND'));
            return;
        }

        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        if (isset(self::$inlineMimes[$ext])) {
            header('X-Content-Type-Options: nosniff');
            header('Content-Type: ' . self::$inlineMimes[$ext]);
            header(
                'Content-Disposition: inline; filename="'
                . $fileName . '"'
            );
        } else {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header(
                'Content-Disposition: attachment; filename="'
                . $fileName . '"'
            );
            header('Content-Transfer-Encoding: binary');
        }

        header('Content-Length: ' . filesize($fullPath));
        header(
            'Last-Modified: '
            . gmdate('D, d M Y H:i:s T', filemtime($fullPath))
        );

        ob_clean();
        ob_end_flush();
        readfile($fullPath);
        exit;
    }
}
