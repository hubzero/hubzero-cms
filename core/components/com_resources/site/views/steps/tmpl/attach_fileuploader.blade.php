{{--
  Resources contribution — file uploader and link adder widgets.

  Variables:
    $id — parent resource id

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $uploadAction = 'index.php?option=com_resources'
      . '&no_html=1&controller=attachments'
      . '&task=save&pid=' . $id;
  $uploadList = 'index.php?option=com_resources'
      . '&no_html=1&controller=attachments'
      . '&pid=' . $id;
  $linkAction = 'index.php?option=com_resources'
      . '&controller=attachments&no_html=1'
      . '&task=create&pid=' . $id
      . '&url=';
  $linkList = 'index.php?option=com_resources'
      . '&controller=attachments&no_html=1'
      . '&pid=' . $id;
@endphp

<div>
  <div id="ajax-uploader"
       data-action="{{ $uploadAction }}"
       data-list="{{ $uploadList }}"
       data-instructions="Click or drop file"
       role="region"
       aria-label="File upload">
  </div>
</div>

<div>
  <div id="link-adder"
       data-action="{{ $linkAction }}"
       data-list="{{ $linkList }}"
       role="region"
       aria-label="Add link">
  </div>
</div>
