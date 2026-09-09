{{--
  Log JS Errors module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<script>
jQuery(function($) {
  var handlingError = false;
  window.onerror = function(msg, file, line) {
    if (handlingError) return;
    handlingError = true;
    try { msg = JSON.stringify(msg); } catch (ex) {}
    $.post('{{ rtrim(Request::base(true), "/") }}/core/modules/mod_logjserrors/mod_logjserrors.php', {
      'message': msg, 'file': file, 'line': line,
      'url': window.location.toString(),
      'navigator': JSON.stringify(podify(navigator))
    }).done(function() { handlingError = false; });
  };
  var podify = function(val) {
    var pod = {};
    for (var k in val) {
      switch (typeof val[k]) {
        case 'function': continue;
        case 'object':
          if (val[k] === null) pod[k] = null;
          else if (k == 'plugins') {
            var plg = [];
            for (var idx = 0; idx < val[k].length; ++idx)
              plg.push(val[k][idx].name + ' ' + val[k][idx].description
                + (val[k][idx].version ? ' ' + val[k][idx].version : ''));
            pod[k] = plg;
          }
          continue;
        default: pod[k] = val[k];
      }
    }
    return pod;
  };
});
</script>
