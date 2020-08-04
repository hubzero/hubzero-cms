<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Filesystem\Adapter;

use Hubzero\Filesystem\AdapterInterface;
use Hubzero\Filesystem\Util;
use RuntimeException;

/**
 * FTP adapter for filesystem.
 */
class Ftp extends AbstractFtpAdapter
{
	/**
	 * FTP Transfer mode
	 *
	 * @var int
	 */
	protected $transferMode = FTP_BINARY;

	/**
	 * List of configurable items
	 *
	 * @var array
	 */
	protected $configurable = array(
		'host',
		'port',
		'username',
		'password',
		'ssl',
		'timeout',
		'root',
		'passive',
		'transferMode',
	);

	protected $connection;
	protected $host;
	protected $port = 21;
	protected $username;
	protected $password;
	protected $ssl = false;
	protected $timeout = 90;
	protected $passive = true;
	protected $separator = '/';
	protected $root;

	/**
	 * Constructor.
	 *
	 * @param   array  $config
	 * @return  void
	 */
	public function __construct(array $config)
	{
		$this->setConfig($config);
	}

	/**
	 * Set the config.
	 *
	 * @param   array   $config
	 * @return  object  $this
	 */
	public function setConfig(array $config)
	{
		foreach ($this->configurable as $setting)
		{
			if (!isset($config[$setting]))
			{
				continue;
			}

			$method = 'set' . ucfirst($setting);

			if (method_exists($this, $method))
			{
				$this->$method($config[$setting]);
			}
		}

		return $this;
	}

	/**
	 * Returns the host.
	 *
	 * @return  string
	 */
	public function getHost()
	{
		return $this->host;
	}

	/**
	 * Set the host.
	 *
	 * @param   string  $host
	 * @return  object  $this
	 */
	public function setHost($host)
	{
		$this->host = $host;

		return $this;
	}

	/**
	 * Returns the ftp port.
	 *
	 * @return  int
	 */
	public function getPort()
	{
		return $this->port;
	}

	/**
	 * Set the ftp port.
	 *
	 * @param   mixed   $port
	 * @return  object  $this
	 */
	public function setPort($port)
	{
		$this->port = (int) $port;

		return $this;
	}

	/**
	 * Returns the root folder to work from.
	 *
	 * @return  string
	 */
	public function getRoot()
	{
		return $this->root;
	}

	/**
	 * Set the root folder to work from.
	 *
	 * @param   string  $root
	 * @return  object  $this
	 */
	public function setRoot($root)
	{
		$this->root = rtrim($root, '\\/') . $this->separator;

		return $this;
	}

	/**
	 * Returns the ftp username.
	 *
	 * @return  string  username
	 */
	public function getUsername()
	{
		return empty($this->username) ? 'anonymous' : $this->username;
	}

	/**
	 * Set ftp username.
	 *
	 * @param   string  $username
	 * @return  object  $this
	 */
	public function setUsername($username)
	{
		$this->username = $username;

		return $this;
	}

	/**
	 * Returns the password.
	 *
	 * @return  string  password
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * Set the ftp password.
	 *
	 * @param   string  $password
	 * @return  object  $this
	 */
	public function setPassword($password)
	{
		$this->password = $password;

		return $this;
	}

	/**
	 * Returns the amount of seconds before the connection will timeout.
	 *
	 * @return  int
	 */
	public function getTimeout()
	{
		return $this->timeout;
	}

	/**
	 * Set the amount of seconds before the connection should timeout.
	 *
	 * @param    int     $timeout
	 * @return   object  $this
	 */
	public function setTimeout($timeout)
	{
		$this->timeout = (int) $timeout;

		return $this;
	}

	/**
	 * Get the transfer mode.
	 *
	 * @return  int
	 */
	public function getTransferMode()
	{
		return $this->transferMode;
	}

	/**
	 * Set the transfer mode.
	 *
	 * @param   int     $mode
	 * @return  object  $this
	 */
	public function setTransferMode($mode)
	{
		$this->transferMode = $mode;

		return $this;
	}

	/**
	 * Get if Ssl is enabled.
	 *
	 * @return  bool
	 */
	public function getSsl()
	{
		return $this->ssl;
	}

	/**
	 * Set if Ssl is enabled.
	 *
	 * @param   bool    $ssl
	 * @return  object  $this
	 */
	public function setSsl($ssl)
	{
		$this->ssl = (bool) $ssl;

		return $this;
	}

	/**
	 * Set if passive mode should be used.
	 *
	 * @param   bool    $passive
	 * @return  object  $this
	 */
	public function setPassive($passive = true)
	{
		$this->passive = $passive;

		return $this;
	}

	/**
	 * Get the connection.
	 *
	 * @return  resource|Net_SFTP
	 */
	public function getConnection()
	{
		if (!$this->connection)
		{
			$this->connect();
		}

		return $this->connection;
	}

	/**
	 * Connect to the FTP server.
	 *
	 * @return  void
	 */
	public function connect()
	{
		if ($this->ssl)
		{
			$this->connection = ftp_ssl_connect($this->getHost(), $this->getPort(), $this->getTimeout());
		}
		else
		{
			$this->connection = ftp_connect($this->getHost(), $this->getPort(), $this->getTimeout());
		}

		if (!$this->connection)
		{
			throw new RuntimeException(sprintf('Could not connect to host: %s, port: %s', $this->getHost(), $this->getPort()));
		}

		$this->login();
		$this->setConnectionPassiveMode();
		$this->setConnectionRoot();
	}

	/**
	 * Set the connections to passive mode.
	 *
	 * @return  void
	 * @throws  RuntimeException
	 */
	protected function setConnectionPassiveMode()
	{
		if (!ftp_pasv($this->getConnection(), $this->passive))
		{
			throw new RuntimeException(sprintf('Could not set passive mode for connection: %s::%s', $this->getHost(), $this->getPort()));
		}
	}

	/**
	 * Set the connection root.
	 *
	 * @return  void
	 */
	protected function setConnectionRoot()
	{
		$root = $this->getRoot();
		$connection = $this->getConnection();

		if ($root && !ftp_chdir($connection, $root))
		{
			throw new RuntimeException('Root is invalid or does not exist: ' . $this->getRoot());
		}

		// Store absolute path for further reference.
		// This is needed when creating directories and
		// initial root was a relative path, else the root
		// would be relative to the chdir'd path.
		$this->root = ftp_pwd($connection);
	}

	/**
	 * Login.
	 *
	 * @return  void
	 * @throws  RuntimeException
	 */
	protected function login()
	{
		set_error_handler(function () {});
		$isLoggedIn = ftp_login($this->getConnection(), $this->getUsername(), $this->getPassword());
		restore_error_handler();

		if (!$isLoggedIn)
		{
			$this->disconnect();

			throw new RuntimeException(sprintf('Could not login with connection: %s::%s, username: %s', $this->getHost(), $this->getPort(), $this->getUsername()));
		}
	}

	/**
	 * Disconnect from the FTP server.
	 *
	 * @return  void
	 */
	public function disconnect()
	{
		if ($this->connection)
		{
			ftp_close($this->connection);
		}

		$this->connection = null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function write($path, $contents)
	{
		$mimetype = Util::guessMimeType($path, $contents);

		$stream = tmpfile();
		fwrite($stream, $contents);
		rewind($stream);

		$result = $this->writeStream($path, $stream);
		$result = fclose($stream) && $result;

		if ($result === false)
		{
			return false;
		}

		$this->setPermissions($path, $visibility);

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function writeStream($path, $resource)
	{
		$this->ensureDirectory(Util::dirname($path));

		if (!ftp_fput($this->getConnection(), $path, $resource, $this->transferMode))
		{
			return false;
		}

		$this->setPermissions($path, $visibility);

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function prepend($path, $contents)
	{
		if ($this->exists($path))
		{
			return $this->write($path, $contents . $this->read($path));
		}

		return $this->write($path, $contents);
	}

	/**
	 * {@inheritdoc}
	 */
	public function append($path, $contents)
	{
		if ($this->exists($path))
		{
			return $this->write($path, $this->read($path) . $contents);
		}

		return $this->write($path, $contents);
	}

	/**
	 * {@inheritdoc}
	 */
	public function move($path, $target)
	{
		return $this->rename($path, $target);
	}

	/**
	 * {@inheritdoc}
	 */
	public function rename($path, $target)
	{
		return ftp_rename($this->getConnection(), $path, $target);
	}

	/**
	 * {@inheritdoc}
	 */
	public function delete($path)
	{
		return ftp_delete($this->getConnection(), $path);
	}

	/**
	 * {@inheritdoc}
	 */
	public function find($paths, $file)
	{
		$paths = is_array($path) ? $path : array($path);

		foreach ($paths as $path)
		{
			$fullname = $path . DS . $file;

			if ($this->isFile($fullname) && substr($fullname, 0, strlen($path)) == $path)
			{
				return $fullname;
			}
		}

		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function makeDirectory($path, $mode = 0755, $recursive = true, $force = false)
	{
		$result = false;

		$connection  = $this->getConnection();
		$directories = explode('/', $path);

		foreach ($directories as $directory)
		{
			$result = $this->createActualDirectory($directory, $connection);

			if (!$result)
			{
				break;
			}

			ftp_chdir($connection, $directory);
		}

		$this->setConnectionRoot();

		return $result;
	}

	/**
	 * Create a directory.
	 *
	 * @param   string    $directory
	 * @param   resource  $connection
	 * @return  bool
	 */
	protected function createActualDirectory($directory, $connection)
	{
		// List the current directory
		$listing = ftp_nlist($connection, '.');

		foreach ($listing as $key => $item)
		{
			if (preg_match('~^\./.*~', $item))
			{
				$listing[$key] = substr($item, 2);
			}
		}

		if (in_array($directory, $listing))
		{
			return true;
		}

		return (boolean) ftp_mkdir($connection, $directory);
	}

	/**
	 * {@inheritdoc}
	 */
	public function copyDirectory($path, $target, $options = null)
	{
		$response = $this->readStream($path);

		if ($response === false || ! is_resource($response['stream']))
		{
			return false;
		}

		$result = $this->writeStream($target, $response['stream']);

		if ($result !== false && is_resource($response['stream']))
		{
			fclose($response['stream']);
		}

		return $result !== false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function deleteDirectory($dirname)
	{
		$connection = $this->getConnection();

		$contents = array_reverse($this->listDirectoryContents($dirname));

		foreach ($contents as $object)
		{
			if ($object['type'] === 'file')
			{
				if (!ftp_delete($connection, $object['path']))
				{
					return false;
				}
			}
			elseif (!ftp_rmdir($connection, $object['path']))
			{
				return false;
			}
		}

		return ftp_rmdir($connection, $dirname);
	}

	/**
	 * {@inheritdoc}
	 */
	public function mimetype($path)
	{
		if (! $contents = $this->read($path))
		{
			return false;
		}

		return Util::guessMimeType($path, $contents);
	}

	/**
	 * {@inheritdoc}
	 */
	public function isDirectory($directory)
	{
		$result = @ftp_chdir($this->connection(), $directory);
		$result = $result ? true : false;

		return $result;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isWritable($path)
	{
		return is_writable($path);
	}

	/**
	 * {@inheritdoc}
	 */
	public function isFile($file)
	{
		$result = @ftp_chdir($this->connection(), $directory);
		$result = $result ? false : true;

		return $result;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isSafe($file)
	{
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function read($path)
	{
		if (! $object = $this->readStream($path))
		{
			return false;
		}

		$contents = stream_get_contents($object['stream']);
		fclose($object['stream']);
		unset($object['stream']);

		return $contents;
	}

	/**
	 * {@inheritdoc}
	 */
	public function readStream($path)
	{
		$stream = fopen('php://temp', 'w+');
		$result = ftp_fget($this->getConnection(), $stream, $path, $this->transferMode);
		rewind($stream);

		if (!$result)
		{
			fclose($stream);

			return false;
		}

		return compact('stream');
	}

	/**
	 * {@inheritdoc}
	 */
	public function setPermissions($path, $filemode = '0644', $foldermode = '0755')
	{
		if (!ftp_chmod($this->getConnection(), $foldermode, $path))
		{
			return false;
		}

		return true;
	}

	/**
	 * Ensure a directory exists.
	 *
	 * @param  string  $dirname
	 */
	public function ensureDirectory($dirname)
	{
		if (!empty($dirname) && !$this->exists($dirname))
		{
			$this->makeDirectory($dirname);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function listContents($path, $filter = '.', $recursive = false, $full = false, $exclude = array('.svn', '.git', 'CVS', '.DS_Store', '__MACOSX'))
	{
		$listing = ftp_rawlist($this->getConnection(), '-lna ' . $path, $recursive);

		return $listing ? $this->normalizeListing($listing, ($full ? '' : $path), $filter, $exclude) : array();
	}

	/**
	 * Normalize a directory listing.
	 *
	 * @param   array   $listing
	 * @param   string  $prefix
	 * @return  array   Directory listing
	 */
	protected function normalizeListing(array $listing, $prefix = '', $filter = '.', $exclude = array('.svn', '.git', 'CVS', '.DS_Store', '__MACOSX'))
	{
		$base = $prefix;

		$result  = array();
		$listing = $this->removeDotDirectories($listing);

		while ($item = array_shift($listing))
		{
			if (preg_match('#^.*:$#', $item))
			{
				$base = trim($item, ':');
				continue;
			}

			$file = $this->normalizeObject($item, $base);

			$name = basename($file['path']);

			if (preg_match("/$filter/", $name) && !in_array($name, $exclude))
			{
				$result[] = $file;
			}
		}

		return $this->sortListing($result);
	}

	/**
	 * Sort a directory listing.
	 *
	 * @param   array  $result
	 * @return  array  Sorted listing
	 */
	protected function sortListing(array $result)
	{
		$compare = function ($one, $two)
		{
			return strnatcmp($one['path'], $two['path']);
		};

		usort($result, $compare);

		return $result;
	}

	/**
	 * Normalize a file entry.
	 *
	 * @param   string  $item
	 * @param   string  $base
	 * @return  array   Normalized file array
	 */
	protected function normalizeObject($item, $base)
	{
		$item = preg_replace('#\s+#', ' ', trim($item), 7);
		list($permissions, /* $number */, /* $owner */, /* $group */, $size, /* $month */, /* $day */, /* $time*/, $name) = explode(' ', $item, 9);

		$type = $this->detectType($permissions);
		$path = empty($base) ? $name : $base . $this->separator . $name;

		if ($type === 'dir')
		{
			return compact('type', 'path');
		}

		$permissions = $this->normalizePermissions($permissions);
		$size = (int) $size;

		return compact('type', 'path', 'permissions', 'size');
	}

	/**
	 * Get the file type from the permissions.
	 *
	 * @param   string  $permissions
	 * @return  string  File type
	 */
	protected function detectType($permissions)
	{
		return substr($permissions, 0, 1) === 'd' ? 'dir' : 'file';
	}

	/**
	 * Normalize a permissions string.
	 *
	 * @param   string  $permissions
	 * @return  int
	 */
	protected function normalizePermissions($permissions)
	{
		// remove the type identifier
		$permissions = substr($permissions, 1);

		// map the string rights to the numeric counterparts
		$map = array(
			'-' => '0',
			'r' => '4',
			'w' => '2',
			'x' => '1'
		);
		$permissions = strtr($permissions, $map);

		// split up the permission groups
		$parts = str_split($permissions, 3);

		// convert the groups
		$mapper = function ($part)
		{
			return array_sum(str_split($part));
		};

		// get the sum of the groups
		return array_sum(array_map($mapper, $parts));
	}

	/**
	 * Filter out dot-directories.
	 *
	 * @param   array  $list
	 * @return  array
	 */
	public function removeDotDirectories(array $list)
	{
		$filter = function ($line)
		{
			if (!empty($line) && !preg_match('#.* \.(\.)?$|^total#', $line))
			{
				return true;
			}

			return false;
		};

		return array_filter($list, $filter);
	}

	/**
	 * Disconnect on destruction.
	 *
	 * @return  void
	 */
	public function __destruct()
	{
		$this->disconnect();
	}
}
