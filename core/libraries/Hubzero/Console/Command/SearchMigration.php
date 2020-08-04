<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command;

use Hubzero\Console\Output;
use Hubzero\Console\Arguments;
use Components\Search\Models\Solr\SearchComponent;

require_once Component::path('com_search') . '/models/solr/searchcomponent.php';

/**
 * Migration class
 **/
class SearchMigration extends Base implements CommandInterface
{
	/**
	 * Default (required) command - just executes run
	 *
	 * @return  void
	 **/
	public function execute()
	{
		$this->run();
	}

	/**
	 * Run migration
	 *
	 * @museDescription  Adds components to solr index
	 *
	 * @return  void
	 **/
	public function run()
	{
		$newComponents = SearchComponent::getNewComponents();
		$newComponents->save();
		$components = SearchComponent::all();
		if (!$this->arguments->getOpt('all'))
		{
			$componentArgs = array();
			if ($this->arguments->getOpt('components'))
			{
				$componentArgs = explode(',', $this->arguments->getOpt('components'));
				$componentArgs = array_map('trim', $componentArgs);
			}

			if (empty($componentArgs))
			{
				$this->output->error('Error: No components specified.');
			}
			else
			{
				$components = $components->whereIn('name', $componentArgs);
			}
		}

		if (!$this->arguments->getOpt('rebuild'))
		{
			$components = $components->whereEquals('state', 0);
		}
		$url = $this->arguments->getOpt('url');
		if (empty($url))
		{
			$this->output->error('Error: no URL provided.');
		}
		foreach ($components as $compObj)
		{
			$offset = 0;
			$batchSize = $compObj->getBatchSize();
			$batchNum = 1;
			$compName = ucfirst($compObj->get('name'));
			$startMessage = 'Indexing ' . $compName . '...' . PHP_EOL;
			$this->output->addLine($startMessage);
			while ($indexResults = $compObj->indexSearchResults($offset, $url))
			{
				$batchMessage = 'Indexed ' . $compName . ' batch ' . $batchNum . ' of ' . $batchSize . PHP_EOL;
				$this->output->addLine($batchMessage);
				$offset = $indexResults['offset'];
				$batchNum++;
			}
			if ($compObj->state != 1)
			{
				$compObj->set('state', 1);
				$compObj->save();
			}
		}
	}

	/**
	 * Output help documentation
	 *
	 * @return  void
	 **/
	public function help()
	{
		$this
			->output
			->addOverview(
				'Run a solr search migration. Searches for and indexes models with the searcable interface.'
			)
			->addTasks($this)
			->addArgument(
				'-url: the base url of the site being indexed.',
				'Example: -url=\'https://localhost\''
			)
			->addArgument(
				'-components: component(s) that should be indexed.',
				'If multiple, separate each component name with a comma.',
				'Example: -components=\'blog, content, kb, resources\''
			)
			->addArgument(
				'--all: index all searchable components',
				'Any component that contains a model that implements Searchable will be added to the solr index.',
				'Example: --all'
			)
			->addArgument(
				'--rebuild: include components that have already had a full index run previously.',
				'this will overwrite any existing search documents with a new version.',
				'example: --rebuild'
			);
	}
}
