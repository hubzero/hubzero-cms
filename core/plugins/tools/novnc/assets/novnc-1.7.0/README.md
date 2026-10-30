# noVNC 1.7.0 (HUBzero)

Upstream noVNC v1.7.0 (https://github.com/novnc/noVNC, MPL 2.0, see
LICENSE.txt), `core/` and `vendor/pako/` only, used by the plugin's
`modern` engine (`views/session/tmpl/modern.php`).

`core/rfb.js` carries the HUBzero patches in `hubzero.patch`:

- `requestHubzeroResize(w, h)`: asks the HUBzero VNC server for a new
  framebuffer size with its own client message (type 0x20, subtype 42,
  u16 width, u16 height), in place of ExtendedDesktopSize.
- server message 0x20, ClientAction (3 bytes padding, u32 length, then
  "url <url>"), raised as a `clientaction` event; the page opens the URL.
- an `fbresize` event whenever the framebuffer size changes, which the
  page sizes its frame to.

To upgrade: copy `core/` and `vendor/pako/` from the new release, then
`git apply hubzero.patch` (from this directory) and resolve any rejects.
