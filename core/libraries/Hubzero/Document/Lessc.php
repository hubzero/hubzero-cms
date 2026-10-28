<?php

/**
 * @package framework
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Document;

// Add back cachedCompile() removed from splitbrain's
// opinionated LesserPHP fork

class Lessc extends \LesserPHP\Lessc
{
    /**
     * Execute lessphp on a .less file or a lessphp cache structure
     *
     * The lessphp cache structure contains information about a specific
     * less file having been parsed. It can be used as a hint for future
     * calls to determine whether or not a rebuild is required.
     *
     * The cache structure contains two important keys that may be used
     * externally:
     *
     * compiled: The final compiled CSS
     * updated: The time (in seconds) the CSS was last compiled
     *
     * The cache structure is a plain-ol' PHP associative array and can
     * be serialized and unserialized without a hitch.
     *
     * @param mixed $in Input
     * @param bool $force Force rebuild?
     * @return array lessphp cache structure
     * @throws Exception
     */
    public function cachedCompile($in, $force = false)
    {
        // assume no root
        $root = null;

        if (is_string($in)) {
            $root = $in;
        } elseif (is_array($in) and isset($in['root'])) {
            if ($force or !isset($in['files'])) {
                // If we are forcing a recompile or if for some reason the
                // structure does not contain any file information we should
                // specify the root to trigger a rebuild.
                $root = $in['root'];
            } elseif (isset($in['files']) and is_array($in['files'])) {
                foreach ($in['files'] as $fname => $ftime) {
                    if (!file_exists($fname) or filemtime($fname) > $ftime) {
                        // One of the files we knew about previously has changed
                        // so we should look at our incoming root again.
                        $root = $in['root'];
                        break;
                    }
                }
            }
        } else {
            // TODO: Throw an exception? We got neither a string nor something
            // that looks like a compatible lessphp cache structure.
            return null;
        }

        if ($root !== null) {
            // If we have a root value which means we should rebuild.
            $out = [];
            $out['root'] = $root;
            $out['compiled'] = $this->compileFile($root);
            $out['files'] = $this->allParsedFiles;
            $out['updated'] = time();
            return $out;
        } else {
            // No changes, pass back the structure
            // we were given initially.
            return $in;
        }
    }
}
