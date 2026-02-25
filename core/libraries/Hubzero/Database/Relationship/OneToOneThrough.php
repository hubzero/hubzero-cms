<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Relationship;

use Hubzero\Facades\User;

/**
 * Database one to one through relationship
 *
 * This relationship retrieves a single related model through an intermediate table.
 * It is similar to OneToManyThrough but returns a single model instead of a collection.
 *
 * Example use case:
 * - User has one Country through Profile
 * - User -> Profile -> Country
 *
 * ```php
 * // In User model
 * public function country()
 * {
 *     return $this->oneToOneThrough(Country::class, Profile::class, 'country_id', 'user_id');
 * }
 *
 * // Usage
 * $user = User::oneOrFail(1);
 * $country = $user->country;  // Returns single Country model
 * ```
 *
 * The SQL generated is similar to:
 * SELECT countries.* FROM countries
 * INNER JOIN profiles ON countries.id = profiles.country_id
 * WHERE profiles.user_id = ?
 * LIMIT 1
 */
class OneToOneThrough extends Relationship
{
    /**
     * The intermediate table used to capture the through relationship
     *
     * @var  string
     **/
    protected $throughTable = null;

    /**
     * Key on the intermediate table that links to the local model
     *
     * @var  string
     **/
    protected $throughLocalKey = null;

    /**
     * Key on the intermediate table that links to the related model
     *
     * @var  string
     **/
    protected $throughRelatedKey = null;

    /**
     * Constructs a new object instance
     *
     * @param   \Hubzero\Database\Relational|static  $model             The local model
     * @param   \Hubzero\Database\Relational|static  $related           The related model
     * @param   string                               $throughTable      The intermediate table name
     * @param   string  $throughLocalKey   The key on intermediate
     *                                      table linking to local model
     * @param   string  $throughRelatedKey The key on intermediate
     *                                      table linking to related model
     * @return  void
     **/
    public function __construct($model, $related, $throughTable, $throughLocalKey, $throughRelatedKey)
    {
        parent::__construct($model, $related, $model->getPrimaryKey(), $related->getPrimaryKey());

        $this->throughTable      = $throughTable;
        $this->throughLocalKey   = $throughLocalKey;
        $this->throughRelatedKey = $throughRelatedKey;
    }

    /**
     * Fetch results of relationship
     *
     * Returns a single model (overrides base to ensure single result)
     *
     * @return  \Hubzero\Database\Relational
     **/
    public function rows()
    {
        return $this->constrain()->row();
    }

    /**
     * Loads the relationship content and returns the related side of the model
     *
     * @return  object
     **/
    public function constrain()
    {
        $this->mediate();

        $this->related->whereEquals(
            $this->throughTable . '.' . $this->throughLocalKey,
            $this->model->getPkValue()
        );

        // Apply any default conditions
        return $this->applyDefaultConditions($this->related);
    }

    /**
     * Get keys based on a given constraint
     *
     * @param   closure  $constraint  The constraint function to apply
     * @return  array
     **/
    public function getConstrainedKeys($constraint)
    {
        $this->mediate();

        return array_unique($this->getConstrained($constraint)->fieldsByKey($this->throughLocalKey));
    }

    /**
     * Joins the intermediate and related tables together to the model for the pending query
     *
     * @return  $this
     **/
    public function join()
    {
        // We do a left outer join here because we're not trying to limit the primary table's results
        // This function is primarily used when needing to sort by a field in the joined table
        $this->model->select($this->model->getQualifiedFieldName('*'))
                    ->select($this->related->getQualifiedFieldName('*'))
                    ->join(
                        $this->throughTable,
                        $this->model->getQualifiedFieldName($this->localKey),
                        $this->throughLocalKey,
                        'LEFT OUTER'
                    )
                    ->join(
                        $this->related->getTableName(),
                        $this->throughRelatedKey,
                        $this->related->getQualifiedFieldName($this->relatedKey),
                        'LEFT OUTER'
                    );

        return $this;
    }

    /**
     * Joins the related table together with the intermediate table for the pending query
     *
     * This is primarily used when we're getting the related results and we need to work
     * our way backwards through the intermediate table.
     *
     * @return  $this
     **/
    public function mediate()
    {
        $this->related->select($this->related->getQualifiedFieldName('*'))
                      ->select($this->throughLocalKey)
                      ->join(
                          $this->throughTable,
                          $this->related->getQualifiedFieldName($this->relatedKey),
                          $this->throughRelatedKey
                      );

        return $this;
    }

    /**
     * Gets the relations that will be seeded on to the provided rows
     *
     * @param   array    $keys        The keys for which to fetch related items
     * @param   closure  $constraint  The constraint function to limit related items
     * @return  array
     **/
    protected function getRelations($keys, $constraint = null)
    {
        $this->mediate();

        // Apply default conditions first
        $this->applyDefaultConditions($this->related);

        if (isset($constraint)) {
            call_user_func_array($constraint, array($this->related));
        }

        return $this->related->whereIn($this->throughTable . '.' . $this->throughLocalKey, array_unique($keys));
    }

    /**
     * Sorts the relations into arrays keyed by the related key
     *
     * For OneToOneThrough, we store single models instead of collections
     *
     * @param   array  $relations  The relations to sort
     * @return  array
     **/
    protected function getResultsByRelatedKey($relations)
    {
        $resultsByRelatedKey = [];

        foreach ($relations as $relation) {
            // For one-to-one, we only keep the first result for each key
            if (!isset($resultsByRelatedKey[$relation->{$this->throughLocalKey}])) {
                $resultsByRelatedKey[$relation->{$this->throughLocalKey}] = $relation;
            }
        }

        return $resultsByRelatedKey;
    }

    /**
     * Seeds the given rows with the relationship data
     *
     * @param   \Hubzero\Database\Rows  $rows        The rows to seed
     * @param   mixed                   $data        Pre-fetched data or null
     * @param   string                  $name        The relationship name
     * @return  \Hubzero\Database\Rows
     **/
    protected function seed($rows, $data, $name)
    {
        // For one-to-one through, we seed single models not collections
        foreach ($rows as $row) {
            $key = $row->{$this->localKey};
            if (isset($data[$key])) {
                $row->addRelationship($name, $data[$key]);
            } else {
                $related = $this->related;
                $row->addRelationship($name, $related::blank());
            }
        }

        return $rows;
    }
}
