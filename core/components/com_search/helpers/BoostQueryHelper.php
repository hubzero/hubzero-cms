<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Helpers;

use Hubzero\Utility\Arr;

class BoostQueryHelper
{
    /**
     * @var  mixed
     */
    protected $boostOrm;

    /**
     * @var  mixed
     */
    protected $queriesFactory;

    /**
     * @var  mixed
     */
    protected $boosts;

    /**
     * @param   array<mixed>  $args
     */
    public function __construct($args = [])
    {
        $this->boostOrm = Arr::getValue(
            $args,
            'boosts',
            new MockProxy([
                'class' => 'Components\Search\Models\Solr\Boost',
            ]),
        );
        $this->queriesFactory = Arr::getValue(
            $args,
            'queries',
            new MockProxy([
                'class' => 'Components\Search\Helpers\SolariumBoostQueries',
            ]),
        );
    }

    /**
     * @return  mixed
     */
    public function getAllQueries()
    {
        $boosts = $this->boostOrm->all();

        return $this->queriesFactory->one(['boosts' => $boosts]);
    }
}
