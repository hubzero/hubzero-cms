<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Models\Orm\Zone;

use Components\Tools\Helpers\Iprange;
use Hubzero\Database\Relational;
use Hubzero\Facades\Lang;

/**
 * Tool zone location model
 *
 * @uses \Hubzero\Database\Relational
 */
class Location extends Relational
{
    /**
     * The table namespace
     *
     * @var string
     */
    protected $namespace = 'zone';

    /**
     * The table to which the class pertains
     *
     * This will default to #__{namespace}_{modelName} unless otherwise
     * overwritten by a given subclass. Definition of this property likely
     * indicates some derivation from standard naming conventions.
     *
     * @var  string
     **/
    protected $table = 'zone_locations';

    /**
     * Fields and their validation criteria
     *
     * @var  array
     **/
    protected $rules = [
        'zone_id' => 'positive|nonzero'
    ];

    /**
     * Automatic fields, set on every save; automaticIpFrom() sets ipTO too
     *
     * @var  array
     **/
    public $always = [
        'ipFROM'
    ];

    /**
     * Sets up additional custom rules
     *
     * @return  void
     */
    public function setup()
    {
        $this->addRule('state', function ($data) {
            $data['state'] = strtolower($data['state']);

            return (in_array($data['state'], ['up', 'down']) ? false : Lang::txt('Invalid state provided.'));
        });

        // No range at all is allowed, as by the table class: a location by
        // country or continent only
        $this->addRule('ipFROM', function ($data) {
            $from = isset($data['ipFROM']) ? $data['ipFROM'] : '';
            $to   = isset($data['ipTO']) ? $data['ipTO'] : '';

            return (Iprange::fromInput($from, $to) === false ? Lang::txt('Invalid IP address or range') : false);
        });
    }

    /**
     * Generates automatic ipFROM field value, and sets ipTO with it
     *
     * The range is read from both fields at once: a network entered in
     * ipFROM gives ipTO its end. The validation rule has already refused
     * anything that is not an address or network.
     *
     * @param   array   $data  the data being saved
     * @return  string
     */
    public function automaticIpFrom($data)
    {
        $from  = isset($data['ipFROM']) ? $data['ipFROM'] : '';
        $to    = isset($data['ipTO']) ? $data['ipTO'] : '';
        $range = Iprange::fromInput($from, $to);

        if ($range === false) {
            return $from;
        }

        $this->set('ipTO', $range[1]);

        return $range[0];
    }

    /**
     * Defines a belongs to one relationship between location and zone
     *
     * @return  object
     */
    public function zone()
    {
        return $this->belongsToOne('Components\Tools\Models\Orm\Zone', 'zone_id');
    }
}
