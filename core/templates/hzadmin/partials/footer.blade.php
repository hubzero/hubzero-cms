{{--
  Admin footer — copyright and version.
--}}
<footer class="border-t border-base-300 bg-base-100 px-6 py-3">
  <div class="flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-muted-foreground">
    <p>&copy; {{ date('Y') }} Purdue University. All Rights Reserved.</p>
    <p>Powered by <a class="underline text-primary hover:text-primary/80" href="https://hubzero.org">Hubzero</a>&reg; {{ defined('HVERSION') ? HVERSION : '' }}</p>
  </div>
</footer>
