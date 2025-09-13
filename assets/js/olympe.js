// Olympe mini toolkit
(() => {
  // ———————————————————
  // Utils DOM
  // ———————————————————
  const $ = (sel, root=document) => root.querySelector(sel);
  const $$ = (sel, root=document) => [...root.querySelectorAll(sel)];
  const html = (el, s) => { if (el) el.innerHTML = s; };
  const show = el => el && (el.style.display = '');
  const hide = el => el && (el.style.display = 'none');

  // ———————————————————
  // Loading overlay (full page)
  // ———————————————————
  let overlay;
  function loading(on=true) {
    if (!overlay) {
      overlay = document.createElement('div');
      overlay.className = 'olympe-loading';
      overlay.style.cssText =
        'position:fixed;inset:0;background:rgba(255,255,255,.6);z-index:9999;display:none;' +
        'backdrop-filter:saturate(120%) blur(2px);';
      overlay.innerHTML =
        '<div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);' +
        'padding:12px 16px;border-radius:10px;background:#fff;box-shadow:0 4px 16px rgba(0,0,0,.1);' +
        'font-family:sans-serif"><span class="fa fa-spinner fa-spin"></span> Chargement…</div>';
      document.body.appendChild(overlay);
    }
    overlay.style.display = on ? 'block' : 'none';
  }

  // ———————————————————
  // Confirm (wrapper)
  // ———————————————————
  function ask(msg) { return window.confirm(msg); }

  // ———————————————————
  // CSRF
  // ———————————————————
  function csrf() {
    const m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.content : '';
  }

  // ———————————————————
  // API fetchers (POST JSON/form ou GET)
  // ———————————————————
  const BASE = (document.querySelector('base')?.getAttribute('href') || '/'); // optionnel
  const API_BASE = BASE + 'api/'; // nos endpoints : /api/...

  function toFormData(obj) {
    const fd = new FormData();
    Object.keys(obj || {}).forEach(k => {
      const v = obj[k];
      if (v !== undefined && v !== null) fd.append(k, v);
    });
    return fd;
  }

  async function apiPost(endpoint, payload = {}, opts = {}) {
    // payload = { mode, ... } si tu veux garder les "modes"
    const body = toFormData({ csrf_token: csrf(), ...payload });
    const res = await fetch(API_BASE + endpoint, {
      method: 'POST',
      body,
      credentials: 'same-origin',
      headers: opts.headers || {}
    });
    if (!res.ok) throw new Error('HTTP ' + res.status);
    const ct = res.headers.get('content-type') || '';
    return ct.includes('application/json') ? res.json() : res.text();
  }

  async function apiGet(endpoint, params = {}) {
    const usp = new URLSearchParams(params);
    const url = API_BASE + endpoint + (usp.toString() ? ('?' + usp.toString()) : '');
    const res = await fetch(url, { credentials: 'same-origin' });
    if (!res.ok) throw new Error('HTTP ' + res.status);
    const ct = res.headers.get('content-type') || '';
    return ct.includes('application/json') ? res.json() : res.text();
  }

  // ———————————————————
  // Expose global (propre)
  // ———————————————————
  window.$ol = {
    $, $$, html, show, hide,
    loading, ask,
    apiPost, apiGet
  };
})();


// Helpers DOM
function displayReponse(sText, place) { $ol.html(document.getElementById(place), sText); }