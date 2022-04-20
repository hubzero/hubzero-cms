<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command;

use Hubzero\Console\Output;
use Hubzero\Console\Arguments;

require_once Component::path('com_resources') . '/models/entry.php';
use Components\Resources\Models\Entry;

/**
 * Resources command class
 **/
class Resources extends Base implements CommandInterface
{
	/**
	 * Resources object
	 *
	 * @var  object
	 **/
	// private $resources;

	/**
	 * Constructor - sets output mechanism and arguments for use by command
	 *
	 * @param   \Hubzero\Console\Output    $output     The ouput renderer
	 * @param   \Hubzero\Console\Arguments $arguments  The command arguments
	 * @return  void
	 **/
	public function __construct(Output $output, Arguments $arguments)
	{
		parent::__construct($output, $arguments);
	}

	/**
	 * Default (required) command - just executes run
	 *
	 * @return  void
	 **/
	public function execute()
	{
		$this->output = $this->output->getHelpOutput();
		$this->help();
		$this->output->render();
		return;
	}

	/**
	 * Run Gitstats generation for tools with git repos
	 *
	 * @return  void
	 */
	public function gitstats()
	{
		$entries = Entry::all()
					->whereEquals('standalone', 1)
					->whereEquals('type', 7)
					->ordered()
					->rows();

		foreach ($entries as $entry)
		{
			// Determine if entry/resource has a git repo on the host in /opt/git or /opt/gitExternal

			if (is_dir("/opt/gitExternal/tools/" . $entry->alias))
			{
				$repopath = "/opt/gitExternal/tools/" . $entry->alias;
				// file_put_contents('/var/log/muselog', "/opt/gitExternal/tools/" . $entry->alias . "\n", FILE_APPEND | LOCK_EX);
			}
			else if (is_dir("/opt/git/tools/" . $entry->alias . ".git"))
			{
				$repopath = "/opt/git/tools/" . $entry->alias . ".git";
				// file_put_contents('/var/log/muselog', "/opt/git/tools/" . $entry->alias . ".git" . "\n", FILE_APPEND | LOCK_EX);
			}
			$statsfullpath = $entry->basepath() . DS . $entry->relativepath() . DS . 'stats/';
			// if ($repopath)
			// {
			// 	$statsfullpath = $entry->basepath() . DS . $entry->relativepath() . DS . 'stats/';
			// 	if (!file_exists($statsfullpath)) {
			// 		mkdir($statsfullpath, 0777, true);
			// 	}
			// 	$command  = 'gitstats -c authors_top=10 -c max_authors=10 ' $repopath . ' ' . $statsfullpath . ' 2>&1';
			// 	$response = shell_exec($command);
				
			// }

			file_put_contents('/var/log/muselog', serialize($statsfullpath) . "\n", FILE_APPEND | LOCK_EX);
		}
		exit;
	}

	/**
	 * Run Export to CSV
	 *
	 * @return  void
	 */
	public function exportcsv()
	{
		$skip = array('password', 'params', 'usertype');
		$keys = array();
		$tags = array();
		$records = array();

		// $members = Member::blank();
		$entries = Entry::all()->whereEquals('standalone', 1);
		$attribs = $entries->getStructure()->getTableColumns($entries->getTableName());

		foreach ($attribs as $key => $desc)
		{
			if (in_array(strtolower($key), $skip))
			{
				continue;
			}

			$keys[$key] = $key;
		}
		array_push($keys, 'tags');
		array_push($keys, 'children');
		array_push($keys, 'authors');
		
		$rows = Entry::all()
			->ordered()
			->rows();

		$path = Config::get('tmp_path') . DS . 'resources_export_' . date("Ymd") . '.csv';
		$file = fopen($path, 'w');
		fputcsv($file, $keys, ',');

		foreach ($rows as $row)
		{
			$record = array();
			

			foreach ($keys as $key)
			{
				if ($key == 'tags')
				{
					$val = implode(",", $row->tags());
					$record[$key] = $val;
				}
				else if ($key == 'introtext')
				{
					$val = htmlspecialchars($row->get($key), ENT_COMPAT | ENT_XML1, 'UTF-8');
					$record[$key] = strip_tags($val);
				}
				else if ($key == 'fulltxt')
				{
					$val = htmlspecialchars($row->get($key), ENT_COMPAT | ENT_XML1, 'UTF-8');
					$record[$key] = strip_tags($val);
				}
				else if ($key == 'created_by')
				{
					$val = User::getInstance($row->created_by)->get('username');
					$record[$key] = $val;
				}
				else if ($key == 'modified_by')
				{
					$val = User::getInstance($row->modified_by)->get('username');
					$record[$key] = $val;
				}
				else if ($key == 'authors')
				{
					$authnames = $row->authors()->ordered()->rows();
					$authors_array = array();
					foreach ($authnames as $author)
					{
						array_push($authors_array, User::getInstance($author->authorid)->get('username'));
					}
					$val = implode(",", $authors_array);
					$record[$key] = $val;

				}
				else if ($key == 'type')
				{
					$val = $row->type->type;
					$record[$key] = $val;
				}
				else if ($key == 'children')
				{
					$children = Entry::oneOrFail($row->get('id'))->children()->rows();
					$children_array = array();
					foreach ($children as $child)
					{
						array_push($children_array,$child->id);
					}
					$val = implode(",", $children_array);
					$record[$key] = $val;
				}
				else
				{
					$val = $row->get($key);
					$record[$key] = $val;
				}
			}

			fputcsv($file, $record, ',');
		}
		fclose($file);
		exit;
	}


	/**
	 * Run Export to XML
	 *
	 * @return  void
	 */
	public function exportxml()
	{
		$skip = array('password', 'params', 'usertype');
		$keys = array();
		$tags = array();

		// $members = Member::blank();
		$entries = Entry::all()->whereEquals('standalone', 1);
		$attribs = $entries->getStructure()->getTableColumns($entries->getTableName());

		foreach ($attribs as $key => $desc)
		{
			if (in_array(strtolower($key), $skip))
			{
				continue;
			}

			$keys[$key] = $key;
		}
		array_push($keys, 'tags');
		array_push($keys, 'children');
		array_push($keys, 'authors');
		
		$rows = Entry::all()
			->ordered()
			->rows();

		// Create the root node.
		$xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><publications xsi:noNamespaceSchemaLocation="schema.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"></publications>');
		$i=0;
		foreach ($rows as $row)
		{
			$pub_node = $xml->addChild('publication');

			if ($row->get('standalone') == 0)
			{
				continue;
			}
			// title
			$val = htmlspecialchars($row->get('title'), ENT_COMPAT | ENT_XML1, 'UTF-8');
			$pub_node->addChild('title', strip_tags($val));
			// synopsis
			$val = htmlspecialchars($row->get('introtext'), ENT_COMPAT | ENT_XML1, 'UTF-8');
			$pub_node->addChild('synopsis', strip_tags($val));
			// abstract
			$val = htmlspecialchars($row->get('fulltxt'), ENT_COMPAT | ENT_XML1, 'UTF-8');
			$pub_node->addChild('abstract', strip_tags($val));
			// version
			$pub_node->addChild('version');
			// category
			$val = $row->type->type;
			$pub_node->addChild('category', $val);
			// notes
			$pub_node->addChild('notes');
			// content
			$content_node = $pub_node->addChild('content');
			$file_node = $content_node->addChild('file');
			$file_node->addChild('path');
			$file_node->addChild('title');
			// supportingmaterials
			$supportingmaterials_node = $pub_node->addChild('supportingmaterials');
			$file_node = $supportingmaterials_node->addChild('file');
			$file_node->addChild('path');
			$file_node->addChild('title');
			// gallery
			$gallery_node = $pub_node->addChild('gallery');
			$file_node = $gallery_node->addChild('file');
			$file_node->addChild('path');
			$file_node->addChild('title');
			// authors
			$authors_node = $pub_node->addChild('authors');
			if (count($row->authors()->ordered()->rows()) > 0) {
				$authnames = $row->authors()->ordered()->rows();
				foreach ($authnames as $author)
				{
					$author_node = $authors_node->addChild('author');
					$author_name = explode(" ", trim(User::getInstance($author->authorid)->get('name')));
					$author_node->addChild('firstname', $author_name[0]);
					array_shift($author_name);
					$author_node->addChild('lastname', implode($author_name));
					$author_node->addChild('organization', User::getInstance($author->authorid)->get('organization'));
				}
			} else {
				$author_node = $authors_node->addChild('author');
				$author_node->addChild('firstname');
				$author_node->addChild('lastname');
				$author_node->addChild('organization');
			}
			// tags
			$tag_node = $pub_node->addChild('tags');
			if (count($row->tags()) > 0) {
				foreach ($row->tags() as $tag)
				{
					$tag_node->addChild('tag', $tag);
				}
			} else {
				$tag_node->addChild('tag');
			}
			// license
			$val = $row->get('license');
			// $pub_node->addChild('license', $val);
			$pub_node->addChild('license', 'cc0');
			// citations
			$citations_node = $pub_node->addChild('citations');
			$citations_node->addChild('citation');


				// }
				// else if ($key == 'children')
				// {
				// 	$children = Entry::oneOrFail($row->get('id'))->children()->rows();
				// 	$children_array = array();
				// 	foreach ($children as $child)
				// 	{
				// 		array_push($children_array,$child->id);
				// 	}
				// 	$val = implode(",", $children_array);
				// }
				// else
				// {
				// 	$val = $row->get($key);
				// 	$pub_node->addChild($key, $val);
				// }
			// }

			if ($i >= 1)
			{
				break;
			}
			$i++;
		}
		$xml->asXML(Config::get('tmp_path') . DS . 'resources_export_' . date("Ymd") . '.xml');
		exit;
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
				'Resource component export commands.'
			);
	}
}
