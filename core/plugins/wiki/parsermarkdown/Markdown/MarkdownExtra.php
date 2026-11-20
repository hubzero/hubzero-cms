<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
