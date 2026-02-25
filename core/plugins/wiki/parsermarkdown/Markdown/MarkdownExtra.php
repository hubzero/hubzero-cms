<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Wiki\Parsermarkdown\Markdown;

/**
 * Markdown Extra with accessible table headers
 */
class MarkdownExtra extends \cebe\markdown\MarkdownExtra
{
    use ScopedTableTrait;
}
