<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models;

use Hubzero\Utility\Str;
use ZipArchive;

/**
 * Off-request publication bundle build engine (core; replaces the PURR-only
 * app/bin/rebuild-publication-bundle).
 *
 * Builds bundles/bundle.zip from a version's primary (role 1) files with the
 * system zip(1) binary — ZIP64 for >4 GB members and stored (no deflate) for
 * already-compressed extensions, which is what lets a multi-GB dataset finish
 * in seconds instead of timing out the way the in-request PHP ZipArchive path
 * does. It then assembles the served <DOI>.zip by adding the bundle as a
 * stored <DOI>/bundle.zip entry to the gallery+README outer that publish
 * produced — matching curation::package()'s structure without the size guard
 * or request timeout.
 *
 * No web/URL context required (pure file zipping), so it is safe to run from a
 * detached CLI worker (muse publications:bundle) where absolute URLs would be
 * wrong.
 */
class BundleBuilder
{
	/**
	 * Extensions whose contents are already compressed — zip(1) stores these
	 * (-n) instead of wasting CPU trying to deflate them.
	 *
	 * @return  array
	 */
	public static function compressedExtList()
	{
		return array(
			'zip', 'gz', 'bz2', 'xz', 'tgz', '7z', 'rar', 'lz4', 'zst',
			'jpg', 'jpeg', 'png', 'gif', 'webp', 'heic', 'heif',
			'mp4', 'm4v', 'mov', 'mkv', 'avi', 'wmv', 'webm', 'flv',
			'mp3', 'm4a', 'aac', 'ogg', 'opus', 'flac',
			'docx', 'xlsx', 'pptx', 'odt', 'ods', 'odp', 'epub',
			'pdf',
		);
	}

	/**
	 * Build a version's bundle (inner bundle.zip + served <DOI>.zip).
	 *
	 * @param   integer   $versionId
	 * @param   array     $opts  force(bool), level(int|null), log(callable|null)
	 * @return  array     ok, file, size, source_hash, inner, error
	 */
	public function build($versionId, $opts = array())
	{
		$versionId = (int) $versionId;
		$force     = !empty($opts['force']);
		$level     = isset($opts['level']) ? (int) $opts['level'] : 1;
		$log       = (isset($opts['log']) && is_callable($opts['log'])) ? $opts['log'] : function ($m) {};

		$fail = function ($msg) {
			return array('ok' => false, 'error' => $msg, 'file' => null, 'size' => null, 'source_hash' => null, 'inner' => null);
		};

		$version = $this->versionRow($versionId);
		if (!$version)
		{
			return $fail('Publication version ' . $versionId . ' not found.');
		}

		$pubId  = (int) $version['publication_id'];
		$secret = (string) $version['secret'];

		$webpath = trim(\Component::params('com_publications')->get('webpath', '/site/publications'), '/');
		$base    = PATH_APP . DS . $webpath . DS . Str::pad($pubId) . DS . Str::pad($versionId);
		$content = $base . DS . $secret;

		if (!is_dir($base) || !is_dir($content))
		{
			return $fail('Publication storage not found: ' . $base);
		}

		$files = $this->primaryFiles($versionId, $content);
		if (count($files) < 1)
		{
			return $fail('No primary (role 1) files found for version ' . $versionId . '.');
		}
		// The system-zip backend junks paths (basenames); verify no in-archive
		// name carries a subdirectory (matches the existing bundle layout).
		foreach ($files as $abs => $name)
		{
			if (basename($abs) !== $name)
			{
				return $fail('Primary file uses a subdirectory ("' . $name . '"); the system-zip backend expects flat names.');
			}
		}

		$sourceHash = $this->sourceHash($files);
		$name       = $this->bundleName($version);

		$bundleDir  = $base . DS . 'bundles';
		$innerPath  = $bundleDir . DS . 'bundle.zip';
		$outerPath  = $base . DS . $name . '.zip';

		if (!is_dir($bundleDir) && !mkdir($bundleDir, 0775, true) && !is_dir($bundleDir))
		{
			return $fail('Cannot create ' . $bundleDir);
		}
		@chgrp($bundleDir, 'access-content');

		if (is_file($outerPath) && !$force)
		{
			// Nothing to do unless forced; report the existing artifact.
			return array(
				'ok' => true, 'file' => $outerPath, 'size' => filesize($outerPath),
				'source_hash' => $sourceHash, 'inner' => (is_file($innerPath) ? $innerPath : null), 'error' => null
			);
		}

		// --- inner bundle.zip (system zip) -------------------------------
		$log(sprintf('Building bundle.zip from %d primary file(s)...', count($files)));
		$res = $this->buildInner($files, $innerPath, $level);
		if (!$res['ok'])
		{
			return $fail($res['error']);
		}
		$log(sprintf('bundle.zip: %d bytes', filesize($innerPath)));

		// --- served outer <DOI>.zip --------------------------------------
		$res = $this->buildOuter($innerPath, $outerPath, $name);
		if (!$res['ok'])
		{
			return $fail($res['error']);
		}

		return array(
			'ok' => true,
			'file' => $outerPath,
			'size' => filesize($outerPath),
			'source_hash' => $sourceHash,
			'inner' => $innerPath,
			'error' => null,
		);
	}

	/**
	 * Current source signature for a built version: a hash over the sorted
	 * (name, size, mtime) of the primary files. Stat only, no reading.
	 *
	 * @param   array   $files  abs => name
	 * @return  string
	 */
	public function sourceHash($files)
	{
		$parts = array();
		foreach ($files as $abs => $name)
		{
			$parts[] = $name . ':' . @filesize($abs) . ':' . @filemtime($abs);
		}
		sort($parts);

		return hash('sha256', implode("\n", $parts));
	}

	/**
	 * Load the version row fields we need.
	 *
	 * @param   integer  $versionId
	 * @return  array|false
	 */
	protected function versionRow($versionId)
	{
		$db = \App::get('db');
		$db->setQuery(
			"SELECT `publication_id`, `secret`, `version_number`, `doi`
			 FROM `#__publication_versions` WHERE `id` = " . (int) $versionId . " LIMIT 1"
		);

		$row = $db->loadAssoc();

		return $row ?: false;
	}

	/**
	 * Resolve the version's primary (role 1) file attachments to abs => name.
	 *
	 * @param   integer  $versionId
	 * @param   string   $content   the version's secret content directory
	 * @return  array
	 */
	protected function primaryFiles($versionId, $content)
	{
		$db = \App::get('db');
		$db->setQuery(
			"SELECT `path` FROM `#__publication_attachments`
			 WHERE `publication_version_id` = " . (int) $versionId . "
			   AND `type` = 'file' AND `role` = 1
			 ORDER BY `ordering`, `id`"
		);

		$files = array();
		foreach ((array) $db->loadColumn() as $relPath)
		{
			$relPath = ltrim((string) $relPath, '/');
			$abs     = $content . DS . $relPath;
			if (is_file($abs))
			{
				$files[$abs] = basename($relPath);
			}
		}

		return $files;
	}

	/**
	 * The served bundle base name, matching curation::getBundleName().
	 *
	 * @param   array   $version
	 * @return  string
	 */
	protected function bundleName($version)
	{
		$doi = isset($version['doi']) ? trim((string) $version['doi']) : '';
		if ($doi !== '')
		{
			return str_replace(array('.', '/'), '_', $doi);
		}

		return 'Publication_' . (int) $version['publication_id'];
	}

	/**
	 * Build bundle.zip with the system zip binary (ZIP64; stored for
	 * already-compressed extensions). Atomic via a temp file + rename.
	 *
	 * @param   array    $files
	 * @param   string   $innerPath
	 * @param   integer  $level
	 * @return  array    ok, error
	 */
	protected function buildInner($files, $innerPath, $level)
	{
		if (!$this->systemZipAvailable())
		{
			return array('ok' => false, 'error' => 'The system "zip" binary is not available.');
		}

		$tmp = $innerPath . '.build.' . getmypid();
		@unlink($tmp);

		$cmd = array('zip', '-' . max(0, min(9, (int) $level)), '-j', '-X', '-q',
			'-n', '.' . implode(':.', self::compressedExtList()), $tmp);
		foreach ($files as $abs => $_)
		{
			$cmd[] = $abs;
		}

		$rc = $this->runQuiet($cmd);

		if ($rc !== 0 || !is_file($tmp))
		{
			@unlink($tmp);
			return array('ok' => false, 'error' => 'zip exited with status ' . $rc);
		}

		@chgrp($tmp, 'access-content');
		@chmod($tmp, 0664);

		if (!rename($tmp, $innerPath))
		{
			@unlink($tmp);
			return array('ok' => false, 'error' => 'Could not move ' . $tmp . ' into place.');
		}

		return array('ok' => true, 'error' => null);
	}

	/**
	 * Assemble the served <name>.zip by adding the (already built) bundle.zip
	 * as a stored <name>/bundle.zip entry to the publish-time gallery+README
	 * outer. Atomic via a temp copy + rename.
	 *
	 * @param   string  $innerPath
	 * @param   string  $outerPath
	 * @param   string  $name
	 * @return  array   ok, error
	 */
	protected function buildOuter($innerPath, $outerPath, $name)
	{
		$entry = $name . '/' . basename($innerPath);
		$tmp   = $outerPath . '.build.' . getmypid();
		@unlink($tmp);

		if (!is_file($outerPath))
		{
			// The gallery+README outer is produced at publish time; without it
			// we'd have to re-synthesise package()'s structure. Require it.
			return array('ok' => false, 'error' => 'No existing ' . basename($outerPath)
				. ' to update; run publish/package first to produce the gallery+README bundle.');
		}

		if (!copy($outerPath, $tmp))
		{
			return array('ok' => false, 'error' => 'Could not copy ' . $outerPath);
		}

		$zip = new ZipArchive();
		if ($zip->open($tmp) !== true)
		{
			@unlink($tmp);
			return array('ok' => false, 'error' => 'Could not open ' . $tmp . ' for update.');
		}

		if ($zip->locateName($entry) !== false)
		{
			$zip->deleteName($entry);
		}

		if (!$zip->addFile($innerPath, $entry))
		{
			$zip->close();
			@unlink($tmp);
			return array('ok' => false, 'error' => 'Could not add ' . $entry . ' to the bundle.');
		}
		$zip->setCompressionName($entry, ZipArchive::CM_STORE);

		if (!$zip->close())
		{
			@unlink($tmp);
			return array('ok' => false, 'error' => 'Failed to write ' . $tmp);
		}

		@chgrp($tmp, 'access-content');
		@chmod($tmp, 0664);

		if (!rename($tmp, $outerPath))
		{
			@unlink($tmp);
			return array('ok' => false, 'error' => 'Could not move ' . $tmp . ' into place.');
		}

		return array('ok' => true, 'error' => null);
	}

	/**
	 * Is the system zip binary available?
	 *
	 * @return  boolean
	 */
	protected function systemZipAvailable()
	{
		$out = array(); $rc = 1;
		@exec('command -v zip 2>/dev/null', $out, $rc);

		return ($rc === 0 && !empty($out));
	}

	/**
	 * Run a command (argv array) quietly, returning its exit code.
	 *
	 * @param   array    $cmd
	 * @return  integer
	 */
	protected function runQuiet($cmd)
	{
		$line = implode(' ', array_map('escapeshellarg', $cmd));

		$proc = proc_open($line, array(
			0 => array('file', '/dev/null', 'r'),
			1 => array('file', '/dev/null', 'w'),
			2 => array('pipe', 'w'),
		), $pipes);

		if (!is_resource($proc))
		{
			return 1;
		}

		$err = stream_get_contents($pipes[2]);
		fclose($pipes[2]);

		return proc_close($proc);
	}
}
