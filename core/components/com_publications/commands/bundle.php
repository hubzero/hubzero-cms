<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Commands;

use Hubzero\Console\Command\Base;
use Hubzero\Console\Command\CommandInterface;
use Components\Publications\Models\BundleBuilder;
use Components\Publications\Models\BundleQueue;

require_once dirname(__DIR__) . DS . 'models' . DS . 'bundlebuilder.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'bundlequeue.php';

/**
 * Build publication download bundles (core; replaces the PURR-only
 * app/bin/rebuild-publication-bundle).
 **/
class Bundle extends Base implements CommandInterface
{
	/**
	 * Default — show help.
	 *
	 * @return  void
	 **/
	public function execute()
	{
		$this->help();
	}

	/**
	 * Build a version's bundle.
	 *
	 * With --queue, this is the worker the async dispatcher spawns: it claims
	 * the queued row (skipping if already building/claimed) and records the
	 * ready/failed result. Without --queue it is a direct manual build (the
	 * replacement for app/bin/rebuild-publication-bundle); pass --force to
	 * rebuild over an existing bundle.
	 *
	 * @museDescription  Build a publication version's download bundle
	 * @return  void
	 **/
	public function build()
	{
		$versionId = (int) $this->arguments->getOpt('version');

		if (!$versionId)
		{
			$this->output->error('A version id is required: --version=<publication_version_id>');
			return;
		}

		$queued = (bool) $this->arguments->getOpt('queue');
		$force  = (bool) $this->arguments->getOpt('force');
		$level  = $this->arguments->getOpt('level');

		$entry = null;

		if ($queued)
		{
			$entry = BundleQueue::forVersion($versionId);

			if (!$entry || !$entry->get('id') || !$entry->claim())
			{
				// Not queued, or another worker won the claim. Nothing to do.
				$this->output->addLine('Nothing to build for version ' . $versionId . ' (not queued or already building).');
				return;
			}

			$force = true; // a queued (re)build always produces a fresh bundle
		}

		$builder = new BundleBuilder();
		$result  = $builder->build($versionId, array(
			'force' => $force,
			'level' => ($level === false ? null : (int) $level),
			'log'   => function ($m) { $this->output->addLine($m); },
		));

		if ($queued && $entry)
		{
			if ($result['ok'])
			{
				$entry->markReady($result['file'], $result['size'], $result['source_hash']);
			}
			else
			{
				$entry->markFailed($result['error']);
			}
		}

		if ($result['ok'])
		{
			$this->output->addLine(sprintf('Bundle ready: %s (%s bytes)', $result['file'], number_format($result['size'])), 'success');
		}
		else
		{
			$this->output->error($result['error']);
		}
	}

	/**
	 * Queue a version for (re)building by the async worker. Use this to repair
	 * a bundle discovered to be wrong, or to pre-warm a build.
	 *
	 * @museDescription  Queue a publication version's bundle for building
	 * @return  void
	 **/
	public function enqueue()
	{
		$versionId = (int) $this->arguments->getOpt('version');

		if (!$versionId)
		{
			$this->output->error('A version id is required: --version=<publication_version_id>');
			return;
		}

		if (BundleQueue::enqueueVersion($versionId))
		{
			$this->output->addLine('Queued version ' . $versionId . ' for bundle build.', 'success');
		}
		else
		{
			$this->output->error('Could not queue version ' . $versionId . '.');
		}
	}

	/**
	 * List the bundle queue.
	 *
	 * @museDescription  List the bundle build queue
	 * @return  void
	 **/
	public function list()
	{
		$rows = array(array('Version', 'Status', 'Attempts', 'Size', 'Built', 'Last error'));

		foreach (BundleQueue::all()->order('queued_at', 'desc')->rows() as $row)
		{
			$rows[] = array(
				$row->get('publication_version_id'),
				$row->get('status'),
				$row->get('attempts') . '/' . $row->get('max_attempts'),
				$row->get('bundle_size') ? number_format($row->get('bundle_size')) : '-',
				$row->get('built_at') ?: '-',
				\Hubzero\Utility\Str::truncate((string) $row->get('last_error'), 40),
			);
		}

		$this->output->addTable($rows, true);
	}

	/**
	 * Show one version's bundle status.
	 *
	 * @museDescription  Show a version's bundle status
	 * @return  void
	 **/
	public function status()
	{
		$versionId = (int) $this->arguments->getOpt('version');

		if (!$versionId)
		{
			$this->output->error('A version id is required: --version=<publication_version_id>');
			return;
		}

		$row = BundleQueue::forVersion($versionId);

		if (!$row || !$row->get('id'))
		{
			$this->output->addLine('No bundle queue entry for version ' . $versionId . '.');
			return;
		}

		foreach (array('status', 'attempts', 'max_attempts', 'source_hash', 'bundle_file', 'bundle_size', 'queued_at', 'last_attempt_at', 'built_at', 'last_error') as $k)
		{
			$this->output->addLine(sprintf('%-16s %s', $k . ':', $row->get($k)));
		}
	}

	/**
	 * Help.
	 *
	 * @return  void
	 **/
	public function help()
	{
		$this->output
			->getHelpOutput()
			->addOverview('Build publication download bundles.')
			->addTasks($this)
			->addArgument('--version=<id>', 'Publication version id.', 'Example: --version=4333')
			->addArgument('--force', 'Rebuild even if a bundle already exists.', '')
			->addArgument('--queue', 'Worker mode: claim the queued row and record the result.', '')
			->addArgument('--level=<0-9>', 'zip compression level for the inner bundle (default 1).', '')
			->render();
	}
}
