<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Filesystem\Github;

use Github\Client;
use Github\Exception\RuntimeException;
use League\Flysystem\Config;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToCreateDirectory;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToSetVisibility;
use League\Flysystem\UnableToWriteFile;
use League\Flysystem\Visibility;
use League\MimeTypeDetection\ExtensionMimeTypeDetector;

/**
 * Read-only Flysystem (v3) adapter for browsing a GitHub repository.
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
 **/
class GithubAdapter implements FilesystemAdapter
{
    const ERROR_NOT_FOUND = 'Not Found';

    const KEY_BLOB = 'blob';
    const KEY_MODE = 'mode';
    const KEY_PATH = 'path';
    const KEY_SIZE = 'size';
    const KEY_TREE = 'tree';
    const KEY_TYPE = 'type';

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

    /** @var ExtensionMimeTypeDetector */
    private $mimeTypeDetector;

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
    public function __construct($repository, $token = null, $reference = 'HEAD', $branch = 'master', ?Client $client = null)
    {
        $repository = $this->normalizeRepository($repository);

        if (
            $repository === ''
            || substr_count($repository, '/') !== 1
            || substr($repository, 0, 1) === '/'
            || substr($repository, -1, 1) === '/'
        ) {
            throw new \InvalidArgumentException(sprintf(
                'Given Repository name "%s" should be in the format of "owner/repo"',
                var_export($repository, true)
            ));
        }

        list($this->vendor, $this->package) = explode('/', $repository);

        $this->token            = $token;
        $this->reference        = (string) $reference;
        $this->branch           = (string) $branch;
        $this->client           = $client ?: new Client();
        $this->mimeTypeDetector = new ExtensionMimeTypeDetector();
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
        if (preg_match('#github\.com[:/]+([^/]+/[^/]+)#i', $repository, $matches)) {
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
        if (!$this->authenticated) {
            if (!empty($this->token)) {
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
     * Run a read against the API, transparently retrying anonymously if GitHub
     * rejects the stored credential.
     *
     * A public repository is readable without any credential, so an expired or
     * revoked token must never block reading one. If the authenticated call
     * fails with an auth error we drop the credential and try again as an
     * anonymous client; a genuinely private repo will still fail (as "Not
     * Found"), which callers handle.
     *
     * @param   callable  $fn
     * @return  mixed
     **/
    private function readCall(callable $fn)
    {
        try {
            return $fn();
        } catch (RuntimeException $e) {
            if (!empty($this->token) && $this->isAuthFailure($e)) {
                $this->token         = null;
                $this->authenticated = false;
                $this->client        = new Client();

                return $fn();
            }

            throw $e;
        }
    }

    /**
     * Whether an API exception indicates the credential was rejected.
     *
     * @param   RuntimeException  $e
     * @return  bool
     **/
    private function isAuthFailure(RuntimeException $e)
    {
        $message = strtolower($e->getMessage());

        return $e->getCode() == 401
            || strpos($message, 'bad credentials') !== false
            || strpos($message, 'requires authentication') !== false;
    }

    //////////////////////////////// EXISTENCE \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    /**
     * Does a file exist at this path?
     *
     * @param   string  $path
     * @return  bool
     **/
    public function fileExists(string $path): bool
    {
        // Route through showMetadata() rather than knplabs' exists(): exists()
        // swallows a rejected-credential error and returns false, which would
        // bypass the anonymous retry in readCall() and make a public file look
        // missing whenever a stale token is stored. show() throws instead, so
        // the fallback applies; a genuinely absent path returns false.
        $metadata = $this->showMetadata($path);

        return $metadata !== false && !self::isDirectoryListing($metadata);
    }

    /**
     * Does a directory exist at this path?
     *
     * @param   string  $path
     * @return  bool
     **/
    public function directoryExists(string $path): bool
    {
        if (trim($path, '/') === '') {
            return true;
        }

        $metadata = $this->showMetadata($path);

        return $metadata !== false && self::isDirectoryListing($metadata);
    }

    //////////////////////////////// READ \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    /**
     * Read a file
     *
     * @param   string  $path
     * @return  string
     **/
    public function read(string $path): string
    {
        try {
            return (string) $this->readCall(function () use ($path) {
                return $this->contents()->download($this->vendor, $this->package, $path, $this->reference);
            });
        } catch (RuntimeException $e) {
            throw UnableToReadFile::fromLocation($path, $e->getMessage(), $e);
        }
    }

    /**
     * Read a file as a stream
     *
     * @param   string  $path
     * @return  resource
     **/
    public function readStream(string $path)
    {
        $contents = $this->read($path);

        $stream = fopen('php://temp', 'w+b');
        fwrite($stream, $contents);
        rewind($stream);

        return $stream;
    }

    /**
     * List the contents of a directory.
     *
     * The whole repository tree is fetched in one call and filtered here, as
     * GitHub's non-recursive tree endpoint only describes the root.
     *
     * @param   string  $path
     * @param   bool    $deep
     * @return  iterable<FileAttributes|DirectoryAttributes>
     **/
    public function listContents(string $path, bool $deep): iterable
    {
        foreach ($this->treeEntries($path, $deep) as $entry) {
            yield $this->attributes($entry);
        }
    }

    //////////////////////////////// METADATA \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    /**
     * Get the MIME type of a file.
     *
     * GitHub does not report a MIME type, so it is guessed from the extension.
     *
     * @param   string  $path
     * @return  FileAttributes
     **/
    public function mimeType(string $path): FileAttributes
    {
        $mimeType = $this->mimeTypeDetector->detectMimeTypeFromPath($path);

        if ($mimeType === null) {
            $mimeType = 'text/plain';
        }

        return new FileAttributes($path, null, null, null, $mimeType);
    }

    /**
     * Get the last-modified timestamp of a file.
     *
     * @param   string  $path
     * @return  FileAttributes
     **/
    public function lastModified(string $path): FileAttributes
    {
        // Display-only metadata: a commit lookup can legitimately fail (e.g. the
        // default branch is not "master"), and that must never break browsing.
        $timestamp = 0;

        try {
            $commits = $this->commitsForFile($path);
            $updated = array_shift($commits);

            if (isset($updated['commit']['committer']['date'])) {
                $time      = new \DateTime($updated['commit']['committer']['date']);
                $timestamp = $time->getTimestamp();
            }
        } catch (\Throwable $e) {
            // fall through to the safe default
        }

        return new FileAttributes($path, null, null, $timestamp);
    }

    /**
     * Get the size of a file.
     *
     * @param   string  $path
     * @return  FileAttributes
     **/
    public function fileSize(string $path): FileAttributes
    {
        $metadata = $this->showMetadata($path);

        if ($metadata === false || self::isDirectoryListing($metadata)) {
            throw UnableToRetrieveMetadata::fileSize($path, self::ERROR_NOT_FOUND);
        }

        return new FileAttributes($path, isset($metadata[self::KEY_SIZE]) ? (int) $metadata[self::KEY_SIZE] : null);
    }

    /**
     * Get the visibility of a file, from its mode in the git tree.
     *
     * @param   string  $path
     * @return  FileAttributes
     **/
    public function visibility(string $path): FileAttributes
    {
        foreach ($this->treeEntries(dirname($path) === '.' ? '' : dirname($path), false) as $entry) {
            if ($entry[self::KEY_PATH] === $path) {
                return new FileAttributes($path, null, $this->visibilityFromMode($entry));
            }
        }

        throw UnableToRetrieveMetadata::visibility($path, self::ERROR_NOT_FOUND);
    }

    //////////////////////////////// WRITE (unsupported) \\\\\\\\\\\\\\\\\\\\\\\\

    public function write(string $path, string $contents, Config $config): void
    {
        throw UnableToWriteFile::atLocation($path, 'Write actions are not supported for GitHub connections');
    }

    public function writeStream(string $path, $contents, Config $config): void
    {
        throw UnableToWriteFile::atLocation($path, 'Write actions are not supported for GitHub connections');
    }

    public function delete(string $path): void
    {
        throw UnableToDeleteFile::atLocation($path, 'Write actions are not supported for GitHub connections');
    }

    public function deleteDirectory(string $path): void
    {
        throw UnableToDeleteDirectory::atLocation($path, 'Write actions are not supported for GitHub connections');
    }

    public function createDirectory(string $path, Config $config): void
    {
        throw UnableToCreateDirectory::atLocation($path, 'Write actions are not supported for GitHub connections');
    }

    public function setVisibility(string $path, string $visibility): void
    {
        throw UnableToSetVisibility::atLocation($path, 'Write actions are not supported for GitHub connections');
    }

    public function move(string $source, string $destination, Config $config): void
    {
        throw UnableToMoveFile::fromLocationTo($source, $destination);
    }

    public function copy(string $source, string $destination, Config $config): void
    {
        throw UnableToCopyFile::fromLocationTo($source, $destination);
    }

    //////////////////////////////// UTILITY METHODS \\\\\\\\\\\\\\\\\\\\\\\\\\\\

    /**
     * Fetch raw metadata for a single path, normalizing GitHub's "Not Found"
     * error into a false return.
     *
     * For a file this is an associative array (type, size, sha, …); for a
     * directory it is a numerically indexed list of entries.
     *
     * @param   string  $path
     * @return  array|false
     **/
    private function showMetadata($path)
    {
        try {
            return $this->readCall(function () use ($path) {
                return $this->contents()->show($this->vendor, $this->package, $path, $this->reference);
            });
        } catch (RuntimeException $exception) {
            if ($exception->getMessage() === self::ERROR_NOT_FOUND) {
                return false;
            }

            throw $exception;
        }
    }

    /**
     * Is a contents()->show() response a directory listing?
     *
     * @param   mixed  $metadata
     * @return  bool
     **/
    private static function isDirectoryListing($metadata)
    {
        return is_array($metadata) && is_array(current($metadata));
    }

    /**
     * Pull the repository's git tree and return the entries under $path.
     *
     * @param   string  $path
     * @param   bool    $recursive
     * @return  array
     **/
    private function treeEntries($path, $recursive)
    {
        // If the response is truncated the tree exceeded GitHub's limit and
        // additional calls would be needed to page through it.
        $info = $this->readCall(function () {
            $this->authenticate();
            return $this->client->api('git')->trees()->show(
                $this->vendor,
                $this->package,
                $this->reference,
                true
            );
        });

        $tree = isset($info[self::KEY_TREE]) ? $info[self::KEY_TREE] : [];

        return $this->filterTree($tree, $path, $recursive);
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
        $path = trim((string) $path, '/');

        if ($path !== '') {
            $path .= '/';

            return array_filter($tree, function ($entry) use ($path, $recursive) {
                if (strpos($entry[self::KEY_PATH], $path) !== 0) {
                    return false;
                }

                if ($recursive === true) {
                    return true;
                }

                return strpos($entry[self::KEY_PATH], '/', strlen($path)) === false;
            });
        }

        if ($recursive === false) {
            return array_filter($tree, function ($entry) {
                return strpos($entry[self::KEY_PATH], '/') === false;
            });
        }

        return $tree;
    }

    /**
     * Turn a git tree entry into Flysystem attributes
     *
     * @param   array  $entry
     * @return  FileAttributes|DirectoryAttributes
     **/
    private function attributes(array $entry)
    {
        $type = isset($entry[self::KEY_TYPE]) ? $entry[self::KEY_TYPE] : self::KEY_BLOB;

        if ($type === self::KEY_TREE) {
            return new DirectoryAttributes($entry[self::KEY_PATH], $this->visibilityFromMode($entry));
        }

        return new FileAttributes(
            $entry[self::KEY_PATH],
            isset($entry[self::KEY_SIZE]) ? (int) $entry[self::KEY_SIZE] : null,
            $this->visibilityFromMode($entry)
        );
    }

    /**
     * Derive visibility from a tree entry's unix mode.
     *
     * @param   array  $entry
     * @return  string|null
     **/
    private function visibilityFromMode(array $entry)
    {
        if (!isset($entry[self::KEY_MODE])) {
            return null;
        }

        return (octdec($entry[self::KEY_MODE]) & 0044) ? Visibility::PUBLIC : Visibility::PRIVATE;
    }

    /**
     * List the commits touching a path (most recent first).
     *
     * @param   string  $path
     * @return  array
     **/
    private function commitsForFile($path)
    {
        return $this->readCall(function () use ($path) {
            $this->authenticate();
            // Omit 'sha' so GitHub uses the repository's actual default branch
            // (which may be "main", not the legacy "master").
            return $this->client->api('repo')->commits()->all(
                $this->vendor,
                $this->package,
                ['path' => $path]
            );
        });
    }
}
