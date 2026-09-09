{{--
  Unpublished page notice with publish link.

  Variables:
    $group — Group object
    $page  — Page model

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  $publishLink = Route::url('index.php?option=com_groups&cn=' . $group->get('cn')
      . '&controller=pages&task=publish&pageid=' . $page->get('id'));
  $returnUrl = $publishLink . '&return=' . base64_encode(Request::current(true));
@endphp

<div class="group-page group-page-notice notice-info">
  <h4>{{ Lang::txt('COM_GROUPS_PAGES_PAGE_NOT_PUBLISHED') }}</h4>
  <p>{{ Lang::txt('COM_GROUPS_PAGES_PAGE_NOT_PUBLISHED_DESC') }}</p>
  <p><a href="{{ $returnUrl }}">{{ rtrim(Request::base(), '/') . $publishLink }}</a></p>
</div>
