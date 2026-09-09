{{--
  Application Environment module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<div id="system-$environment">
  <p class="badge badge-{{ strtolower($environment) === 'production' ? 'neutral' : 'warning' }}">
    {{ Lang::txt('MOD_APPLICATION_ENV_' . strtoupper($environment)) }}
  </p>
</div>
