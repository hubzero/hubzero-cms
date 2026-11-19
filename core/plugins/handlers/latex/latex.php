<?php

namespace Plugins\Handlers\Latex;

use Hubzero\Plugin\Plugin;
use Hubzero\Filesystem\Manager;
use Hubzero\Filesystem\File;
use Hubzero\Facades\Lang;

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Plugin class for latex file handling
 */
class Latex extends Plugin
{
    /**
     * Affects constructor behavior. If true, language files will be loaded automatically.
     *
     * @var  boolean
     */
    protected $autoloadLanguage = true;

    /**
     * Determines if the given collection can be handled by this plugin
     *
     * @param   \Hubzero\Filesystem\Collection  $collection  The file collection to assess
     * @return  boolean
     **/
    public function canHandle(\Hubzero\Filesystem\Collection $collection)
    {
        $need = [
            'tex' => 1,
        ];

        // Check extension to make sure we can proceed
        if (!$collection->hasExtensions($need)) {
            return false;
        }

        return true;
    }

    /**
     * Handles view events for latex files
     *
     * @param   \Hubzero\Filesystem\Collection  $collection  The file collection to view
     * @return  \Hubzero\Plugin\View|false
     **/
    public function onHandleView(\Hubzero\Filesystem\Collection $collection)
    {
        if (!$this->canHandle($collection)) {
            return false;
        }

        $file = $collection->findFirstWithExtension('tex');
        if (!$file) {
            return false;
        }

        // Create view
        $view = new \Hubzero\Plugin\View([
            'folder'  => 'handlers',
            'element' => 'latex',
            'name'    => 'latex',
            'layout'  => 'view',
        ]);

        // Build path for storing temp previews
        $outputDir = PATH_APP . DS . trim($this->params->get('compile_dir', 'site/latex/compiled'), DS);
        $adapter   = Manager::adapter('local', ['path' => $outputDir]);
        $uniqid    = md5(uniqid());
        $temp      = File::fileFromPath($uniqid . '.tex', $adapter);

        // Clean up data from Windows characters - important!
        $data = (string) preg_replace('/[^(\x20-\x7F)\x0A]*/', '', $file->read());

        // Store file locally
        $temp->write($data);

        // Build the command. The TeX is user-supplied, so: no shell escape,
        // kpathsea's paranoid file access (no absolute paths, no parent
        // directories, no dotfiles for \input, \openin or \openout -- a
        // document could otherwise \input any file the web user can read into
        // the served PDF), and a time limit so a looping document does not
        // hold a worker. Run from the output directory so the input is a
        // relative name that paranoid mode accepts.
        $timeout = max(1, (int) $this->params->get('timeout', 60));

        $command  = 'cd ' . escapeshellarg($outputDir) . ' && ';
        $command .= 'openin_any=p openout_any=p shell_escape=f ';
        $command .= 'timeout ' . (int) $timeout . ' ';
        $command .= escapeshellarg(DS . trim($this->params->get('texpath', '/usr/bin/pdflatex'), DS));
        $command .= ' -no-shell-escape -interaction=batchmode ' . escapeshellarg($uniqid . '.tex');

        $out = [];
        // Exec and capture output
        exec($command, $out);

        $compiled = File::fromPath($uniqid . '.pdf', $adapter);
        $log      = File::fromPath($uniqid . '.log', $adapter);

        // There is no size(): getSize(true) is the size in bytes, or false
        // when the file is not there, as when the compile failed
        if (!($compiled instanceof File) || !$compiled->getSize(true)) {
            $view->setError(Lang::txt('PLG_HANDLERS_LATEX_ERROR_COMPILE_TEX_FAILED'));
        }

        // Read log (to show in case of error)
        if ($log instanceof File && $log->getSize(true)) {
            $view->set('log', $log->read());
        }

        $view->set('compiled', $compiled);

        return $view;
    }
}
