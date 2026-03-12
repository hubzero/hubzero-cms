{{--
  Admin toolbar — declarative wrapper for Toolbar::* facade calls.

  Emits no HTML. Sets up toolbar buttons based on props and permissions.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@props([
    'title'  => '',
    'icon'   => '',
    'canDo'  => null,
    'option' => '',
    'edit'   => false,
    'help'   => true,
])

@php
  use Hubzero\Facades\Toolbar;

  if ($title) {
      Toolbar::title($title, $icon);
  }

  if ($edit) {
      // Edit mode: save/apply/cancel
      if ($canDo && $canDo->get('core.edit')) {
          Toolbar::apply();
          Toolbar::save();
      }
      Toolbar::cancel();
  } else {
      // List mode: preferences, publish/unpublish, CRUD
      if ($canDo && $canDo->get('core.admin') && $option) {
          Toolbar::preferences($option, '550');
          Toolbar::spacer();
      }
      if ($canDo && $canDo->get('core.edit.state')) {
          Toolbar::publishList();
          Toolbar::unpublishList();
          Toolbar::spacer();
      }
      if ($canDo && $canDo->get('core.create')) {
          Toolbar::addNew();
      }
      if ($canDo && $canDo->get('core.edit')) {
          Toolbar::editList();
      }
      if ($canDo && $canDo->get('core.delete')) {
          Toolbar::deleteList();
      }
  }
@endphp

{{-- Custom toolbar calls — rendered before auto help --}}
{{ $slot }}

@php
  if ($help) {
      Toolbar::spacer();
      Toolbar::help(basename($icon ?: 'help'));
  }
@endphp
