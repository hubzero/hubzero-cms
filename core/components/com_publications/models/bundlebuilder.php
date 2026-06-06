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
	 * Per-version cache of includeInPackage-excluded element ids.
	 *
	 * @var  array
	 */
	protected $incExcludedCache = array();

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
	 * Build a version's served <DOI>.zip. Multi-file multiZip publications get a
	 * nested inner bundle (bundle.zip / <title>.zip); single-file and
	 * non-multiZip publications get their files placed flat in the outer. The
	 * layout matches whatever the version already serves (see servedLayout).
	 *
	 * @param   integer   $versionId
	 * @param   array     $opts  force(bool), level(int|null), log(callable|null)
	 * @return  array     ok, file, size, source_hash, inner, error
	 */
	public function build($versionId, $opts = array())
	{
		$versionId = (int) $versionId;
		$force     = !empty($opts['force']);
		$level     = isset($opts['level']) ? (int) $opts['level'] : 6;
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

		// Primary (role-1) files decide the layout; the full file set (all
		// roles) is what a flat outer must carry. (resolveFiles honors each
		// element's includeInPackage, so excluded files never reach the bundle.)
		$primary = $this->primaryFiles($versionId, $content);
		if (count($primary) < 1)
		{
			return $fail('No primary (role 1) files found for version ' . $versionId . '.');
		}
		$allFiles = $this->attachmentFiles($versionId, $content);

		// In-archive names are paths relative to the content dir, preserving the
		// publication's directory structure (matching the original packager).
		// Reject anything that escapes that root.
		foreach ($allFiles as $abs => $rel)
		{
			if ($rel === '' || $rel[0] === '/' || preg_match('#(^|/)\.\.(/|$)#', $rel))
			{
				return $fail('Unsafe file path: "' . $rel . '"');
			}
		}

		$sourceHash = $this->sourceHash($primary);
		$name       = $this->bundleName($version);

		$bundleDir  = $base . DS . 'bundles';
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
				'source_hash' => $sourceHash, 'inner' => null, 'error' => null
			);
		}

		// The served outer also carries the publish-time metadata: hubREADME.txt/
		// README.txt, LICENSE.txt (custom-license pubs) and the gallery. Normally
		// these already live in the outer and are preserved untouched; this set is
		// what lets a *missing or incomplete* outer be reassembled (from the copies
		// publish left in the version base dir, or — if even those are gone —
		// regenerated to match the original packager). See outerMetadata().
		$metadata = $this->outerMetadata($versionId, $version, $base, $name);

		// A multiZip publication with >1 primary file is served as a nested
		// bundle.zip; a single primary file (or a non-multiZip publication) is
		// served with its files flat in the outer. Match whichever layout the
		// version already uses (default to nested for a fresh multi-file build).
		if ($this->servedLayout($outerPath, $primary, $allFiles) === 'flat')
		{
			$log(sprintf('Rebuilding %s with %d file(s), flat...', basename($outerPath), count($allFiles)));
			$res = $this->buildOuterFlat($allFiles, $outerPath, $name, $this->hasNonFileAttachments($versionId), $metadata);
			if (!$res['ok'])
			{
				return $fail($res['error']);
			}

			return array(
				'ok' => true, 'file' => $outerPath, 'size' => filesize($outerPath),
				'source_hash' => $sourceHash, 'inner' => null, 'error' => null
			);
		}

		// --- nested: inner bundle (system zip) ---------------------------
		// Match the inner's name to the one the served outer already nests: the
		// original packager names it from a per-element param (not always
		// "bundle.zip"), so a rebuild replaces the served inner in place rather
		// than add a second one. Fresh builds default to bundle.zip.
		$innerName = (is_file($outerPath) ? $this->servedInnerName($outerPath) : null) ?: 'bundle.zip';
		$innerPath = $bundleDir . DS . $innerName;

		$log(sprintf('Building %s from %d primary file(s)...', $innerName, count($primary)));
		$res = $this->buildInner($primary, $innerPath, $level, $content);
		if (!$res['ok'])
		{
			return $fail($res['error']);
		}
		$log(sprintf('%s: %d bytes', $innerName, filesize($innerPath)));

		// --- served outer <DOI>.zip --------------------------------------
		// Supporting (role >= 2) files sit flat in the outer alongside the nested
		// inner. They are already there in an existing outer (and preserved); the
		// map lets a reassembled outer carry them too.
		$supporting = array_diff_key($allFiles, $primary);
		$res = $this->buildOuter($innerPath, $outerPath, $name, $supporting, $metadata);
		if (!$res['ok'])
		{
			return $fail($res['error']);
		}

		// A bundle.zip left by an earlier hardcoded-name build is now a stray
		// duplicate (the served inner has a custom name); remove it.
		if ($innerName !== 'bundle.zip')
		{
			@unlink($bundleDir . DS . 'bundle.zip');
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
	 * The current source signature for a version (resolves its primary files
	 * and hashes them). Used at serve time to detect that a ready bundle is
	 * stale and needs rebuilding.
	 *
	 * @param   integer  $versionId
	 * @return  string   the hash, or '' if the version/files can't be resolved
	 */
	public function currentSourceHash($versionId)
	{
		$version = $this->versionRow((int) $versionId);
		if (!$version)
		{
			return '';
		}

		$webpath = trim(\Component::params('com_publications')->get('webpath', '/site/publications'), '/');
		$content = PATH_APP . DS . $webpath . DS . Str::pad((int) $version['publication_id'])
			. DS . Str::pad((int) $versionId) . DS . $version['secret'];

		$files = $this->primaryFiles((int) $versionId, $content);

		return count($files) ? $this->sourceHash($files) : '';
	}

	/**
	 * Read-only health check of a version's bundle on disk. A true per-file
	 * content check: every primary (role-1) source file must appear in the
	 * bundle (the inner bundle.zip for a multi-file publication, or the outer's
	 * flat data entries for a single-file one) with a byte-exact uncompressed
	 * size. A source .zip the packager expanded is satisfied when all of its
	 * members are present, matched by name + uncompressed size + CRC-32. Every
	 * comparison is read from a zip central directory, so it never extracts and
	 * is cheap enough to run across every publication; using uncompressed sizes
	 * (not the compressed file size) means good compression is never mistaken
	 * for loss.
	 *
	 * With $deep, it additionally reads each present source file and compares its
	 * CRC-32 to the CRC the bundle recorded for that entry — proving the bytes
	 * are identical, not merely the same size. This reads the source files (so
	 * it is much heavier than the default central-directory-only pass) but
	 * catches same-size corruption and post-build content drift.
	 *
	 * Issues: missing_bundle (primary files but no served zip), incomplete:X/N
	 * (only X of N source files are in the bundle — a truncated/partial build a
	 * rebuild would restore), content_mismatch:0/N (none of the current source
	 * files are present though the bundle is full of other data — a post-publish
	 * swap/rename, where a rebuild would replace what's served), outer_missing_data
	 * (inner holds the data but the served outer doesn't carry it),
	 * crc_mismatch:N/M (deep: N present files whose bytes differ from the bundle),
	 * inner_unreadable / outer_unreadable, version_not_found. A publication with
	 * no primary files is not flagged (nothing to bundle). Files in an element
	 * the master type marks includeInPackage=0 are excluded from the source set
	 * (resolveFiles), matching the packager, so they are not mistaken as missing.
	 *
	 * Returns: version_id, publication_id, doi, primary_count, source_bytes,
	 * outer_exists, outer_size, inner_exists, inner_entries, outer_has_bundle,
	 * source_hash, issues[] (machine-readable), ok.
	 *
	 * @param   integer  $versionId
	 * @param   boolean  $deep  also CRC-verify each present file against the bundle
	 * @return  array
	 */
	public function audit($versionId, $deep = false)
	{
		$versionId = (int) $versionId;

		$r = array(
			'version_id' => $versionId, 'publication_id' => null, 'doi' => null,
			'primary_count' => 0, 'outer_exists' => false, 'outer_size' => 0,
			'inner_exists' => false, 'inner_entries' => null, 'outer_has_bundle' => null,
			'source_hash' => '', 'issues' => array(), 'ok' => true,
		);

		$version = $this->versionRow($versionId);
		if (!$version)
		{
			$r['issues'][] = 'version_not_found';
			$r['ok'] = false;
			return $r;
		}

		$r['publication_id'] = (int) $version['publication_id'];
		$r['doi']            = $version['doi'];

		$webpath = trim(\Component::params('com_publications')->get('webpath', '/site/publications'), '/');
		$base    = PATH_APP . DS . $webpath . DS . Str::pad((int) $version['publication_id']) . DS . Str::pad($versionId);
		$content = $base . DS . $version['secret'];
		$name    = $this->bundleName($version);
		$outer   = $base . DS . $name . '.zip';

		// Inner bundle: the one the served outer actually nests (its name varies
		// — bundle.zip by default, but a custom primary-element title produces
		// <title>.zip). Read that exact standalone inner, not whatever a glob
		// happens to sort first.
		$innerName = $this->servedInnerName($outer);
		$inner     = ($innerName !== null && is_file($base . DS . 'bundles' . DS . $innerName))
			? $base . DS . 'bundles' . DS . $innerName
			: null;

		// Source files keyed by in-archive name (path relative to the content
		// dir) with uncompressed sizes — the truth a complete bundle reproduces.
		$files               = $this->primaryFiles($versionId, $content);
		$r['primary_count']  = count($files);
		$r['source_hash']    = $r['primary_count'] ? $this->sourceHash($files) : '';
		$r['source_bytes']   = 0;
		$srcSizes            = array();
		foreach ($files as $abs => $name)
		{
			$sz = (int) @filesize($abs);
			$srcSizes[$abs]     = $sz;
			$r['source_bytes'] += $sz;
		}

		$r['outer_exists']   = is_file($outer);
		$r['outer_size']     = $r['outer_exists'] ? filesize($outer) : 0;
		$r['inner_exists']   = ($inner !== null);

		// Inner bundle.zip entries: in-archive name => uncompressed size.
		$innerMap = null;
		if ($r['inner_exists'])
		{
			$zip = new ZipArchive();
			if ($zip->open($inner) === true)
			{
				$innerMap = array();
				for ($i = 0; $i < $zip->numFiles; $i++)
				{
					$s = $zip->statIndex($i);
					if ($s === false || substr($s['name'], -1) === '/')
					{
						continue;
					}
					$innerMap[$s['name']] = array('size' => (int) $s['size'], 'crc' => $s['crc']);
				}
				$r['inner_entries'] = count($innerMap);
				$zip->close();
			}
			else
			{
				$r['issues'][] = 'inner_unreadable';
			}
		}

		// Served outer: collect its data entries (name minus the "<name>/"
		// prefix and the gallery/README/LICENSE/hash metadata) with sizes, so a
		// flat single-file layout can be verified by size too. outer_has_bundle
		// is simply "does it carry any data entry at all".
		$outerData = null;
		if ($r['outer_exists'])
		{
			$zip = new ZipArchive();
			if ($zip->open($outer) === true)
			{
				$outerData = array();
				for ($i = 0; $i < $zip->numFiles; $i++)
				{
					$s = $zip->statIndex($i);
					if ($s === false) { continue; }
					$rel = preg_replace('#^[^/]+/#', '', (string) $s['name']); // strip "<name>/"
					if ($rel === '' || substr($rel, -1) === '/'
						|| strpos($rel, 'gallery/') === 0
						|| substr($rel, -5) === '.hash'
						|| in_array($rel, array('hubREADME.txt', 'LICENSE.txt'), true))
					{
						continue;
					}
					$outerData[$rel] = array('size' => (int) $s['size'], 'crc' => $s['crc']);
				}
				$r['outer_has_bundle'] = !empty($outerData);
				$zip->close();
			}
			else
			{
				$r['issues'][] = 'outer_unreadable';
			}
		}

		// === Completeness: a true per-file content check (no size ratio) ===
		// Every primary source file's data must actually be in the bundle. A
		// file is satisfied when an entry with its name and a byte-exact
		// uncompressed size is present (matched at its path, or by basename for
		// older bundles whose path convention drifted). A source .zip the
		// packager expanded is instead satisfied when every one of its members
		// is present — matched by name + uncompressed size + CRC-32, all read
		// from the two zip central directories, so re-compression is irrelevant
		// and nothing is extracted. This counts real data loss exactly and is
		// not fooled by good compression, an expanded archive, or a swapped file.
		if ($r['primary_count'] == 0)
		{
			// No primary data files: nothing to bundle, so not a build failure.
		}
		else if (!$r['outer_exists'])
		{
			$r['issues'][] = 'missing_bundle';
		}
		else
		{
			$carrier   = ($innerMap !== null) ? $innerMap : $outerData;
			$usedInner = ($innerMap !== null);

			if ($carrier !== null) // else *_unreadable already fired
			{
				$byBase = array();
				foreach ($carrier as $n => $m)
				{
					$byBase[basename($n)][] = $m;
				}

				$has = function ($name, $size, $crc) use ($carrier, $byBase)
				{
					if (isset($carrier[$name]) && $carrier[$name]['size'] === $size
						&& ($crc === null || $carrier[$name]['crc'] === $crc))
					{
						return true;
					}
					if (isset($byBase[basename($name)]))
					{
						foreach ($byBase[basename($name)] as $m)
						{
							if ($m['size'] === $size && ($crc === null || $m['crc'] === $crc))
							{
								return true;
							}
						}
					}
					return false;
				};

				$missing = 0;
				$crcBad  = 0;
				foreach ($files as $abs => $name)
				{
					$size = isset($srcSizes[$abs]) ? $srcSizes[$abs] : (int) @filesize($abs);

					// Present verbatim (a plain file, or an archive stored as-is)?
					if ($has($name, $size, null))
					{
						// Deep: also confirm the bytes themselves match — the
						// source file's CRC-32 vs the CRC the bundle recorded for
						// that entry — catching same-size corruption or a
						// post-build content change the size check can't see.
						if ($deep)
						{
							$c = @hash_file('crc32b', $abs);
							if ($c === false || !$has($name, $size, hexdec($c)))
							{
								$crcBad++;
							}
						}
						continue;
					}

					// A source .zip the packager expanded into the bundle: every
					// member must be present (name + size + CRC, both central dirs).
					if (preg_match('/\.zip$/i', $name))
					{
						$members = $this->zipMembers($abs);
						if ($members)
						{
							$allIn = true;
							foreach ($members as $mn => $mm)
							{
								if (!$has($mn, $mm['size'], $mm['crc']))
								{
									$allIn = false;
									break;
								}
							}
							if ($allIn)
							{
								continue;
							}
						}
					}

					$missing++;
				}

				if ($missing > 0)
				{
					$present = $r['primary_count'] - $missing;

					if ($present === 0 && !empty($carrier))
					{
						// None of the current source files are in the bundle,
						// yet it is full of other data — the content was swapped
						// or renamed after publish. A rebuild would *replace*
						// what's served, so flag it distinctly from a truncation.
						$r['issues'][] = 'content_mismatch:0/' . $r['primary_count'];
					}
					else
					{
						// A truncated/partial build: some (or no) source files
						// present, the rest missing. A rebuild restores them.
						$r['issues'][] = 'incomplete:' . $present . '/' . $r['primary_count'];
					}
				}
				else if ($usedInner && $r['outer_has_bundle'] === false)
				{
					// Inner holds the data, but the served outer doesn't carry
					// it — the download would be metadata-only.
					$r['issues'][] = 'outer_missing_data';
				}

				// Deep mode: source bytes present but not byte-identical to what
				// the bundle holds (corruption, or content changed post-build).
				if ($crcBad > 0)
				{
					$r['issues'][] = 'crc_mismatch:' . $crcBad . '/' . $r['primary_count'];
				}
			}
		}

		$r['ok'] = empty($r['issues']);

		return $r;
	}

	/**
	 * The set of element ids (manifest element keys) that the master type marks
	 * includeInPackage=0 — i.e. file elements deliberately excluded from the
	 * download. Attachments link to elements via element_id, and that id is the
	 * element's key in the master type's curation manifest (verified: it lines
	 * up with the element role), so the flag is reachable without the curation
	 * model. resolveFiles() drops attachments of these elements, exactly as the
	 * original packager skips such elements. Cached per version; empty (the case
	 * today on PURR, where the flag is unused) means no files are dropped.
	 *
	 * @param   integer  $versionId
	 * @return  array  element_id => true
	 */
	protected function includeInPackageExcludedIds($versionId)
	{
		$versionId = (int) $versionId;
		if (isset($this->incExcludedCache[$versionId]))
		{
			return $this->incExcludedCache[$versionId];
		}

		$db = \App::get('db');
		$db->setQuery(
			"SELECT mt.`curation`
			 FROM `#__publication_versions` v
			 JOIN `#__publications` p ON p.`id` = v.`publication_id`
			 JOIN `#__publication_master_types` mt ON mt.`id` = p.`master_type`
			 WHERE v.`id` = " . $versionId . " LIMIT 1"
		);
		$curation = (string) $db->loadResult();

		$excluded = array();
		// Fast out: the flag isn't mentioned at all (the norm on PURR today).
		if ($curation !== '' && stripos($curation, 'includeInPackage') !== false)
		{
			$man = json_decode($curation, true);
			if (is_array($man) && !empty($man['blocks']) && is_array($man['blocks']))
			{
				foreach ($man['blocks'] as $blk)
				{
					if (empty($blk['elements']) || !is_array($blk['elements']))
					{
						continue;
					}
					foreach ($blk['elements'] as $ek => $el)
					{
						$params = isset($el['params']) ? $el['params'] : null;
						if (is_string($params))
						{
							$params = json_decode($params, true);
						}
						if (!is_array($params))
						{
							continue;
						}
						// Only file/attachment elements carry file attachments.
						$isAttach = (isset($params['type']) && $params['type'] === 'file')
							|| (isset($el['type']) && $el['type'] === 'attachment');
						if (!$isAttach)
						{
							continue;
						}
						$tp = isset($params['typeParams']) ? $params['typeParams'] : null;
						if (is_string($tp))
						{
							$tp = json_decode($tp, true);
						}
						if (is_array($tp) && isset($tp['includeInPackage']) && (int) $tp['includeInPackage'] === 0)
						{
							$excluded[(int) $ek] = true;
						}
					}
				}
			}
		}

		return $this->incExcludedCache[$versionId] = $excluded;
	}

	/**
	 * Does the version have any non-file element attachments (data / publication
	 * / tool)? Their content is bundled by the original packager but not by this
	 * builder, so a flat rebuild must preserve rather than drop it.
	 *
	 * @param   integer  $versionId
	 * @return  boolean
	 */
	protected function hasNonFileAttachments($versionId)
	{
		$db = \App::get('db');
		$db->setQuery(
			"SELECT COUNT(*) FROM `#__publication_attachments`
			 WHERE `publication_version_id` = " . (int) $versionId . " AND `type` != 'file'"
		);

		return (int) $db->loadResult() > 0;
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
			"SELECT `publication_id`, `secret`, `version_number`, `doi`,
			        `title`, `version_label`, `license_type`, `license_text`
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
		return $this->resolveFiles($versionId, $content, 1);
	}

	/**
	 * Resolve all of a version's file attachments (every role) to abs => name.
	 * A flat outer carries this full set (primary + supporting), the way the
	 * original packager assembles it.
	 *
	 * @param   integer  $versionId
	 * @param   string   $content
	 * @return  array
	 */
	protected function attachmentFiles($versionId, $content)
	{
		return $this->resolveFiles($versionId, $content, null);
	}

	/**
	 * Resolve file attachments to abs => name-relative-to-$content (with "./"
	 * stripped — exactly how the original packager names entries, so a rebuild
	 * preserves the same directory structure).
	 *
	 * @param   integer       $versionId
	 * @param   string        $content
	 * @param   integer|null  $role   role to filter on, or null for all roles
	 * @return  array
	 */
	protected function resolveFiles($versionId, $content, $role = null)
	{
		// Resolve the includeInPackage-excluded element ids FIRST: it issues its
		// own query, which would otherwise clobber the pending attachments query
		// below before loadObjectList() reads it (leaving the rows without the
		// path/element_id columns, so nothing resolves).
		$excluded = $this->includeInPackageExcludedIds($versionId);

		$db = \App::get('db');
		$db->setQuery(
			"SELECT `path`, `id`, `params`, `element_id` FROM `#__publication_attachments`
			 WHERE `publication_version_id` = " . (int) $versionId . "
			   AND `type` = 'file'" . ($role !== null ? " AND `role` = " . (int) $role : "") . "
			 ORDER BY `ordering`, `id`"
		);
		$rows = $db->loadObjectList();

		$files = array();
		foreach ((array) $rows as $row)
		{
			// Files of an element marked includeInPackage=0 are excluded from
			// the package by the original packager — drop them here too.
			if (isset($excluded[(int) $row->element_id]))
			{
				continue;
			}

			$rel = str_replace('./', '', ltrim(str_replace('\\', '/', (string) $row->path), '/'));
			if ($rel === '')
			{
				continue;
			}

			// The on-disk name depends on the element's dirHierarchy and a
			// per-file "suffix" param (attachments/file.php::getFilePath). Try
			// the same candidates the original packager would, in priority
			// order, and key by whichever actually exists — so a rebuild
			// reproduces the original's contents and entry names.
			$name = $this->resolveName($content, $rel, (int) $row->id, (string) $row->params);
			if ($name !== null)
			{
				$files[$content . DS . $name] = $name;
			}
		}

		return $files;
	}

	/**
	 * The on-disk relative name of an attachment, mirroring getFilePath():
	 * dirHierarchy 1 keeps the path, 2 flattens to the basename, 0 appends
	 * "-<id>"; a "suffix" param inserts " (<suffix>)" before the extension.
	 * Returns the first candidate that exists, or null if none do (the file is
	 * genuinely missing — the original packager would miss it too).
	 *
	 * @param   string   $content
	 * @param   string   $rel     cleaned path relative to content
	 * @param   integer  $id      attachment id
	 * @param   string   $params  attachment params (JSON)
	 * @return  string|null
	 */
	protected function resolveName($content, $rel, $id, $params)
	{
		$base   = basename($rel);
		$suffix = $this->fileSuffix($params);

		$cands = array($rel);                                            // dirHierarchy=1
		if ($suffix !== '')
		{
			$cands[] = $this->injectBeforeExt($rel, ' (' . $suffix . ')');   // dirHierarchy=1 + suffix
			$cands[] = $this->injectBeforeExt($base, ' (' . $suffix . ')');  // dirHierarchy=2 + suffix
		}
		$cands[] = $base;                                                // dirHierarchy=2
		$cands[] = $this->injectBeforeExt($base, '-' . $id);             // dirHierarchy=0

		foreach ($cands as $c)
		{
			if ($c !== '' && is_file($content . DS . $c))
			{
				return $c;
			}
		}

		return null;
	}

	/**
	 * The attachment's "suffix" param (used by getFilePath to disambiguate
	 * same-named files), or ''. Parsed via Hubzero\Config\Registry exactly as
	 * getFilePath does, so it handles the stored format ("suffix=1", JSON, …).
	 *
	 * @param   string  $params
	 * @return  string
	 */
	protected function fileSuffix($params)
	{
		if ($params === '')
		{
			return '';
		}
		try
		{
			$s = (new \Hubzero\Config\Registry($params))->get('suffix');
		}
		catch (\Exception $e)
		{
			return '';
		}

		return ($s === null || $s === '') ? '' : (string) $s;
	}

	/**
	 * Insert a string before a filename's last extension, mirroring
	 * Html::fixFileName (e.g. "dir/a.b.c" + " (1)" => "dir/a.b (1).c").
	 *
	 * @param   string  $path
	 * @param   string  $insert
	 * @return  string
	 */
	protected function injectBeforeExt($path, $insert)
	{
		$slash = strrpos($path, '/');
		$dir   = ($slash !== false) ? substr($path, 0, $slash + 1) : '';
		$file  = ($slash !== false) ? substr($path, $slash + 1) : $path;
		$dot   = strrpos($file, '.');
		$file  = ($dot === false) ? $file . $insert : substr($file, 0, $dot) . $insert . substr($file, $dot);

		return $dir . $file;
	}

	/**
	 * Which layout the served outer uses: 'nested' (a generated bundle .zip
	 * holding the primaries) or 'flat' (files placed directly in the outer). A
	 * single primary file is always flat. Otherwise match the existing outer:
	 * a generated bundle — a top-level .zip that is NOT one of the data files —
	 * means nested (this is the reliable signal, and survives a file that is
	 * both a primary and a flat supporting copy); failing that, a primary file
	 * sitting directly in the outer means flat; otherwise default to nested (a
	 * fresh multi-file build, the multiZip norm).
	 *
	 * @param   string  $outerPath
	 * @param   array   $primary   abs => rel  (role-1 files)
	 * @param   array   $allFiles  abs => rel  (all file attachments)
	 * @return  string  'flat' | 'nested'
	 */
	protected function servedLayout($outerPath, $primary, $allFiles)
	{
		if (count($primary) <= 1)
		{
			return 'flat';
		}
		if (!is_file($outerPath))
		{
			return 'nested';
		}

		$zip = new ZipArchive();
		if ($zip->open($outerPath) !== true)
		{
			return 'nested';
		}

		$primaryRel = array_flip(array_values($primary));
		$attBase    = array();
		foreach ($allFiles as $rel)
		{
			$attBase[basename($rel)] = true;
		}

		$flatPrimary = false;
		for ($i = 0; $i < $zip->numFiles; $i++)
		{
			$rel = preg_replace('#^[^/]+/#', '', (string) $zip->getNameIndex($i)); // strip "<dir>/"
			if ($rel === '' || substr($rel, -1) === '/')
			{
				continue;
			}
			// A generated bundle (top-level .zip that isn't one of the data
			// files) is the definitive nested signal.
			if (substr($rel, -4) === '.zip' && strpos($rel, '/') === false && !isset($attBase[basename($rel)]))
			{
				$zip->close();
				return 'nested';
			}
			if (isset($primaryRel[$rel]))
			{
				$flatPrimary = true; // a primary file sits directly in the outer
			}
		}
		$zip->close();

		return $flatPrimary ? 'flat' : 'nested';
	}

	/**
	 * The inner bundle's name as the served outer nests it. The original
	 * packager names the inner from a per-element param (e.g. <title>.zip), not
	 * always "bundle.zip", so build/audit must use the real name. Returns the
	 * basename of the single data .zip directly under the outer's top dir;
	 * prefers a non-"bundle.zip" name when more than one is present (so it
	 * recovers the original even if a stray bundle.zip was added). Returns null
	 * when the outer is missing/unreadable or nests no inner zip (i.e. it serves
	 * its files flat) — callers must NOT fall back to a stray standalone.
	 *
	 * @param   string  $outerPath
	 * @return  string
	 */
	protected function servedInnerName($outerPath)
	{
		if (!is_file($outerPath))
		{
			return null;
		}

		$zip = new ZipArchive();
		if ($zip->open($outerPath) !== true)
		{
			return null;
		}

		$cands = array();
		for ($i = 0; $i < $zip->numFiles; $i++)
		{
			$rel = preg_replace('#^[^/]+/#', '', (string) $zip->getNameIndex($i)); // strip "<dir>/"
			if (substr($rel, -4) === '.zip' && strpos($rel, '/') === false)
			{
				$cands[] = $rel;
			}
		}
		$zip->close();

		if (!$cands)
		{
			// The outer serves its files flat — there is no nested inner. (Do
			// not fall back to a stray bundles/bundle.zip that isn't served.)
			return null;
		}
		foreach ($cands as $c)
		{
			if ($c !== 'bundle.zip')
			{
				return $c; // the original custom-named inner
			}
		}

		return 'bundle.zip';
	}

	/**
	 * Read a zip's members as name => array(size, crc) from its central
	 * directory (no extraction). Used to verify that a source archive the
	 * packager expanded is fully present in the bundle. Returns null if the
	 * file isn't a readable zip.
	 *
	 * @param   string  $path
	 * @return  array|null
	 */
	protected function zipMembers($path)
	{
		if (!is_file($path))
		{
			return null;
		}

		$zip = new ZipArchive();
		if ($zip->open($path) !== true)
		{
			return null;
		}

		$members = array();
		for ($i = 0; $i < $zip->numFiles; $i++)
		{
			$s = $zip->statIndex($i);
			if ($s === false || substr($s['name'], -1) === '/')
			{
				continue;
			}
			$members[$s['name']] = array('size' => (int) $s['size'], 'crc' => $s['crc']);
		}
		$zip->close();

		return $members;
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
	 * @param   array    $files    abs => name-relative-to-$content
	 * @param   string   $innerPath
	 * @param   integer  $level
	 * @param   string   $content  the content dir the names are relative to
	 * @return  array    ok, error
	 */
	protected function buildInner($files, $innerPath, $level, $content)
	{
		if (!$this->systemZipAvailable())
		{
			return array('ok' => false, 'error' => 'The system "zip" binary is not available.');
		}

		$tmp = $innerPath . '.build.' . getmypid();
		@unlink($tmp);

		// Run from the content dir and feed the relative names on stdin (-@) so
		// paths are preserved (no -j), arbitrary names can't be misread as
		// options, and there is no ARG_MAX ceiling on large datasets.
		$cmd = array('zip', '-' . max(0, min(9, (int) $level)), '-X', '-q',
			'-n', '.' . implode(':.', self::compressedExtList()), $tmp, '-@');

		$rc = $this->runQuiet($cmd, $content, implode("\n", array_values($files)) . "\n");

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
	 * Assemble the served <name>.zip by adding the (already built) inner bundle
	 * as a stored <name>/<inner> entry to the gallery+README outer (replacing any
	 * prior entry of that name, and dropping a stray bundle.zip from an earlier
	 * hardcoded-name build). Atomic via a temp copy + rename.
	 *
	 * An existing outer is updated in place and its metadata preserved untouched.
	 * If the outer is *missing* (a build that timed out before package() could
	 * write it — large datasets hit the same guard async exists to bypass) it is
	 * assembled from scratch: the inner, the supporting (role >= 2) files, and the
	 * publish-time metadata (README/LICENSE/gallery). $metadata/$supporting are
	 * also used to backfill any of those that a present-but-incomplete outer lost.
	 *
	 * @param   string  $innerPath
	 * @param   string  $outerPath
	 * @param   string  $name
	 * @param   array   $supporting  abs => name-relative-to-content (role >= 2)
	 * @param   array   $metadata    abs => array(kind, entry)  (readme/license/gallery)
	 * @return  array   ok, error
	 */
	protected function buildOuter($innerPath, $outerPath, $name, $supporting = array(), $metadata = array())
	{
		$entry = $name . '/' . basename($innerPath);
		$tmp   = $outerPath . '.build.' . getmypid();
		@unlink($tmp);

		$exists = is_file($outerPath);

		$zip = new ZipArchive();
		if ($exists)
		{
			if (!copy($outerPath, $tmp))
			{
				return array('ok' => false, 'error' => 'Could not copy ' . $outerPath);
			}
			if ($zip->open($tmp) !== true)
			{
				@unlink($tmp);
				return array('ok' => false, 'error' => 'Could not open ' . $tmp . ' for update.');
			}
		}
		else if ($zip->open($tmp, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true)
		{
			@unlink($tmp);
			return array('ok' => false, 'error' => 'Could not create ' . $tmp . '.');
		}

		if ($zip->locateName($entry) !== false)
		{
			$zip->deleteName($entry);
		}

		// Drop a stray <name>/bundle.zip left by an earlier hardcoded-name build
		// so the served outer carries exactly one (correctly named) inner.
		$stray = $name . '/bundle.zip';
		if ($stray !== $entry && $zip->locateName($stray) !== false)
		{
			$zip->deleteName($stray);
		}

		if (!$zip->addFile($innerPath, $entry))
		{
			$zip->close();
			@unlink($tmp);
			return array('ok' => false, 'error' => 'Could not add ' . $entry . ' to the bundle.');
		}
		$zip->setCompressionName($entry, ZipArchive::CM_STORE);

		// Backfill the supporting files and metadata the outer should carry. On a
		// complete existing outer every one is already present, so nothing is
		// added (behaviour unchanged); a fresh or incomplete outer gets them.
		$this->ensureFiles($zip, $name, $supporting);
		$this->ensureMetadata($zip, $name, $metadata);

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
	 * (Re)build a flat served outer: keep its metadata entries (gallery /
	 * README / LICENSE / hash), drop the existing data entries, and add every
	 * current file attachment at <name>/<rel> from disk (matching what
	 * package() assembles for a non-multiZip / single-file publication).
	 * Already-compressed members are stored. Atomic via a temp copy + rename.
	 *
	 * An existing outer is updated in place (metadata preserved); a *missing* one
	 * is assembled from scratch — the flat files plus the publish-time metadata
	 * (README/LICENSE/gallery from $metadata), so a build that timed out before
	 * package() wrote the outer can still be produced off-request.
	 *
	 * @param   array   $files   abs => name-relative-to-content (all roles)
	 * @param   string  $outerPath
	 * @param   string  $name
	 * @param   boolean $preserveExtra
	 * @param   array   $metadata  abs => array(kind, entry)  (readme/license/gallery)
	 * @return  array   ok, error
	 */
	protected function buildOuterFlat($files, $outerPath, $name, $preserveExtra = false, $metadata = array())
	{
		$tmp = $outerPath . '.build.' . getmypid();
		@unlink($tmp);

		$exists = is_file($outerPath);

		$zip = new ZipArchive();
		if ($exists)
		{
			if (!copy($outerPath, $tmp))
			{
				return array('ok' => false, 'error' => 'Could not copy ' . $outerPath);
			}
			if ($zip->open($tmp) !== true)
			{
				@unlink($tmp);
				return array('ok' => false, 'error' => 'Could not open ' . $tmp . ' for update.');
			}
		}
		else if ($zip->open($tmp, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true)
		{
			@unlink($tmp);
			return array('ok' => false, 'error' => 'Could not create ' . $tmp . '.');
		}

		// Record where each existing data entry sits (its path relative to the
		// "<name>/" prefix), so a rebuilt file is placed back at the SAME path —
		// preserving any bundleDirectory/subdir prefix the original used rather
		// than defaulting to <name>/<rel>.
		$existing = array();
		for ($i = 0; $i < $zip->numFiles; $i++)
		{
			$entry = (string) $zip->getNameIndex($i);
			$rel   = preg_replace('#^[^/]+/#', '', $entry);
			if ($rel !== '' && substr($rel, -1) !== '/')
			{
				$existing[$rel] = $entry;
			}
		}

		// Normally drop the existing data entries and re-add the current file
		// attachments (this clears stale/renamed ones). But when the publication
		// also has non-file elements (data/publication/tool) their content lives
		// in the outer too and we cannot reliably tell it from a stale file
		// entry — so in that case keep everything and only refresh the current
		// file attachments below, rather than risk dropping that content.
		// Gallery/README/LICENSE/hash metadata is always kept.
		if (!$preserveExtra)
		{
			$drop = array();
			for ($i = 0; $i < $zip->numFiles; $i++)
			{
				$entry = (string) $zip->getNameIndex($i);
				$rel   = preg_replace('#^[^/]+/#', '', $entry);
				if ($rel === '' || substr($rel, -1) === '/'
					|| $rel === 'README.txt' || $rel === 'LICENSE.txt' || $rel === 'hubREADME.txt'
					|| strpos($rel, 'gallery/') === 0 || substr($rel, -5) === '.hash')
				{
					continue;
				}
				$drop[] = $entry;
			}
			foreach ($drop as $entry)
			{
				$zip->deleteName($entry);
			}
		}

		// Add every current attachment, at the path the original outer used
		// for it (preserving any subdir prefix) or <name>/<rel> if it is new.
		foreach ($files as $abs => $rel)
		{
			$entry = $this->placedEntry($existing, $name, $rel);
			if ($zip->locateName($entry) !== false)
			{
				$zip->deleteName($entry);
			}
			if (!$zip->addFile($abs, $entry))
			{
				$zip->close();
				@unlink($tmp);
				return array('ok' => false, 'error' => 'Could not add ' . $entry);
			}
			$ext = strtolower(pathinfo($rel, PATHINFO_EXTENSION));
			if (in_array($ext, self::compressedExtList(), true))
			{
				$zip->setCompressionName($entry, ZipArchive::CM_STORE);
			}
		}

		// Backfill README/LICENSE/gallery — a no-op on a complete existing outer
		// (they are already present and kept above), the metadata source for a
		// fresh or incomplete one.
		$this->ensureMetadata($zip, $name, $metadata);

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
	 * The outer entry path to use for a rebuilt file: the existing entry at the
	 * same relative path if present, else an existing entry that ends in that
	 * relpath (preserving a bundleDirectory/subdir prefix), else <name>/<rel>
	 * for a file the outer didn't carry before. Matching on the full relpath
	 * (not the basename) avoids collisions between same-named files.
	 *
	 * @param   array   $existing  rel-without-prefix => full entry name
	 * @param   string  $name      the outer's "<DOI>" top dir
	 * @param   string  $rel       the file's relative name
	 * @return  string
	 */
	protected function placedEntry($existing, $name, $rel)
	{
		if (isset($existing[$rel]))
		{
			return $existing[$rel];
		}

		$tail = '/' . $rel;
		foreach ($existing as $er => $full)
		{
			if (strlen($er) > strlen($rel) && substr($er, -strlen($tail)) === $tail)
			{
				return $full;
			}
		}

		return $name . '/' . $rel;
	}

	/**
	 * Add each file to the open archive at <name>/<rel> if it is not already
	 * there. Backfills the supporting (role >= 2) files a reassembled nested
	 * outer must carry; a no-op when the outer already holds them.
	 *
	 * @param   ZipArchive  $zip
	 * @param   string      $name   the outer's "<DOI>" top dir
	 * @param   array       $files  abs => name-relative-to-content
	 * @return  void
	 */
	protected function ensureFiles($zip, $name, $files)
	{
		foreach ((array) $files as $abs => $rel)
		{
			$entry = $name . '/' . $rel;
			if ($zip->locateName($entry) !== false || !is_file($abs))
			{
				continue;
			}
			if ($zip->addFile($abs, $entry))
			{
				$ext = strtolower(pathinfo($rel, PATHINFO_EXTENSION));
				if (in_array($ext, self::compressedExtList(), true))
				{
					$zip->setCompressionName($entry, ZipArchive::CM_STORE);
				}
			}
		}
	}

	/**
	 * Ensure the archive carries the publish-time metadata (README/LICENSE/
	 * gallery). Each item is added only if its KIND is absent — a readme of
	 * either name (README.txt / hubREADME.txt), a LICENSE.txt, or any gallery
	 * image — so a complete outer is left exactly as-is while a fresh or
	 * incomplete one is filled in without ever duplicating what's there.
	 *
	 * @param   ZipArchive  $zip
	 * @param   string      $name      the outer's "<DOI>" top dir
	 * @param   array       $metadata  abs => array(kind, entry)
	 * @return  void
	 */
	protected function ensureMetadata($zip, $name, $metadata)
	{
		if (empty($metadata))
		{
			return;
		}

		$hasReadme  = ($zip->locateName($name . '/README.txt') !== false)
				   || ($zip->locateName($name . '/hubREADME.txt') !== false);
		$hasLicense = ($zip->locateName($name . '/LICENSE.txt') !== false);

		$hasGallery = false;
		$gpfx       = $name . '/gallery/';
		for ($i = 0; $i < $zip->numFiles; $i++)
		{
			if (strpos((string) $zip->getNameIndex($i), $gpfx) === 0)
			{
				$hasGallery = true;
				break;
			}
		}

		foreach ($metadata as $abs => $info)
		{
			list($kind, $entry) = $info;

			if (($kind === 'readme' && $hasReadme)
				|| ($kind === 'license' && $hasLicense)
				|| ($kind === 'gallery' && $hasGallery))
			{
				continue;
			}
			if ($zip->locateName($entry) !== false || !is_file($abs))
			{
				continue;
			}
			if ($zip->addFile($abs, $entry))
			{
				$ext = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
				if (in_array($ext, self::compressedExtList(), true))
				{
					$zip->setCompressionName($entry, ZipArchive::CM_STORE);
				}
			}
		}
	}

	/**
	 * The metadata the served outer carries besides its data — the README
	 * (hubREADME.txt or README.txt), LICENSE.txt (custom-license pubs only) and
	 * the gallery — as abs => array(kind, entry) for ensureMetadata().
	 *
	 * Normally these already live in the outer and are preserved; this set is
	 * what lets a missing or incomplete outer be reassembled. README/LICENSE are
	 * taken from the copies publish left in the version base dir. If no README
	 * survives there at all (a version that never packaged), it is regenerated to
	 * match the original packager and written back to the base dir; LICENSE.txt
	 * is likewise regenerated only when the version carries custom license text,
	 * exactly the condition under which package() writes it. The gallery lives at
	 * <base>/gallery and is placed at <name>/gallery/<file>, as package() does.
	 *
	 * @param   integer  $versionId
	 * @param   array    $version
	 * @param   string   $base   the version base dir
	 * @param   string   $name   the outer's "<DOI>" top dir
	 * @return  array
	 */
	protected function outerMetadata($versionId, $version, $base, $name)
	{
		$meta = array();

		// README — prefer the on-disk copy under its real name; regenerate to
		// hubREADME.txt only when neither name is present.
		$readme = null;
		foreach (array('hubREADME.txt', 'README.txt') as $rn)
		{
			if (is_file($base . DS . $rn))
			{
				$readme = $rn;
				break;
			}
		}
		if ($readme === null)
		{
			$text = $this->generateReadme($versionId, $version);
			if ($text !== '' && @file_put_contents($base . DS . 'hubREADME.txt', $text) !== false)
			{
				@chgrp($base . DS . 'hubREADME.txt', 'access-content');
				@chmod($base . DS . 'hubREADME.txt', 0664);
				$readme = 'hubREADME.txt';
			}
		}
		if ($readme !== null)
		{
			$meta[$base . DS . $readme] = array('readme', $name . '/' . $readme);
		}

		// LICENSE.txt — on disk if present; else regenerate only when the version
		// carries custom license text (the only case package() writes one).
		if (is_file($base . DS . 'LICENSE.txt'))
		{
			$meta[$base . DS . 'LICENSE.txt'] = array('license', $name . '/LICENSE.txt');
		}
		else
		{
			$licText = isset($version['license_text']) ? trim((string) $version['license_text']) : '';
			if ($licText !== '' && @file_put_contents($base . DS . 'LICENSE.txt', $licText) !== false)
			{
				@chgrp($base . DS . 'LICENSE.txt', 'access-content');
				@chmod($base . DS . 'LICENSE.txt', 0664);
				$meta[$base . DS . 'LICENSE.txt'] = array('license', $name . '/LICENSE.txt');
			}
		}

		// Gallery images (publish stores them at <base>/gallery).
		foreach ((array) glob($base . DS . 'gallery' . DS . '*') as $img)
		{
			if (is_file($img))
			{
				$meta[$img] = array('gallery', $name . '/gallery/' . basename($img));
			}
		}

		return $meta;
	}

	/**
	 * Regenerate a version's hubREADME.txt text, mirroring curation::package().
	 * Last resort only — used when no README survives in the base dir. The header
	 * (title, version, authors, DOI, license title + text) is reproduced exactly;
	 * the materials listing is reconstructed from the version's file attachments
	 * grouped by their element's role label.
	 *
	 * @param   integer  $versionId
	 * @param   array    $version
	 * @return  string
	 */
	protected function generateReadme($versionId, $version)
	{
		$db = \App::get('db');

		$title   = isset($version['title']) ? (string) $version['title'] : '';
		$vlabel  = isset($version['version_label']) ? (string) $version['version_label'] : '';
		$doi     = isset($version['doi']) ? trim((string) $version['doi']) : '';
		$licText = isset($version['license_text']) ? (string) $version['license_text'] : '';
		$lt      = isset($version['license_type']) ? (int) $version['license_type'] : 0;

		$readme  = $title . "\n ";
		$readme .= 'Version ' . $vlabel . "\n ";

		$authors = $this->versionAuthors($versionId);
		if ($authors)
		{
			$readme .= 'Authors: ' . "\n ";
			foreach ($authors as $a)
			{
				$readme .= $a['name'];
				if ($a['org'] !== '')
				{
					$readme .= ', ' . $a['org'];
				}
				$readme .= "\n ";
			}
		}

		if ($doi !== '')
		{
			$readme .= 'doi:' . $doi . "\n ";
		}

		// License title + text (custom text wins, else the license's own text) —
		// and note whether a LICENSE.txt will accompany it (custom text only).
		$hasLicFile = false;
		if ($lt)
		{
			$db->setQuery("SELECT `title`, `text` FROM `#__publication_licenses` WHERE `id` = " . $lt . " LIMIT 1");
			$L = $db->loadAssoc();
			if ($L && isset($L['title']) && $L['title'] !== '')
			{
				$readme .= "\n " . "\n ";
				$readme .= 'License: ' . "\n ";
				$readme .= $L['title'] . "\n ";

				if (trim($licText) !== '')
				{
					$readme .= $licText . "\n ";
					$hasLicFile = true;
				}
				else if (trim((string) $L['text']) !== '')
				{
					$readme .= $L['text'] . "\n ";
				}
			}
		}

		$readme .= "\n ";
		$readme .= '#####################################' . "\n ";
		$readme .= 'Included Publication Materials:' . "\n ";
		$readme .= '#####################################' . "\n ";

		$readme .= $this->materialsListing($versionId);

		if ($hasLicFile)
		{
			$readme .= "\n" . 'License File: ' . "\n";
			$readme .= '>>> LICENSE.txt' . "\n";
		}

		$readme .= "\n" . 'Archival Info:' . "\n";
		$readme .= '>>> hubREADME.txt' . "\n";
		$readme .= "\n ";
		$readme .= "\n ";
		$readme .= '--------------------------------------------' . "\n ";

		try
		{
			$when = \Date::toSql();
		}
		catch (\Exception $e)
		{
			$when = gmdate('Y-m-d H:i:s');
		}
		$readme .= 'Archival package produced ' . $when;

		return $readme;
	}

	/**
	 * The "Included Publication Materials" body for a regenerated README: the
	 * version's file attachments grouped by their element's role label, listed as
	 * ">>> <path>" — a nested multi-file primary is shown as ">>> bundle.zip", a
	 * gallery image under gallery/, mirroring how package()/bundleItems lists them.
	 *
	 * @param   integer  $versionId
	 * @return  string
	 */
	protected function materialsListing($versionId)
	{
		$elemByRole = $this->fileElementsByRole($versionId);

		$db = \App::get('db');
		$db->setQuery(
			"SELECT `path`, `role` FROM `#__publication_attachments`
			 WHERE `publication_version_id` = " . (int) $versionId . " AND `type` = 'file'
			 ORDER BY `role`, `ordering`, `id`"
		);

		$byRole = array();
		foreach ((array) $db->loadObjectList() as $a)
		{
			$byRole[(int) $a->role][] = (string) $a->path;
		}

		$out = '';
		foreach ($byRole as $role => $paths)
		{
			$label    = isset($elemByRole[$role]['label']) && $elemByRole[$role]['label'] !== ''
					  ? $elemByRole[$role]['label'] : ('Role ' . $role);
			$multiZip = isset($elemByRole[$role]['multiZip']) ? (int) $elemByRole[$role]['multiZip'] : 1;

			$out .= "\n" . $label . ': ' . "\n";

			if ($role == 1 && $multiZip == 1 && count($paths) > 1)
			{
				$out .= '>>> bundle.zip' . "\n";
				continue;
			}
			foreach ($paths as $p)
			{
				$rel = str_replace('./', '', ltrim(str_replace('\\', '/', $p), '/'));
				$out .= '>>> ' . ($role == 3 ? 'gallery/' . basename($rel) : $rel) . "\n";
			}
		}

		return $out;
	}

	/**
	 * Map role => array(label, multiZip) for the master type's file/attachment
	 * elements (first element of each role wins). Used to label the regenerated
	 * README's materials listing.
	 *
	 * @param   integer  $versionId
	 * @return  array
	 */
	protected function fileElementsByRole($versionId)
	{
		$db = \App::get('db');
		$db->setQuery(
			"SELECT mt.`curation`
			 FROM `#__publication_versions` v
			 JOIN `#__publications` p ON p.`id` = v.`publication_id`
			 JOIN `#__publication_master_types` mt ON mt.`id` = p.`master_type`
			 WHERE v.`id` = " . (int) $versionId . " LIMIT 1"
		);
		$man = json_decode((string) $db->loadResult(), true);

		$byRole = array();
		if (is_array($man) && !empty($man['blocks']) && is_array($man['blocks']))
		{
			foreach ($man['blocks'] as $blk)
			{
				if (empty($blk['elements']) || !is_array($blk['elements']))
				{
					continue;
				}
				foreach ($blk['elements'] as $el)
				{
					$params = isset($el['params']) ? $el['params'] : null;
					if (is_string($params))
					{
						$params = json_decode($params, true);
					}
					if (!is_array($params))
					{
						continue;
					}
					$type = isset($params['type']) ? $params['type'] : (isset($el['type']) ? $el['type'] : '');
					if ($type !== 'file' && $type !== 'attachment')
					{
						continue;
					}
					$role = isset($params['role']) ? (int) $params['role'] : 0;
					if (isset($byRole[$role]))
					{
						continue;
					}
					$tp = isset($params['typeParams']) ? $params['typeParams'] : array();
					if (is_string($tp))
					{
						$tp = json_decode($tp, true);
					}
					$byRole[$role] = array(
						'label'    => isset($el['label']) ? (string) $el['label'] : '',
						'multiZip' => (is_array($tp) && isset($tp['multiZip'])) ? (int) $tp['multiZip'] : 1,
					);
				}
			}
		}

		return $byRole;
	}

	/**
	 * A version's authors as array(name, org), resolving each to the stored
	 * name/organization or, failing that, the linked profile's (the
	 * p_name/p_organization the publication author loader joins from
	 * #__xprofiles). Submitters and inactive authors are excluded, ordered as
	 * package() lists them.
	 *
	 * @param   integer  $versionId
	 * @return  array
	 */
	protected function versionAuthors($versionId)
	{
		$db = \App::get('db');
		$db->setQuery(
			"SELECT A.`name`, A.`organization`, x.`name` AS p_name, x.`organization` AS p_organization
			 FROM `#__publication_authors` A
			 JOIN `#__project_owners` PO ON PO.`id` = A.`project_owner_id`
			 LEFT JOIN `#__xprofiles` x ON x.`uidNumber` = PO.`userid`
			 WHERE A.`publication_version_id` = " . (int) $versionId . "
			   AND A.`status` = 1
			   AND (A.`role` != 'submitter' OR A.`role` IS NULL)
			 ORDER BY A.`ordering` ASC"
		);

		$out = array();
		foreach ((array) $db->loadObjectList() as $a)
		{
			$name = ($a->name !== null && $a->name !== '') ? $a->name : (string) $a->p_name;
			$org  = ($a->organization !== null && $a->organization !== '') ? $a->organization : (string) $a->p_organization;
			$out[] = array('name' => (string) $name, 'org' => trim((string) $org));
		}

		return $out;
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
	 * Run a command (argv array) quietly, returning its exit code. Optionally
	 * in a given working directory and with a string fed to its stdin.
	 *
	 * @param   array    $cmd
	 * @param   string   $cwd    working directory, or null
	 * @param   string   $stdin  data to write to the command's stdin, or null
	 * @return  integer
	 */
	protected function runQuiet($cmd, $cwd = null, $stdin = null)
	{
		$line = implode(' ', array_map('escapeshellarg', $cmd));

		$proc = proc_open($line, array(
			0 => ($stdin === null ? array('file', '/dev/null', 'r') : array('pipe', 'r')),
			1 => array('file', '/dev/null', 'w'),
			2 => array('pipe', 'w'),
		), $pipes, $cwd);

		if (!is_resource($proc))
		{
			return 1;
		}

		if ($stdin !== null)
		{
			fwrite($pipes[0], $stdin);
			fclose($pipes[0]);
		}

		$err = stream_get_contents($pipes[2]);
		fclose($pipes[2]);

		return proc_close($proc);
	}
}
