{{--
  Comment list partial — NOT USED by Blade templates.

  The legacy PHP templates used mutual recursion between _list.php and
  _comment.php: entry.php called _list, which iterated comments and called
  _comment for each one, and _comment called _list again for replies.

  In the Blade rewrite, _comment.blade.php recurses directly via
  $__view->view('_comment')->set(...)->loadTemplate(), eliminating the
  need for a separate list wrapper. entry.blade.php iterates top-level
  comments inline with @foreach.

  This file exists only to document the change and prevent confusion
  when comparing legacy and Blade templates side by side.

  @see    _comment.blade.php  Recursive comment partial (handles replies directly)
  @see    entry.blade.php     Iterates top-level comments via @foreach

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
