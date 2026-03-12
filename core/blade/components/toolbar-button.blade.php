{{--
  Toolbar button — registers a single Toolbar:: button.

  Emits no HTML. Maps the task prop to the appropriate Toolbar:: method.
  Use inside <x-admin-toolbar> slots or directly in any view context.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@props([
    'task'    => '',
    'text'    => '',
    'icon'    => '',
    'confirm' => '',
    'list'    => false,
    'option'  => '',
])

@php
  use Hubzero\Facades\Toolbar;

  switch ($task) {
      case 'add':
          Toolbar::addNew($task, $text);
          break;
      case 'edit':
          Toolbar::editList($task, $text);
          break;
      case 'delete':
      case 'remove':
          Toolbar::deleteList($confirm, $task, $text ?: 'JTOOLBAR_DELETE');
          break;
      case 'trash':
          Toolbar::trash($task, $text);
          break;
      case 'publish':
          Toolbar::publish($task, $text ?: 'JTOOLBAR_PUBLISH', $list);
          break;
      case 'unpublish':
          Toolbar::unpublish($task, $text ?: 'JTOOLBAR_UNPUBLISH', $list);
          break;
      case 'checkin':
          Toolbar::checkin($task, $text, $list);
          break;
      case 'save':
          Toolbar::save($task, $text);
          break;
      case 'apply':
          Toolbar::apply($task);
          break;
      case 'save2new':
          Toolbar::save2new($task);
          break;
      case 'save2copy':
          Toolbar::save2copy($task);
          break;
      case 'cancel':
          Toolbar::cancel($task, $text ?: 'JTOOLBAR_CANCEL');
          break;
      case 'help':
          Toolbar::help($icon ?: 'help');
          break;
      case 'preferences':
          Toolbar::preferences($option, '550');
          break;
      default:
          $iconFile  = $icon ? $icon . '.png'    : 'custom.png';
          $iconFile2 = $icon ? $icon . '_f2.png' : 'custom_f2.png';
          Toolbar::custom($task, $iconFile, $iconFile2, $text ?: $task, $list);
  }
@endphp
