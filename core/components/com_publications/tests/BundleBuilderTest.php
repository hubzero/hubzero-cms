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
	 * Call the protected stripWrapper().
	 *
	 * @param   string  $entry
	 * @param   string  $name
	 * @return  string
	 */
	protected function stripWrapper($entry, $name)
	{
		$m = new \ReflectionMethod($this->builder, 'stripWrapper');
		$m->setAccessible(true);
		return $m->invoke($this->builder, $entry, $name);
	}

	/**
	 * Call the protected hasFreeSpace().
	 *
	 * @param   string   $dir
	 * @param   integer  $need
	 * @return  boolean
	 */
	protected function hasFreeSpace($dir, $need)
	{
		$m = new \ReflectionMethod($this->builder, 'hasFreeSpace');
		$m->setAccessible(true);
		return $m->invoke($this->builder, $dir, $need);
	}

	/**
	 * Call the protected linkInto().
	 *
	 * @param   string  $target
	 * @param   string  $link
	 * @return  boolean
	 */
	protected function linkInto($target, $link)
	{
		$m = new \ReflectionMethod($this->builder, 'linkInto');
		$m->setAccessible(true);
		return $m->invoke($this->builder, $target, $link, null);
	}

	/**
	 * Call the protected downloadLinkName().
	 *
	 * @param   array  $version
	 * @return  string
	 */
	protected function downloadLinkName($version)
	{
		$m = new \ReflectionMethod($this->builder, 'downloadLinkName');
		$m->setAccessible(true);
		return $m->invoke($this->builder, $version);
	}

	/**
	 * Call the protected ensureMetadata().
	 *
	 * @param   \ZipArchive  $zip
	 * @param   string       $name
	 * @param   array        $metadata
	 * @return  void
	 */
	protected function ensureMetadata($zip, $name, $metadata)
	{
		$m = new \ReflectionMethod($this->builder, 'ensureMetadata');
		$m->setAccessible(true);
		$m->invoke($this->builder, $zip, $name, $metadata);
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

	/**
	 * stripWrapper removes the "<DOI>/" prefix only when present — so modern
	 * wrapped bundles and legacy no-wrapper bundles both yield the correct
	 * relative path for the audit to match against the source set.
	 *
	 * @return  void
	 */
	public function testStripWrapperRemovesPrefixOnlyWhenPresent()
	{
		$name = '10_4231_R7FB513S';

		// Modern wrapped entry -> wrapper removed.
		$this->assertSame('data/x.csv', $this->stripWrapper($name . '/data/x.csv', $name));
		$this->assertSame('README.txt', $this->stripWrapper($name . '/README.txt', $name));

		// Legacy no-wrapper entries -> left intact (NOT first-segment-stripped).
		$this->assertSame('data/x.csv', $this->stripWrapper('data/x.csv', $name));
		$this->assertSame('media/121984_media.zip', $this->stripWrapper('media/121984_media.zip', $name));
		$this->assertSame('README.txt', $this->stripWrapper('README.txt', $name));

		// A different top dir that merely shares a prefix is not stripped.
		$this->assertSame('10_4231_R7FB513S_extra/x', $this->stripWrapper('10_4231_R7FB513S_extra/x', $name));
	}

	/**
	 * hasFreeSpace: a need of 0 fits; an impossible need does not; an
	 * unstattable path does not block the build (returns true).
	 *
	 * @return  void
	 */
	public function testHasFreeSpace()
	{
		$this->assertTrue($this->hasFreeSpace($this->root, 0), 'zero need fits');
		$this->assertFalse($this->hasFreeSpace($this->root, PHP_INT_MAX), 'impossible need rejected');
		$this->assertTrue($this->hasFreeSpace($this->root . DIRECTORY_SEPARATOR . 'no-such-dir', 1 << 20), 'unknowable free space does not block');
	}

	/**
	 * downloadLinkName matches curation::getBundleName(true): the DOI with dots
	 * and slashes turned to underscores when present, else
	 * Publication_<pubId>_<versionNumber> for a DOI-less version (the served name
	 * omits the version number; the link name keeps it so per-version links stay
	 * distinct).
	 *
	 * @return  void
	 */
	public function testDownloadLinkNameMatchesGetBundleNameTrue()
	{
		$this->assertSame(
			'10_4231_KPMA-CN98.zip',
			$this->downloadLinkName(array('doi' => '10.4231/KPMA-CN98', 'publication_id' => 5081, 'version_number' => 1))
		);

		$this->assertSame(
			'Publication_5081_3.zip',
			$this->downloadLinkName(array('doi' => '', 'publication_id' => 5081, 'version_number' => 3))
		);

		// A missing doi key behaves like an empty one.
		$this->assertSame(
			'Publication_42_1.zip',
			$this->downloadLinkName(array('publication_id' => 42, 'version_number' => 1))
		);
	}

	/**
	 * linkInto creates a hard link sharing the target's inode when none exists
	 * (the create-on-rebuild case for a bundle that had no FTP link yet).
	 *
	 * @return  void
	 */
	public function testLinkIntoCreatesHardLinkWhenMissing()
	{
		$ds     = DIRECTORY_SEPARATOR;
		$target = $this->root . $ds . 'served.zip';
		$link   = $this->root . $ds . 'ftp' . $ds . 'DOI.zip';
		mkdir(dirname($link), 0775, true);
		file_put_contents($target, 'BUNDLE');

		$this->assertTrue($this->linkInto($target, $link));
		$this->assertFileExists($link);
		$this->assertSame(fileinode($target), fileinode($link), 'link shares the target inode');
		$this->assertSame('BUNDLE', file_get_contents($link));
	}

	/**
	 * linkInto repoints a STALE link (one still pointing at a previous, different
	 * bundle) at the current target — the core fix — and leaves the old file on
	 * disk untouched (never follows or removes the target it replaces).
	 *
	 * @return  void
	 */
	public function testLinkIntoRefreshesStaleLink()
	{
		$ds   = DIRECTORY_SEPARATOR;
		$old  = $this->root . $ds . 'old.zip';
		$new  = $this->root . $ds . 'new.zip';
		$link = $this->root . $ds . 'DOI.zip';
		file_put_contents($old, 'OLD-SMALL');
		file_put_contents($new, 'NEW-COMPLETE-BUNDLE');

		// Stale: the link currently points at the old bundle.
		link($old, $link);
		$this->assertSame(fileinode($old), fileinode($link));

		$this->assertTrue($this->linkInto($new, $link));
		$this->assertSame(fileinode($new), fileinode($link), 'link now points at the new bundle');
		$this->assertSame('NEW-COMPLETE-BUNDLE', file_get_contents($link));

		// The old bundle survives, unchanged.
		$this->assertFileExists($old);
		$this->assertSame('OLD-SMALL', file_get_contents($old));

		// No temp relink artifact left behind.
		$this->assertCount(0, glob($this->root . $ds . '*.relink.*'));
	}

	/**
	 * linkInto is a no-op when the link already shares the target inode, so it is
	 * safe to call on every build, and it leaves no temp artifact.
	 *
	 * @return  void
	 */
	public function testLinkIntoNoopWhenAlreadyLinked()
	{
		$ds     = DIRECTORY_SEPARATOR;
		$target = $this->root . $ds . 'served.zip';
		$link   = $this->root . $ds . 'DOI.zip';
		file_put_contents($target, 'BUNDLE');
		link($target, $link);
		$ino = fileinode($target);

		$this->assertTrue($this->linkInto($target, $link));
		$this->assertSame($ino, fileinode($link), 'still the same inode');
		$this->assertCount(0, glob($this->root . $ds . '*.relink.*'), 'no temp created for a no-op');
	}

	/**
	 * A missing target is rejected and creates nothing — the FTP link is only
	 * ever pointed at a bundle that actually exists.
	 *
	 * @return  void
	 */
	public function testLinkIntoMissingTargetFails()
	{
		$ds   = DIRECTORY_SEPARATOR;
		$link = $this->root . $ds . 'DOI.zip';

		$this->assertFalse($this->linkInto($this->root . $ds . 'nope.zip', $link));
		$this->assertFileDoesNotExist($link);
	}

	/**
	 * ensureMetadata REFRESHES the archival readme: on a rebuild it replaces a
	 * stale hubREADME.txt entry with the freshly regenerated one (never keeps the
	 * old copy, never duplicates it), while leaving an author-supplied README.txt
	 * payload untouched and keeping an already-present gallery as-is (backfill
	 * only). This is what stops a version built off-request from shipping a
	 * stale draft readme (ticket 2787 / pub 5149).
	 *
	 * @return  void
	 */
	public function testEnsureMetadataRefreshesReadmeButKeepsPayload()
	{
		$ds   = DIRECTORY_SEPARATOR;
		$name = '10_4231_ABCD-1234';
		$z    = $this->root . $ds . 'outer.zip';

		$a = new \ZipArchive();
		$a->open($z, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
		$a->addFromString($name . '/hubREADME.txt', 'STALE ARCHIVAL README');
		$a->addFromString($name . '/README.txt', 'AUTHOR PAYLOAD');   // author's own file, not archival
		$a->addFromString($name . '/gallery/pic.png', 'ORIGINAL IMG');
		$a->close();

		// The freshly regenerated archival readme + a (would-be) gallery source.
		$fresh  = $this->root . $ds . 'hubREADME.txt';
		file_put_contents($fresh, 'FRESH ARCHIVAL README');
		$galSrc = $this->root . $ds . 'pic.png';
		file_put_contents($galSrc, 'NEW IMG');

		$a = new \ZipArchive();
		$a->open($z);
		$this->ensureMetadata($a, $name, array(
			$fresh  => array('readme',  $name . '/hubREADME.txt'),
			$galSrc => array('gallery', $name . '/gallery/pic.png'),
		));
		$a->close();

		$a = new \ZipArchive();
		$a->open($z);
		$this->assertSame('FRESH ARCHIVAL README', $a->getFromName($name . '/hubREADME.txt'), 'archival readme refreshed');
		$this->assertSame('AUTHOR PAYLOAD', $a->getFromName($name . '/README.txt'), 'author payload README.txt untouched');
		$this->assertSame('ORIGINAL IMG', $a->getFromName($name . '/gallery/pic.png'), 'gallery kept, not overwritten (backfill only)');

		$count = 0;
		for ($i = 0; $i < $a->numFiles; $i++)
		{
			if ($a->getNameIndex($i) === $name . '/hubREADME.txt')
			{
				$count++;
			}
		}
		$a->close();
		$this->assertSame(1, $count, 'exactly one archival readme entry (refreshed, not duplicated)');
	}
}
