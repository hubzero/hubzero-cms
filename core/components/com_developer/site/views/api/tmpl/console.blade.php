{{--
  API console placeholder page.

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

<x-page-container :title="Lang::txt('COM_DEVELOPER_API_CONSOLE')">
  <x-empty-state :title="Lang::txt('COM_DEVELOPER_COMING_SOON')">
    <a class="btn btn-ghost btn-sm"
       href="{{ Route::url('index.php?option=com_developer&controller=api') }}">
      {{ Lang::txt('COM_DEVELOPER_API_HOME') }}
    </a>
  </x-empty-state>
</x-page-container>
