<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Plugin\Filesystem\Github;

use Github\Client;
use Github\Exception\RuntimeException;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Adapter\Polyfill\StreamedTrait;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Config;
use League\Flysystem\Exception;
use League\Flysystem\Util\MimeType;

/**
 * Read-only Flysystem (v1) adapter for browsing a GitHub repository.
 *
 * This is a first-party replacement for the abandoned
 * potherca/flysystem-github library (last released 2016, knplabs v1 only).
 * It talks to the maintained knplabs/github-api v3 client directly, so the
 * hub no longer carries a hand-patched fork of a dead dependency.
 *
 * GitHub connections are surfaced in the project Files browser as
 * browse / read / download only (the UI disables upload, new-folder, move,
 * rename and delete for github providers), so only the read side of the
 * Flysystem adapter contract is implemented; mutating operations throw.
 *
 * The array shapes returned here intentionally match what the old adapter
 * produced so existing consumers (com_projects connected files) need no
 * changes.
 **/
class GithubAdapter extends AbstractAdapter
{
	use StreamedTrait;

	const ERROR_NOT_FOUND = 'Not Found';

	const KEY_BLOB       = 'blob';
	const KEY_CONTENTS   = 'contents';
	const KEY_DIRECTORY  = 'dir';
	const KEY_FILE       = 'file';
	const KEY_FILENAME   = 'basename';
	const KEY_MODE       = 'mode';
	const KEY_NAME       = 'name';
	const KEY_PATH       = 'path';
	const KEY_SIZE       = 'size';
	const KEY_STREAM     = 'stream';
	const KEY_TIMESTAMP  = 'timestamp';
	const KEY_TREE       = 'tree';
	const KEY_TYPE       = 'type';
	const KEY_VISIBILITY = 'visibility';

	/** @var Client */
	private $client;

	/** @var string  Repository owner (the "vendor" half of "owner/repo") */
	private $vendor;

	/** @var string  Repository name (the "package" half of "owner/repo") */
	private $package;

	/** @var string  Git reference (branch/tag/sha) used for tree and content reads */
	private $reference;

	/** @var string  Branch used when listing a file's commit history */
	private $branch;

	/** @var bool */
	private $authenticated = false;

	/** @var string|null */
	private $token;

	/**
	 * Constructor
	 *
	 * @param   string       $repository  Repository in "owner/repo" form
	 * @param   string|null  $token       OAuth access token, or null for public read
	 * @param   string       $reference   Git reference to read from (branch/tag/sha)
	 * @param   string       $branch      Branch for commit-history lookups
	 * @param   Client|null  $client      Pre-built knplabs client (optional)
	 * @throws  \InvalidArgumentException  When the repository name is malformed
	 **/
	public function __construct($repository, $token = null, $reference = 'HEAD', $branch = 'master', Client $client = null)
	{
		$repository = $this->normalizeRepository($repository);

		if ($repository === ''
			|| substr_count($repository, '/') !== 1
			|| substr($repository, 0, 1) === '/'
			|| substr($repository, -1, 1) === '/')
		{
			throw new \InvalidArgumentException(sprintf(
				'Given Repository name "%s" should be in the format of "owner/repo"',
				var_export($repository, true)
			));
		}

		list($this->vendor, $this->package) = explode('/', $repository);

		$this->token     = $token;
		$this->reference = (string) $reference;
		$this->branch    = (string) $branch;
		$this->client    = $client ?: new Client();
	}

	/**
	 * Reduce a variety of accepted repository inputs to the "owner/repo" form
	 * the GitHub API needs.
	 *
	 * Users routinely paste a full clone URL into the connection's repository
	 * field (e.g. "https://github.com/owner/repo.git" or
	 * "git@github.com:owner/repo.git"); the previous adapter rejected those
	 * outright, which is one of the failures behind support ticket 4965.
	 *
	 * @param   mixed  $repository
	 * @return  string
	 **/
	private function normalizeRepository($repository)
	{
		$repository = trim((string) $repository);

		// Pull "owner/repo" out of a full GitHub URL (https or scp-style).
		if (preg_match('#github\.com[:/]+([^/]+/[^/]+)#i', $repository, $matches))
		{
			$repository = $matches[1];
		}

		// Drop a trailing ".git" and any surrounding slashes.
		$repository = preg_replace('#\.git$#i', '', $repository);

		return trim($repository, '/');
	}

	/**
	 * Authenticate the client on first use.
	 *
	 * knplabs v3 sends the token in the Authorization header
	 * (AUTH_ACCESS_TOKEN); GitHub removed the old URL-query token auth in 2020.
	 *
	 * @return  void
	 **/
	private function authenticate()
	{
		if (!$this->authenticated)
		{
			if (!empty($this->token))
			{
				$this->client->authenticate($this->token, null, Client::AUTH_ACCESS_TOKEN);
			}

			$this->authenticated = true;
		}
	}

	/**
	 * @return  \Github\Api\Repository\Contents
	 **/
	private function contents()
	{
		$this->authenticate();
		return $this->client->api('repo')->contents();
	}

	/**
	 * Check that a file or directory exists in the repository
	 *
	 * @param   string  $path
	 * @return  bool
	 **/
	public function has($path)
	{
		return $this->contents()->exists($this->vendor, $this->package, $path, $this->reference);
	}

	/**
	 * Read a file
	 *
	 * @param   string  $path
	 * @return  array
	 **/
	public function read($path)
	{
		return [self::KEY_CONTENTS => $this->contents()->download($this->vendor, $this->package, $path, $this->reference)];
	}

	/**
	 * List contents of a directory.
	 *
	 * Mirrors the previous adapter: the full recursive tree under $path is
	 * returned in one shot.
	 *
	 * @param   string  $path
	 * @param   bool    $recursive
	 * @return  array
	 **/
	public function listContents($path = '', $recursive = false)
	{
		return $this->recursiveMetadata($path, true);
	}

	/**
	 * Get the metadata for a file or directory.
	 *
	 * @param   string  $path
	 * @return  array|false
	 **/
	public function getMetadata($path)
	{
		$metadata = $this->showMetadata($path);

		// When contents()->show() is given a directory it returns a numerically
		// indexed list of entries, so the first value is itself an array.
		if (is_array($metadata) && is_array(current($metadata)))
		{
			return [
				self::KEY_TYPE => self::KEY_DIRECTORY,
				self::KEY_SIZE => 0,
				self::KEY_PATH => $path
			];
		}

		return $metadata;
	}

	/**
	 * Get the size of a file.
	 *
	 * @param   string  $path
	 * @return  array|false
	 **/
	public function getSize($path)
	{
		return $this->showMetadata($path);
	}

	/**
	 * Get the mimetype of a file.
	 *
	 * GitHub does not report a MIME type, so it is guessed from the extension.
	 *
	 * @param   string  $path
	 * @return  array
	 **/
	public function getMimetype($path)
	{
		$mimeType = null;

		if (strrpos($path, '.') > 1)
		{
			$extension = substr($path, strrpos($path, '.') + 1);
			$mimeType  = MimeType::detectByFileExtension($extension) ?: 'text/plain';
		}

		return ['mimetype' => $mimeType];
	}

	/**
	 * Get the last-modified timestamp of a file.
	 *
	 * @param   string  $path
	 * @return  array
	 **/
	public function getTimestamp($path)
	{
		$commits = $this->commitsForFile($path);
		$updated = array_shift($commits);

		$time = new \DateTime($updated['commit']['committer']['date']);

		return [self::KEY_TIMESTAMP => $time->getTimestamp()];
	}

	/**
	 * Get the visibility of a file.
	 *
	 * @param   string  $path
	 * @return  array|false
	 **/
	public function getVisibility($path)
	{
		$metadata = $this->recursiveMetadata($path, false);

		return isset($metadata[0]) ? $metadata[0] : false;
	}

	//////////////////////////////// WRITE (unsupported) \\\\\\\\\\\\\\\\\\\\\\\\

	public function write($path, $contents, Config $config)
	{
		throw new Exception('Write actions are not supported for GitHub connections');
	}

	public function update($path, $contents, Config $config)
	{
		throw new Exception('Write actions are not supported for GitHub connections');
	}

	public function rename($path, $newpath)
	{
		throw new Exception('Write actions are not supported for GitHub connections');
	}

	public function copy($path, $newpath)
	{
		throw new Exception('Write actions are not supported for GitHub connections');
	}

	public function delete($path)
	{
		throw new Exception('Write actions are not supported for GitHub connections');
	}

	public function deleteDir($dirname)
	{
		throw new Exception('Write actions are not supported for GitHub connections');
	}

	public function createDir($dirname, Config $config)
	{
		throw new Exception('Write actions are not supported for GitHub connections');
	}

	public function setVisibility($path, $visibility)
	{
		return false;
	}

	//////////////////////////////// UTILITY METHODS \\\\\\\\\\\\\\\\\\\\\\\\\\\\

	/**
	 * Fetch raw metadata for a single path, normalizing GitHub's "Not Found"
	 * error into a false return (as Flysystem expects).
	 *
	 * @param   string  $path
	 * @return  array|false
	 **/
	private function showMetadata($path)
	{
		try
		{
			return $this->contents()->show($this->vendor, $this->package, $path, $this->reference);
		}
		catch (RuntimeException $exception)
		{
			if ($exception->getMessage() === self::ERROR_NOT_FOUND)
			{
				return false;
			}

			throw $exception;
		}
	}

	/**
	 * Pull the repository's git tree and return normalized metadata for the
	 * entries under $path.
	 *
	 * @param   string  $path
	 * @param   bool    $recursive
	 * @return  array
	 **/
	private function recursiveMetadata($path, $recursive)
	{
		$this->authenticate();

		// If the response is truncated the tree exceeded GitHub's limit and
		// additional calls would be needed to page through it.
		$info = $this->client->api('git')->trees()->show(
			$this->vendor,
			$this->package,
			$this->reference,
			$recursive
		);

		$tree = isset($info[self::KEY_TREE]) ? $info[self::KEY_TREE] : [];

		return $this->normalizeTree($this->filterTree($tree, $path, $recursive));
	}

	/**
	 * Restrict a flat git tree to the entries that live under $path.
	 *
	 * @param   array   $tree
	 * @param   string  $path
	 * @param   bool    $recursive
	 * @return  array
	 **/
	private function filterTree(array $tree, $path, $recursive)
	{
		if (!empty($path))
		{
			$path = rtrim($path, '/') . '/';

			return array_filter($tree, function ($entry) use ($path, $recursive) {
				if (strpos($entry[self::KEY_PATH], $path) !== 0)
				{
					return false;
				}

				if ($recursive === true)
				{
					return true;
				}

				return strpos($entry[self::KEY_PATH], '/', strlen($path)) === false;
			});
		}

		if ($recursive === false)
		{
			return array_filter($tree, function ($entry) {
				return strpos($entry[self::KEY_PATH], '/') === false;
			});
		}

		return $tree;
	}

	/**
	 * Normalize git-tree entries into the shape the file browser expects.
	 *
	 * @param   array  $tree
	 * @return  array
	 **/
	private function normalizeTree($tree)
	{
		$result = [];

		if (is_array(current($tree)) === false)
		{
			$tree = [$tree];
		}

		foreach ($tree as $entry)
		{
			$this->setEntryName($entry);
			$this->setEntryType($entry);
			$this->setEntryVisibility($entry);

			$this->setDefault($entry, self::KEY_CONTENTS);
			$this->setDefault($entry, self::KEY_STREAM);
			$this->setDefault($entry, self::KEY_TIMESTAMP);

			$result[] = $entry;
		}

		return $result;
	}

	/**
	 * List the commits touching a path (most recent first).
	 *
	 * @param   string  $path
	 * @return  array
	 **/
	private function commitsForFile($path)
	{
		$this->authenticate();

		return $this->client->api('repo')->commits()->all(
			$this->vendor,
			$this->package,
			['sha' => $this->branch, 'path' => $path]
		);
	}

	/**
	 * @param   array   $entry
	 * @param   string  $key
	 * @return  void
	 **/
	private function setDefault(array &$entry, $key)
	{
		if (!isset($entry[$key]))
		{
			$entry[$key] = false;
		}
	}

	/**
	 * Map GitHub's tree object type to Flysystem's file/dir type.
	 *
	 * @param   array  $entry
	 * @return  void
	 **/
	private function setEntryType(&$entry)
	{
		if (isset($entry[self::KEY_TYPE]))
		{
			if ($entry[self::KEY_TYPE] === self::KEY_BLOB)
			{
				$entry[self::KEY_TYPE] = self::KEY_FILE;
			}
			elseif ($entry[self::KEY_TYPE] === self::KEY_TREE)
			{
				$entry[self::KEY_TYPE] = self::KEY_DIRECTORY;
			}
		}
	}

	/**
	 * Derive visibility from the tree entry's unix mode.
	 *
	 * @param   array  $entry
	 * @return  void
	 **/
	private function setEntryVisibility(&$entry)
	{
		if (isset($entry[self::KEY_MODE]))
		{
			$entry[self::KEY_VISIBILITY] = ($entry[self::KEY_MODE] & 0044)
				? AdapterInterface::VISIBILITY_PUBLIC
				: AdapterInterface::VISIBILITY_PRIVATE;
		}
		else
		{
			$entry[self::KEY_VISIBILITY] = false;
		}
	}

	/**
	 * Ensure every entry carries a name.
	 *
	 * @param   array  $entry
	 * @return  void
	 **/
	private function setEntryName(&$entry)
	{
		if (!isset($entry[self::KEY_NAME]))
		{
			if (isset($entry[self::KEY_FILENAME]))
			{
				$entry[self::KEY_NAME] = $entry[self::KEY_FILENAME];
			}
			elseif (isset($entry[self::KEY_PATH]))
			{
				$entry[self::KEY_NAME] = $entry[self::KEY_PATH];
			}
			else
			{
				$entry[self::KEY_NAME] = null;
			}
		}
	}
}
