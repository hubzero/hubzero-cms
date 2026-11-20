<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Wiki\Parsermarkdown\Markdown;

/**
 * GitHub flavoured Markdown with accessible table headers
 */
class GithubMarkdown extends \cebe\markdown\GithubMarkdown
{
    use ScopedTableTrait;
}
