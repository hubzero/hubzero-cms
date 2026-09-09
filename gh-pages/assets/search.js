/* Client-side search over the index the builder writes to search-index.json.
   Progressive: without script the box is an ordinary form that does nothing;
   with it, typing filters the index and lists matching pages. */
(function () {
  "use strict";

  var forms = document.querySelectorAll("form.search");
  if (!forms.length) return;

  var index = null;
  var loading = null;

  function load(url) {
    if (index) return Promise.resolve(index);
    if (!loading) {
      loading = fetch(url, { credentials: "same-origin" })
        .then(function (r) { return r.json(); })
        .then(function (data) { index = data; return data; });
    }
    return loading;
  }

  function escapeHtml(s) {
    return s.replace(/[&<>"]/g, function (c) {
      return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;" }[c];
    });
  }

  function highlight(text, terms) {
    var out = escapeHtml(text);
    terms.forEach(function (t) {
      if (t.length < 2) return;
      var re = new RegExp("(" + t.replace(/[.*+?^${}()|[\]\\]/g, "\\$&") + ")", "ig");
      out = out.replace(re, "<mark>$1</mark>");
    });
    return out;
  }

  function score(entry, terms) {
    var title = entry.t.toLowerCase();
    var path = (entry.b + " " + entry.s).toLowerCase();
    var heads = (entry.h || "").toLowerCase();
    var body = (entry.x || "").toLowerCase();
    var total = 0;
    for (var i = 0; i < terms.length; i++) {
      var t = terms[i];
      var s = 0;
      if (title === t) s += 40;
      else if (title.indexOf(t) === 0) s += 25;
      else if (title.indexOf(t) >= 0) s += 15;
      if (heads.indexOf(t) >= 0) s += 8;
      if (path.indexOf(t) >= 0) s += 4;
      if (body.indexOf(t) >= 0) s += 2;
      if (!s) return 0; // every term must match somewhere
      total += s;
    }
    return total;
  }

  function relative(base, url) {
    return base + url;
  }

  forms.forEach(function (form) {
    var input = form.querySelector("input[type=search]");
    var list = form.querySelector(".search__results");
    var base = form.getAttribute("data-base") || "";
    var indexUrl = form.getAttribute("data-index");
    if (!input || !list || !indexUrl) return;
    var selected = -1;
    var items = [];

    function close() {
      list.hidden = true;
      list.innerHTML = "";
      items = [];
      selected = -1;
      input.setAttribute("aria-expanded", "false");
    }

    function render(results, terms) {
      list.innerHTML = "";
      items = [];
      if (!results.length) {
        list.innerHTML = '<li class="search__empty">No pages match.</li>';
        list.hidden = false;
        return;
      }
      results.forEach(function (entry) {
        var li = document.createElement("li");
        li.className = "search__result";
        li.setAttribute("role", "option");
        var path = entry.b + (entry.s ? " / " + entry.s : "");
        li.innerHTML =
          '<a href="' + escapeHtml(relative(base, entry.u)) + '">' +
          '<span class="search__result-title">' + highlight(entry.t, terms) + "</span>" +
          '<span class="search__result-path">' + escapeHtml(path) + "</span>" +
          '<span class="search__result-excerpt">' + highlight(entry.x || "", terms) + "</span>" +
          "</a>";
        list.appendChild(li);
        items.push(li);
      });
      list.hidden = false;
      input.setAttribute("aria-expanded", "true");
    }

    function run() {
      var q = input.value.trim().toLowerCase();
      if (q.length < 2) { close(); return; }
      var terms = q.split(/\s+/).filter(Boolean);
      load(indexUrl).then(function (data) {
        var scored = [];
        for (var i = 0; i < data.length; i++) {
          var s = score(data[i], terms);
          if (s > 0) scored.push({ s: s, e: data[i] });
        }
        scored.sort(function (a, b) { return b.s - a.s || a.e.t.localeCompare(b.e.t); });
        render(scored.slice(0, 12).map(function (x) { return x.e; }), terms);
      }).catch(close);
    }

    function select(delta) {
      if (!items.length) return;
      if (selected >= 0) items[selected].classList.remove("is-selected");
      selected = (selected + delta + items.length) % items.length;
      items[selected].classList.add("is-selected");
      items[selected].scrollIntoView({ block: "nearest" });
    }

    var timer = null;
    input.addEventListener("input", function () {
      clearTimeout(timer);
      timer = setTimeout(run, 80);
    });
    input.addEventListener("focus", function () { if (input.value.trim().length >= 2) run(); });
    input.addEventListener("keydown", function (e) {
      if (e.key === "ArrowDown") { e.preventDefault(); select(1); }
      else if (e.key === "ArrowUp") { e.preventDefault(); select(-1); }
      else if (e.key === "Escape") { close(); }
      else if (e.key === "Enter") {
        if (selected >= 0) { e.preventDefault(); items[selected].querySelector("a").click(); }
        else if (items.length) { e.preventDefault(); items[0].querySelector("a").click(); }
        else { e.preventDefault(); }
      }
    });
    form.addEventListener("submit", function (e) { e.preventDefault(); run(); });
    document.addEventListener("click", function (e) {
      if (!form.contains(e.target)) close();
    });
    input.setAttribute("role", "combobox");
    input.setAttribute("aria-autocomplete", "list");
    input.setAttribute("aria-expanded", "false");
    list.setAttribute("role", "listbox");
  });
})();
