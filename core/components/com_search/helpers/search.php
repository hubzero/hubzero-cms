<?php
namespace Components\Search\Helpers;
use Components\Search\Models\Hubtype;
use stdClass;

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
