<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models\Adapters;

use Components\Projects\Models;
use Components\Projects\Helpers;
use Hubzero\Facades\Component;
use Hubzero\Facades\Filesystem;
use Hubzero\Facades\User;

/**
 * Projects Git adapter class
 */
class Nogit extends Models\Adapter
{
    protected $files;
    /** @var \Components\Projects\Helpers\Nogit|null */
    protected $nogit;
    protected $profileAssoc;

    /**
     * Adapter name
     *
     * @var  string
     */
    private $name = 'nogit';

    /**
     * Full path to repository
     *
     * @var  string
     */
    private $path = null;

    /**
     * Constructor
     *
     * @param   string  $path
     * @param   bool    $remote
     * @return  void
     */
    public function __construct($path = null, $remote = false)
    {
        // Get component configs
        $configs = Component::params('com_projects');

        // Set repo path
        $this->path = $path;
        $this->nogit = new Helpers\Nogit($this->path);

        $this->set('remote', $remote);
    }

    /**
     * Get file count
     *
     * @param   array  $params
     * @return  string|bool|null
     */
    public function count($params = [])
    {
        $cmd  = 'cd ' . escapeshellarg($this->path) . ' && ';
        $cmd .= 'find . \( -path "./.git" -or -name ".gitignore" \) -prune -o -type f -print | wc -l';
        return shell_exec($cmd);
    }

    /**
     * Get file list (retrieve and sort)
     *
     * @param   array  $params
     * @return  array
     */
    public function filelist($params = [])
    {
        // Parse incoming params and establish defaults
        $filter        = isset($params['filter']) ? $params['filter'] : '';
        $dirPath       = isset($params['subdir']) ? $params['subdir'] : '';
        $sortdir       = isset($params['sortdir']) && $params['sortdir'] == 'DESC' ? SORT_DESC : SORT_ASC;
        $sortby        = isset($params['sortby']) ? $params['sortby'] : 'name';
        $files         = isset($params['files']) && is_array($params['files']) ? $params['files'] : [];
        $dirsOnly      = isset($params['dirsOnly']) ? $params['dirsOnly'] : false;
        $showAll       = isset($params['showAll']) ? $params['showAll'] : false;
        $recursive     = isset($params['recursive']) ? $params['recursive'] : false;

        if (!$dirsOnly) {
            // Get a list of files from the git repository
            $files = empty($files) ? $this->nogit->getFiles($dirPath, $recursive) : $files;
        } else {
            $files = empty($files) ? $this->nogit->getDirectories($dirPath, $recursive) : $files;
        }

        // Output containers
        $items   = [];
        $sorting = [];

        // Apply the filter early, reduces iterations through foreach()
        if ($filter != '') {
            $files = preg_grep("(" . $filter . ")", $files);
        }

        // Go through items and get what we need
        foreach ($files as $item) {
            $item = rawurldecode($item);
            if (trim($item) == '') {
                continue;
            }
            // Load basic file metadata
            $file = new Models\File($item, $this->path);
            if ($this->shouldSkipFile($file)) {
                continue;
            }
            // Add to list
            if (empty($items[$file->get('name')])) {
                $items[$dirPath . DS . $file->get('localPath')] = $file;

                // Collect info for sorting
                switch ($sortby) {
                    case 'size':
                        $sorting[] = $file->getSize();
                        break;

                    case 'modified':
                        $sorting[] = $file->get('date');
                        break;

                    case 'localpath':
                        $sorting[] = strtolower($file->get('localPath'));
                        break;

                    case 'name':
                    default:
                        $sorting[] = strtolower($file->get('name'));
                        break;
                }
            }
        }

        // Sort
        array_multisort($sorting, $sortdir, $items);

        // Apply start and limit, get complete metadata and return
        return $this->list($items, $params);
    }

    /**
     * Check if file should be skipped
     *
     * @param   object  $file  Models\File
     * @return  bool
     */
    protected function shouldSkipFile($file)
    {
        $fileName = $file->get('name');
        $filesToSkip = ['.git', '.gitignore'];

        return in_array($fileName, $filesToSkip);
    }

    /**
     * Get file content and write to specified location
     *
     * @param   array  $params
     * @return  bool
     */
    public function content($params = [])
    {
        $fileName = isset($params['fileName']) ? $params['fileName'] : null;
        $target   = isset($params['target']) ? $params['target'] : null;

        return $this->nogit->getContent($fileName, $target);
    }

    /**
     * Get last file revision
     *
     * @param   array   $params
     * @return  bool|string
     */
    public function getLastRevision($params = [])
    {
        $file = isset($params['file']) ? $params['file'] : null;

        if (!($file instanceof Models\File)) {
            return false;
        }

        return $file->getMd5Hash();
    }

    /**
     * Get final list
     *
     * @param   array  $items
     * @param   array  $params
     * @param   array  $folders
     * @return  array
     */
    protected function list($items, $params, $folders = [])
    {
        $dirPath  = isset($params['subdir']) ? $params['subdir'] : null;
        $limit    = isset($params['limit']) ? $params['limit'] : 0;
        $start    = isset($params['start']) ? $params['start'] : 0;
        $pubLinks = isset($params['getPubConnections']) ? $params['getPubConnections'] : false;
        $extended = isset($params['showFullMetadata']) ? $params['showFullMetadata'] : true;
        $folders  = count($folders) > 0 ? $folders : false;

        // Skip forward?
        if ($start) {
            $items = array_slice($items, ($start - 1));
        }
        // No results, return early
        if (empty($items)) {
            return [];
        }

        // Go through sorted list, get extended metadata and limit results
        $i = 1;
        $results = [];
        foreach ($items as $file) {
            // First cut off at limit if set
            if ($limit && $i > $limit) {
                break;
            }

            // Now get more metadata
            $file->setSize();
            $file->setMimeType();
            $file->setIcon();

            $results[] = $file;
            $i++;
        }

        // Return top level folders or not.
        if (!$folders) {
            return $results;
        } else {
            return [$results, $folders];
        }
    }

    /**
     * Get changes for sync
     *
     * @param   array  $params
     * @return  array
     */
    public function getChanges($params = [])
    {
        $localPath    = isset($params['localPath']) ? $params['localPath'] : null;
        $fromLocal    = isset($params['fromLocal']) ? $params['fromLocal'] : null;
        $localDir     = isset($params['localDir']) ? $params['localDir'] : null;
        $localRenames = isset($params['localRenames']) ? $params['localRenames'] : null;
        $connections  = isset($params['connections']) ? $params['connections'] : null;

        return $this->nogit->getChanges($localPath, $fromLocal, $localDir, $localRenames, $connections);
    }

    /**
     * Move item
     *
     * @param   array  $params
     * @return  bool
     */
    public function move($params = [])
    {
        $fromFile = isset($params['fromFile']) ? $params['fromFile'] : null;
        $toFile   = isset($params['toFile']) ? $params['toFile'] : null;
        $type     = isset($params['type']) ? $params['type'] : 'file';
        $author   = isset($params['author']) ? $params['author'] : null;
        $date     = isset($params['date']) ? $params['date'] : null;

        if (!($fromFile instanceof Models\File) || !($toFile instanceof Models\File)) {
            return false;
        }

        $fromPath = escapeshellarg($fromFile->get('localPath'));
        $toPath = escapeshellarg($toFile->get('localPath'));
        $this->nogit->call("mv ./" . $fromPath . " ./" . $toPath);
        return true;
    }

    /**
     * Make dir
     *
     * @param   array  $params
     * @return  bool
     */
    public function makeDirectory($params = [])
    {
        $file = isset($params['file']) ? $params['file'] : null;

        if (!($file instanceof Models\File) || $file->get('type') != 'folder') {
            return false;
        }

        if (!$this->get('remote')) {
            if (Filesystem::makeDirectory($file->get('fullPath'), 0755, true, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Delete dir
     *
     * @param   array  $params
     * @return  bool
     */
    public function deleteDirectory($params = [])
    {
        $file   = isset($params['file']) ? $params['file'] : null;
        $author = isset($params['author']) ? $params['author'] : null;
        $date   = isset($params['date']) ? $params['date'] : null;

        if (!($file instanceof Models\File) || $file->get('type') != 'folder') {
            return false;
        }

        if (file_exists($file->get('fullPath'))) {
            // Remove directory that is not in Git
            if (!Filesystem::deleteDirectory($file->get('fullPath'))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Delete file
     *
     * @param   array  $params
     * @return  bool
     */
    public function deleteFile($params = [])
    {
        $file     = isset($params['file']) ? $params['file'] : null;
        $author   = isset($params['author']) ? $params['author'] : null;
        $date     = isset($params['date']) ? $params['date'] : null;

        if (!($file instanceof Models\File) || $file->get('type') != 'file' || !$file->exists()) {
            return false;
        }

        $this->nogit->call("rm ./" . escapeshellarg($file->get('localPath')));

        return true;
    }

    /**
     * Get file data and map to file object
     *
     * @param   object  $file     Models\File
     * @param   string  $dirPath
     * @param   string  $property
     * @return  array
     */
    protected function file($file, $dirPath, $property = null)
    {
        if (isset($this->files[$file->get('localPath')])) {
            return $property
                ? $this->files[$file->get('localPath')]->get($property)
                : $this->files[$file->get('localPath')];
        }

        if (!isset($this->profileAssoc)) {
            $this->profileAssoc = [];
        }

        // Entry is in the master log?
        if (isset($this->_fileLog[$file->get('localPath')])) {
            $log = $this->_fileLog[$file->get('localPath')];
        } else {
            $log = $this->nogit->gitLog($file->get('localPath'), 'combined');
        }

        // Map data we got
        if (!empty($log)) {
            foreach ($log as $name => $value) {
                $file->set($name, $value);
            }
        }

        // SFTP?  Need some parsing
        if ($file->get('message') && substr($file->get('message'), 0, 5) == 'SFTP-') {
            $file->set('origin', 'SFTP');

            if (isset($this->profileAssoc[trim($file->get('author'))])) {
                $profile = $this->profileAssoc[trim($file->get('author'))];
                $file->set('author', $profile->get('name'));
                $file->set('email', $profile->get('email'));
            } else {
                $profile = User::getInstance(trim($file->get('author')));
                if ($profile->get('id')) {
                    $this->profileAssoc[trim($file->get('author'))] = $profile;
                    $file->set('author', $profile->get('name'));
                    $file->set('email', $profile->get('email'));
                }
            }
        }

        // Store
        $this->files[$file->get('localPath')] = $file;

        return $property
            ? $this->files[$file->get('localPath')]->get($property)
            : $this->files[$file->get('localPath')];
    }

    /**
     * Get used disk space in repo
     *
     * @param   array    $params
     * @return  integer
     */
    public function getDiskUsage($params = [])
    {
        $path    = isset($params['path']) ? $params['path'] : $this->path;

        $used = 0;
        if ($path && is_dir($path)) {
            chdir($path);

            exec('du -sb --exclude=.git ', $out);

            if ($out && isset($out[0])) {
                $used = str_replace('.', '', trim($out[0]));
            }
        }

        return $used;
    }

    /**
     * Initialize repository
     *
     * @return  void
     */
    public function ini()
    {
        // Initialize
        $this->nogit->iniGit();
    }

    /**
     * Stub for checking in required for compatibility
     * @return  bool
     */
    public function checkin($params = [])
    {
        return true;
    }

    /**
     * Stub for optimizing, required for compatibility
     * @return  bool
     */
    public function optimize($params = [])
    {
        return true;
    }

    /**
     * Stub for erasing, required for compatibility
     * @return  bool
     */
    public function erase($params = [])
    {
        return true;
    }

    /**
     * Stub for checking if it's git, required for compatibility
     * @return  bool
     */
    public function isGit()
    {
        return false;
    }
    /**
     * Stub for diffing, required for compatibility
     * @return  bool
     */
    public function diff()
    {
        return false;
    }
    /**
     * Stub for restoring a file, required for compatibility
     * @return  bool
     */
    public function restore()
    {
        return false;
    }
    /**
     * Stub for discarding, required for compatibility
     * @return  bool
     */
    public function discard()
    {
        return true;
    }
    /**
     * Stub for getting trash, required for compatibility
     * @return  bool
     */
    public function getTrash()
    {
        return false;
    }
    /**
     * Stub for getting history, required for compatibility
     * @return  bool
     */
    public function history()
    {
        return false;
    }
}
