{{--
  Publications status key legend partial

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php use Hubzero\Facades\Lang; @endphp
<div class="flex flex-wrap gap-3 mt-4 text-xs">
  <span class="badge badge-info">{{ Lang::txt('COM_PUBLICATIONS_VERSION_DRAFT') }}</span>
  <span class="badge badge-success">{{ Lang::txt('COM_PUBLICATIONS_VERSION_PUBLISHED') }}</span>
  <span class="badge badge-ghost">{{ Lang::txt('COM_PUBLICATIONS_VERSION_UNPUBLISHED') }}</span>
  <span class="badge badge-warning">{{ Lang::txt('COM_PUBLICATIONS_VERSION_PENDING') }}</span>
  <span class="badge badge-accent">{{ Lang::txt('COM_PUBLICATIONS_VERSION_READY') }}</span>
  <span class="badge badge-secondary">{{ Lang::txt('COM_PUBLICATIONS_VERSION_PRESERVING') }}</span>
  <span class="badge badge-neutral">{{ Lang::txt('COM_PUBLICATIONS_VERSION_WIP') }}</span>
  <span class="badge badge-error">{{ Lang::txt('COM_PUBLICATIONS_VERSION_DELETED') }}</span>
</div>
