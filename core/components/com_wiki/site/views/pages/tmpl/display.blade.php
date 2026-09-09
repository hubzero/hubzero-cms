{{--
 * Wiki page display — router to default or static layout
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@if($page->isStatic())
    {!! $__view->loadTemplate('static') !!}
@else
    {!! $__view->loadTemplate('default') !!}
@endif
