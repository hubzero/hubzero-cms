{{--
  Resource Edit — JS initialization partial

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $path = trim($rconfig->get('uploadpath'), DIRECTORY_SEPARATOR);
  $root = substr(PATH_APP, strlen(PATH_ROOT));
  if (substr($path, 0, strlen($root)) != $root) {
      $path = $root . DIRECTORY_SEPARATOR . $path;
  }
  $path .= DIRECTORY_SEPARATOR;

  $__view->js('edit.blade.js');
@endphp

<script id="resource-edit-config" type="application/json">
{!! json_encode([
    'confirmHitsReset' => Lang::txt('COM_RESOURCES_CONFIRM_HITS_RESET'),
    'confirmRatingReset' => Lang::txt('COM_RESOURCES_CONFIRM_RATINGS_RESET'),
    'errorMissingTitle' => Lang::txt('COM_RESOURCES_ERROR_MISSING_TITLE'),
    'errorMissingType' => Lang::txt('COM_RESOURCES_ERROR_MISSING_TYPE'),
    'uploadPath' => $rconfig->get('uploadpath') . '/',
    'filePath' => $path,
], JSON_HEX_TAG | JSON_HEX_AMP) !!}
</script>
