{{--
  Windows Tools invoke — auto-click launcher redirect page.

  This is a standalone page that triggers a protocol handler URL and then
  redirects back. The inline script is required for the auto-click behavior.

  TODO: Move inline JS to an external file for CSP compliance.

  Variables (from plugin):
    $url  — string: protocol handler URL to launch
    $rurl — string: return URL back to the resource

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<html>
  <body>
    <p><a id="runapplink" href="{{ $url }}">Run app</a></p>
    <p>This page should go back to the hub application page automatically.
      If it doesn't, click <a href="{{ $rurl }}">here.</a></p>
    <script>
    document.getElementById('runapplink').click();
    window.setTimeout(function(){
        window.location = "{{ $rurl }}";
    },1000);
    </script>
  </body>
</html>
