<?php

/**
 * Download helper for file streaming and ZIP bundling.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Site\Lib;

use Hubzero\Facades\Session;

class Dl
{
    /**
     * Generate an HMAC-signed token for a file path.
     *
     * Stores the path in the Hubzero session keyed by the token,
     * so the download/gallery endpoint can retrieve it.
     *
     * @param   string  $path  File path to sign
     * @param   string  $type  Download type identifier
     * @return  string  HMAC token
     */
    public static function getDlHash($path, $type = 'file_download')
    {
        $secret = \Hubzero\Facades\App::get('config')->get('secret', '');
        $token = hash_hmac('sha256', $type . '|' . $path, $secret);

        // Store in session so the endpoint can look up the path by token
        $list = Session::get('dv.' . $type . '.list', []);
        $list[$token] = $path;
        Session::set('dv.' . $type . '.list', $list);

        return $token;
    }

    /**
     * Verify an HMAC-signed download token.
     *
     * @param   string  $token  Token to verify
     * @param   string  $path   Expected file path
     * @param   string  $type   Download type identifier
     * @return  bool
     */
    public static function verifyHash(
        string $token,
        string $path,
        string $type = 'file_download'
    ): bool {
        $secret = \Hubzero\Facades\App::get('config')->get('secret', '');
        $expected = hash_hmac('sha256', $type . '|' . $path, $secret);

        return hash_equals($expected, $token);
    }

    /**
     * Stream a file to the browser.
     *
     * @param   string  $hash  Download token
     * @return  void
     */
    public static function streamFile($hash)
    {
        $fullpath = '';
        $list = Session::get('dv.file_download.list', []);
        if (isset($list[$hash])) {
            $fullpath = $list[$hash];

            $ext = strtolower(pathinfo($fullpath, PATHINFO_EXTENSION));
            $mimeMap = [
                'csv' => 'text/csv',
                'zip' => 'application/zip',
                'pdf' => 'application/pdf',
            ];
            $mimetype = $mimeMap[$ext] ?? 'application/octet-stream';

            if (file_exists($fullpath)) {
                header('Content-Description: File Transfer');
                header('Content-Type: ' . $mimetype);
                header('Content-Length: ' . filesize($fullpath));
                $safeName = str_replace(' ', '_', basename($fullpath));
                header(
                    'Content-Disposition: attachment; filename="'
                    . $safeName . '"'
                );
                ob_end_flush();
                readfile($fullpath);
                exit(0);
            }
        }
        print \Hubzero\Facades\Lang::txt('COM_DATAVIEWER_ERROR_INVALID_FILE', htmlspecialchars($fullpath));
    }

    /**
     * Create a ZIP of multiple files and stream it.
     *
     * Uses ZipArchive instead of shell `zip` command.
     *
     * @param   string  $hashList  Comma-separated hash tokens
     * @return  void
     */
    public static function zipFiles($hashList)
    {
        $hashes = explode(',', $hashList);
        $files = [];

        $list = Session::get('dv.file_download.list', []);
        foreach ($hashes as $hash) {
            if (isset($list[$hash])) {
                $path = $list[$hash];
                if (file_exists($path)) {
                    $files[] = $path;
                }
            }
        }

        if (empty($files)) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
            exit;
        }

        $tmp = tempnam(sys_get_temp_dir(), 'dv_zip_');
        $zip = new \ZipArchive();

        if ($zip->open($tmp, \ZipArchive::OVERWRITE) !== true) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error');
            exit;
        }

        foreach ($files as $file) {
            $zip->addFile($file, basename($file));
        }

        $zip->close();

        header('Content-Description: File Transfer');
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="selected_files.zip"');
        header('Content-Length: ' . filesize($tmp));

        ob_end_flush();
        readfile($tmp);
        unlink($tmp);
        exit(0);
    }
}
