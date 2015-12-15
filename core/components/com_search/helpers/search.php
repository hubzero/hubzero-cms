<?php
namespace Components\Search\Helpers;

use Components\Search\Models\Hubtype;
use stdClass;
use \Solarium;


class SolrEngine
{
	public function getConfig()
	{
		$config = array('endpoint' => array(
							'localhost' => array(
							'host' => '127.0.1.1',
							'port' =>8983 ,
							'path' => '/solr/hubsearch',
							'log_path' => '/opt/solr/server/logs/solr.log')));

		return $config;
	}

	public function start()
	{
		$cmd = "/opt/solr/bin/solr start -s /srv/example/solr/data/";
		shell_exec($cmd);
	}

	public function ping()
	{
		$config = $this->getConfig();
		$solr = new Solarium\Client($config);
		$ping = $solr->createPing();
		try {
			$ping = $solr->ping($ping);
			$ping = $ping->getData();
			$alive = false;
			if (isset($ping['status']) && $ping['status'] === "OK")
			{
				$alive = true;
			}
		} catch (\Solarium\Exception $e) {
			return false;
		}
		return $alive;
	}

	/** Query Operations **/
	// Create query object
	public function search($queryString = '')
	{
		$config = $this->getConfig();
		$this->solr = new Solarium\Client($config);
		$this->query = $this->solr->createSelect();
		$this->queryString = $queryString;
		$this->query->setQuery($queryString);
		return $this;
	}
	public function addFacet($label, $facetQueryString)
	{
		$this->facetSet = $this->query->getFacetSet();
		$this->facetSet->createFacetQuery($label)->setQuery($facetQueryString);
		return $this;
	}
	public function getFacetCount($label)
	{
		$count = $this->result->getFacetSet()->getFacet($label)->getValue();
		return $count;
	}
	public function setFields($fields)
	{
		if (!isset($this->fields))
		{
			$this->fields = array();
		}
		if (is_array($fields))
		{
			$this->fields = array_unique(array_merge($this->fields, $fields));
			$this->query->setFields($this->fields);
		}
		elseif (is_string($fields))
		{
			if (strpos("," , $fields) === FALSE)
			{
				$this->fields = array($fields);
			}
			else
			{
				$this->fields = explode("," , $fields);
			}
			$this->query->setFields($this->fields);
		}
	}
	public function getResult()
	{
		$this->result = $this->solr->select($this->query);
		return $this->result;
	}
	public function limit($number = 10)
	{
		$this->query->setRows($number);
		return $this;
	}
	public function orderBy($field, $direction)
	{
		$this->query->addSort($field, $direction);
		return $this;
	}
	public function lastInsert()
	{
		$this->search('*:*');
		$this->setFields('timestamp');
		$this->limit(1);
		$this->orderBy('timestamp', 'desc');
		$result = $this->getResult();
		if (isset($result->getDocuments()[0]))
		{
			return $result->getDocuments()[0]->getFields()['timestamp'];
		}
		else
		{
			return false;
		}
	}
	/* Update */
	public function delete($id = NULL)
	{
		// @FIXME Perhaps consider using addDeleteById(1234)?
		$config = $this->getConfig();
		$this->solr = new Solarium\Client($config);

		if ($id != NULL)
		{
			$update = $this->solr->createUpdate();
			$update->addDeleteQuery('id:'.$id);
			$update->addCommit();
			$response = $this->solr->update($update);

			// @FIXME: logical fallicy
			// Wild assumption that the update was successful
			return TRUE;
		}
		else
		{
			return Lang::txt('No record specified.');
		}
	}
	public function add($document, $docID = NULL)
	{
			$config = $this->getConfig();
			$this->solr = new Solarium\Client($config);

			$update = $this->solr->createUpdate();
			$solrDoc = $update->createDocument();
			foreach ($document as $key => $value)
			{
				$solrDoc->$key = $value;
			}
			if ($docID == NULL)
			{
				$solrDoc->id = hash('md5', time()*rand());
			}
			else
			{
				$solrDoc->id = $docID;
			}
			$update->addDocuments(array($solrDoc));
			$update->addCommit();
			$this->solr->update($update);

			return true;
	}

	/* Administrative & Mantainace */
	//public function cleanup($a
	public function getLog()
	{
		$config = $this->getConfig();
		$log = Filesystem::read($config['endpoint']['localhost']['log_path']);
		$levels = array();
		$this->logs = explode("\n",$log);

		return $this;
	}
}


class SearchHelper
{
	public function fetchDataTypes($config, $type = '')
	{
		if ($type == '')
		{
			$hubTypes = HubType::all();
			$typeDescription = array();
			foreach ($hubTypes as $type)
			{
				require_once(PATH_ROOT. DS . $type->file_path);
				$classpath = $type->class_path;
				if (strpos($classpath, 'Tables') === FALSE)
				{
					$model = new $classpath;
				}
				else
				{
					$database = App::get('db');
					$model = new $classpath($database);
				}
				if (is_subclass_of($model, 'Relational'))
				{
					// Get local model fields
					$modelStructure = $type->structure();

					// Get related model fields
					$relationships = $model->introspectRelationships();

					$modelName = $type->get('type');
					// Add the related and local fields
					array_push($typeDescription, array('name' => $modelName, 'structure' => $modelStructure));
				}
			} // End foreach
		}
		else
		{
			$typeDescription = array();
			$type = HubType::all()->where('type', '=', $type)->row();
			require_once(PATH_ROOT. DS . $type->file_path);
			$classpath = $type->class_path;
			if (strpos($classpath, 'Tables') === FALSE)
			{
				$model = new $classpath;
			}
			else
			{
				$database = App::get('db');
				$model = new $classpath($database);
			}

/*			if (is_subclass_of($model, 'Hubzero\Database\Relational'))
			{
				var_dump("yessir"); die;
			}
			else
			{
				var_dump(get_parent_class($model));
				die;
			}
			*/
			// Get local model fields
			$modelStructure = $type->structure();

			// Get related model fields 
			//$relationships = $model->introspectRelationships();

			$modelName = $type->get('type');

			// Add the related and local fields
			array_push($typeDescription, array('name' => $modelName, 'structure' => $modelStructure));
		}
		return $typeDescription;
	}

	public function fetchHubTypeRows($type = '')
	{
		$type = Hubtype::all()->where('type', '=', $type)->row();

		require_once(PATH_ROOT. DS . $type->file_path);
		$classpath = $type->get('class_path');

		if (strpos($classpath, 'Tables') === FALSE)
		{
			$model = new $classpath;
		}
		else
		{
			$database = App::get('db');
			$model = new $classpath($database);
		}

		// Get local model fields
		if (get_parent_class($model) == 'Hubzero\Database\Relational')
		{
			$rows = $model->all()->rows();
		}
		elseif (get_parent_class($model) == 'Hubzero\Base\Model')
		{
			$rows = $model;
			var_dump($this->database);
			die;
			var_dump(get_class_methods($rows)); die;
		}
		elseif (get_parent_class($model) == 'JTable')
		{
			// MAJOR PERFORMANCE HIT
			$query = $database->getQuery(true);
			$query->select('*');
			$query->limit(10);
			$query->from($model->getTableName());
			$database->setQuery($query);
			$rows = $database->loadObjectList();
		}
		else
		{
			var_dump(get_parent_class($model)); die;
		}
		// Get related model fields
		// Add the related and local fields
		return $rows;
	}
}
