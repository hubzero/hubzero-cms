{{--
 * Wiki page display — special page router
 *
 * Delegates to the specific special page template in views/special/tmpl/.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

{!! $__view->view(strtolower($layout), 'special')
    ->set('option', $option)
    ->set('controller', $controller)
    ->set('page', $page)
    ->set('task', $task)
    ->set('sub', $sub)
    ->set('book', $book)
    ->loadTemplate() !!}
