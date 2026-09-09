{{--
  Web development placeholder page.

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

<x-page-container :title="Lang::txt('COM_DEVELOPER_WEB')">
  <x-empty-state :title="Lang::txt('COM_DEVELOPER_COMING_SOON')">
    <a class="btn btn-ghost btn-sm"
       href="{{ Route::url('index.php?option=com_developer') }}">
      {{ Lang::txt('COM_DEVELOPER') }}
    </a>
  </x-empty-state>
</x-page-container>
