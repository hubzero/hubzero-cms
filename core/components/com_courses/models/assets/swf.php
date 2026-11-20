<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models\Assets;

/**
 * Video Asset handler class
 */
class Swf extends File
{
    /**
     * Class info
     *
     * Action message - what the user will see if presented with multiple handlers for this extension
     * Responds to    - what extensions this handler responds to
     *
     * @var array<mixed>
     **/
    protected static $info = [
        'action_message' => 'As an Adobe Shockwave Flash file',
        'responds_to'    => ['swf'],
    ];

    /**
     * Create method for this handler
     *
     * @param   mixed  $localPath
     * @return array<mixed> of assets created
     **/
    public function create($localPath = null)
    {
        // Set the asset type to video
        $this->asset['type']    = 'file';
        $this->asset['subtype'] = 'swf';

        // Call the primary create method on the file asset handler
        return parent::create($localPath);
    }
}
