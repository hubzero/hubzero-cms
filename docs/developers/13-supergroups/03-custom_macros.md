<!--
status: imported
source: https://help.hubzero.org/documentation/240/webdevs/supergroups/custom_macros
source-id: 3522
modified: 2014-09-10
imported: 2026-09-09
-->
# Custom Macros

## Overview

Super groups have the ability to create their own custom macros or override any existing macro `[[MacroName(args)]]`.

## Custom Macro Class Structure

```php
<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see .
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace PluginsContentFormathtmlMacros;

use PluginsContentFormathtmlMacro;

/**
 * Wiki macro class for displaying hello world
 */
class {{macro_name}} extends Macro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['html'] = 'Put macro description here...';
		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return     string
	 */
	public function render()
	{
		return 'Return any html content you want this macro to render';
	}
}
```

## Overriding Macros

To override a macro, copy the original macro located in `/{web_root}/plugins/content/formathtml/macros/` into your groups macro folder `/{web_root}/site/groups/{group_id}/macros/`. You can no modify the render functionality, add or remove params, etc.

> **Warning:** Note: The Macos class name must remain the same and the class must implement a render() method. You are free to add or chanage other methods.

> **Warning:** Note: If the original macro file is located within a subfolder, you must recreate that folder structure in the groups macros folder for the override to work.
