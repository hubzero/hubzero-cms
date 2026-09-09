{{--
 * Search 404 — page not found
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
@endphp

<x-page-container :title="Lang::txt('COM_SEARCH_404_TITLE')">
    <x-empty-state
        :title="Lang::txt('COM_SEARCH_404_TITLE')"
        :message="Lang::txt('COM_SEARCH_404_MESSAGE')"
    />
</x-page-container>
