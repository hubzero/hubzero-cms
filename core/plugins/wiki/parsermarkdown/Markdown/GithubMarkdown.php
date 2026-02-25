<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
