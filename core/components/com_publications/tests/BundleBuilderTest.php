<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Tests;

use Hubzero\Test\Basic;

require_once dirname(__DIR__) . DS . 'models' . DS . 'bundlebuilder.php';

use Components\Publications\Models\BundleBuilder;

/**
 * Safety tests for BundleBuilder::removeStage(), the teardown of the symlink
 * staging tree the Databases / metadata-only zip(1) build creates. The staging
 * tree is real directories holding symlinks to the publication's real source
 * files, so the teardown MUST NEVER follow a link or it could delete a source
 * file. These tests pin that guarantee — especially the dangerous cases (a
 * symlink whose target is a directory of real files) — so the previous blunt
 * `rm -rf` can never be reintroduced without a failing test.
 */
class BundleBuilderTest extends Basic
{
	/**
	 * @var  BundleBuilder
	 */
	protected $builder;

	/**
	 * Scratch root for the test trees.
	 *
	 * @var  string
	 */
	protected $root;

	/**
	 * @return  void
	 */
	protected function setUp(): void
	{
		$this->builder = new BundleBuilder();
		$this->root    = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'bb_removestage_' . uniqid('', true);
		mkdir($this->root, 0775, true);
	}

	/**
	 * @return  void
	 */
	protected function tearDown(): void
	{
		// Independent of the method under test: unlink links/files child-first,
		// rmdir dirs, never following a link.
		$this->saferm($this->root);
	}

	/**
	 * Call the protected removeStage().
	 *
	 * @param   string  $dir
	 * @return  void
	 */
	protected function removeStage($dir)
	{
		$m = new \ReflectionMethod($this->builder, 'removeStage');
		$m->setAccessible(true);
		$m->invoke($this->builder, $dir);
	}

	/**
	 * Call the protected verifyZip().
	 *
	 * @param   string   $path
	 * @param   integer  $min
	 * @return  boolean
	 */
	protected function verifyZip($path, $min = 1)
	{
		$m = new \ReflectionMethod($this->builder, 'verifyZip');
		$m->setAccessible(true);
		return $m->invoke($this->builder, $path, $min);
	}

	/**
	 * Test-local recursive remove (does not follow symlinks).
	 *
	 * @param   string  $dir
	 * @return  void
	 */
	protected function saferm($dir)
	{
		if (is_link($dir) || is_file($dir)) { @unlink($dir); return; }
		if (!is_dir($dir)) { return; }
		foreach (array_diff((array) @scandir($dir), array('.', '..')) as $i)
		{
			$this->saferm($dir . DIRECTORY_SEPARATOR . $i);
		}
		@rmdir($dir);
	}

	/**
	 * The staging tree (real dirs + symlinks to real sources, including a
	 * symlink whose target is a whole directory) is fully removed, while every
	 * source file and directory the links pointed at survives untouched.
	 *
	 * @return  void
	 */
	public function testRemoveStageDeletesStagingButNeverFollowsLinksToSources()
	{
		$ds = DIRECTORY_SEPARATOR;

		// Real source the build would read from.
		$src = $this->root . $ds . 'src';
		mkdir($src . $ds . 'sub', 0775, true);
		file_put_contents($src . $ds . 'a.txt', 'AAA');
		file_put_contents($src . $ds . 'sub' . $ds . 'b.txt', 'BBB');

		// Staging tree: real dirs holding file symlinks to the sources, plus a
		// DIRECTORY symlink to the whole source dir (the dangerous case).
		$stage = $this->root . $ds . 'D.zip.stage.' . getmypid();
		mkdir($stage . $ds . 'DOI' . $ds . 'data' . $ds . 'sub', 0775, true);
		symlink($src . $ds . 'a.txt', $stage . $ds . 'DOI' . $ds . 'data' . $ds . 'a.txt');
		symlink($src . $ds . 'sub' . $ds . 'b.txt', $stage . $ds . 'DOI' . $ds . 'data' . $ds . 'sub' . $ds . 'b.txt');
		symlink($src, $stage . $ds . 'DOI' . $ds . 'dlink');

		$this->removeStage($stage);

		// Staging is gone.
		$this->assertDirectoryDoesNotExist($stage, 'staging tree removed');

		// Every source survives, byte-for-byte.
		$this->assertDirectoryExists($src);
		$this->assertDirectoryExists($src . $ds . 'sub');
		$this->assertSame('AAA', file_get_contents($src . $ds . 'a.txt'));
		$this->assertSame('BBB', file_get_contents($src . $ds . 'sub' . $ds . 'b.txt'));
	}

	/**
	 * Called directly on a symlink whose target is a directory, removeStage
	 * unlinks the link and leaves the target (and its contents) intact.
	 *
	 * @return  void
	 */
	public function testRemoveStageOnDirectorySymlinkUnlinksOnly()
	{
		$ds  = DIRECTORY_SEPARATOR;
		$src = $this->root . $ds . 'realdir';
		mkdir($src, 0775, true);
		file_put_contents($src . $ds . 'keep.txt', 'KEEP');

		$link = $this->root . $ds . 'linkdir';
		symlink($src, $link);

		$this->removeStage($link);

		$this->assertFalse(is_link($link), 'the symlink itself is removed');
		$this->assertDirectoryExists($src, 'the linked directory survives');
		$this->assertSame('KEEP', file_get_contents($src . $ds . 'keep.txt'));
	}

	/**
	 * A missing path is a no-op (so removeStage doubles as the pre-build clean).
	 *
	 * @return  void
	 */
	public function testRemoveStageMissingPathIsNoop()
	{
		$this->removeStage($this->root . DIRECTORY_SEPARATOR . 'nope');
		$this->assertDirectoryExists($this->root);
	}

	/**
	 * A stray real file inside the staging tree is removed (defensive), and the
	 * tree with nested real dirs comes down cleanly.
	 *
	 * @return  void
	 */
	public function testRemoveStageRemovesNestedDirsAndStrayFiles()
	{
		$ds    = DIRECTORY_SEPARATOR;
		$stage = $this->root . $ds . 'X.zip.stage.' . getmypid();
		mkdir($stage . $ds . 'a' . $ds . 'b', 0775, true);
		file_put_contents($stage . $ds . 'a' . $ds . 'stray.txt', 'x');
		file_put_contents($stage . $ds . 'a' . $ds . 'b' . $ds . 'deep.txt', 'y');

		$this->removeStage($stage);

		$this->assertDirectoryDoesNotExist($stage);
	}

	/**
	 * verifyZip() accepts a complete archive and enforces the minimum entry
	 * count — the gate that lets a built bundle replace the live one.
	 *
	 * @return  void
	 */
	public function testVerifyZipAcceptsCompleteArchive()
	{
		$z = $this->root . DIRECTORY_SEPARATOR . 'good.zip';
		$a = new \ZipArchive();
		$a->open($z, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
		$a->addFromString('a.txt', 'AAA');
		$a->addFromString('b.txt', 'BBB');
		$a->close();

		$this->assertTrue($this->verifyZip($z, 1));
		$this->assertTrue($this->verifyZip($z, 2));
		$this->assertFalse($this->verifyZip($z, 3), 'rejects when fewer entries than expected');
	}

	/**
	 * A truncated / half-written archive (its central directory gone) is
	 * rejected — so an interrupted or out-of-space build never replaces the
	 * served bundle.
	 *
	 * @return  void
	 */
	public function testVerifyZipRejectsTruncatedArchive()
	{
		$z = $this->root . DIRECTORY_SEPARATOR . 'trunc.zip';
		$a = new \ZipArchive();
		$a->open($z, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
		for ($i = 0; $i < 20; $i++)
		{
			$a->addFromString('f' . $i . '.txt', str_repeat('x', 2000));
		}
		$a->close();

		// Chop the tail (end-of-central-directory + part of the central dir).
		$fh = fopen($z, 'r+');
		ftruncate($fh, max(1, filesize($z) - 1024));
		fclose($fh);

		$this->assertFalse($this->verifyZip($z, 1), 'truncated archive is rejected');
	}

	/**
	 * A missing build output is rejected (never renamed over the live bundle).
	 *
	 * @return  void
	 */
	public function testVerifyZipRejectsMissingFile()
	{
		$this->assertFalse($this->verifyZip($this->root . DIRECTORY_SEPARATOR . 'nope.zip', 1));
	}
}
