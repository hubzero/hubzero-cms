<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die( 'Restricted access');

$authors = $this->citation->authors();

if (count($authors)) { ?>
	<?php foreach ($authors as $author) { ?>
		<p class="citation-author" id="author_<?php echo $this->escape($author->id); ?>">
			<span class="author-handle">
			</span>
			<span class="author-name">
				<?php echo $this->escape($author->author); ?>
			</span>
			<span class="author-description">
				<a class="delete" data-id="<?php echo $this->escape($author->id); ?>" href="<?php echo Route::url('index.php?option=com_citations&controller=authors&task=remove&citation=' . $this->citation->id . '&author=' . $author->id . '&' . Session::getFormToken() . '=1'); ?>">
					<?php echo Lang::txt('JACTION_DELETE'); ?>
				</a>
			</span>
		</p>
	<?php } ?>
<?php }