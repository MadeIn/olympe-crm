(() => {
  const $ = (s, r=document)=>r.querySelector(s);
  const $$ = (s, r=document)=>[...r.querySelectorAll(s)];
  const html = (el, s)=>{ if(el) el.innerHTML = s; };
  const show = el=>el && (el.style.display='');
  const hide = el=>el && (el.style.display='none');

  // Loading overlay
  let overlay;
  function loading(on=true){
    if(!overlay){
      overlay=document.createElement('div');
      overlay.className='olympe-loading';
      overlay.style.cssText='position:fixed;inset:0;background:rgba(255,255,255,.6);z-index:9999;display:none;backdrop-filter:saturate(120%) blur(2px)';
      overlay.innerHTML='<div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);padding:12px 16px;border-radius:10px;background:#fff;box-shadow:0 4px 16px rgba(0,0,0,.1);font-family:sans-serif"><span class="fa fa-spinner fa-spin"></span> Chargement…</div>';
      document.body.appendChild(overlay);
    }
    overlay.style.display = on ? 'block' : 'none';
  }

  // Toaster
  let toaster;
  function ensureToaster(){
    if(!toaster){
      toaster = document.createElement('div');
      toaster.className = 'ol-toaster';
      document.body.appendChild(toaster);
    }
  }
  function toastError(title='Erreur', msg='Une erreur est survenue'){
    ensureToaster();
    const t = document.createElement('div');
    t.className='ol-toast';
    t.innerHTML = `
      <div class="ol-ico"></div>
      <div class="ol-body">
        <div class="ol-title">${title}</div>
        <div class="ol-msg">${msg}</div>
      </div>
      <button class="ol-close" aria-label="Fermer">×</button>
    `;
    toaster.appendChild(t);
    const remove=()=>{ t.style.opacity='0'; setTimeout(()=>t.remove(), 200); };
    t.querySelector('.ol-close').addEventListener('click', remove);
    setTimeout(remove, 5000);
  }

  // Inline badge (optionnel, près d’une zone cible)
  function showInlineError(target, msg='Erreur'){
    const el = (typeof target==='string') ? document.getElementById(target) : target;
    if(!el) return;
    // n’ajoute qu’un badge, pas des doublons
    let badge = el.previousElementSibling;
    const isBadge = badge && badge.classList.contains('ol-inline-error');
    if(!isBadge){
      badge = document.createElement('div');
      badge.className='ol-inline-error';
      badge.innerHTML = `<span class="dot"></span><span class="txt"></span>`;
      el.parentNode.insertBefore(badge, el);
    }
    badge.querySelector('.txt').textContent = msg;
  }

  // Confirm
  function ask(msg){ return window.confirm(msg); }

  // CSRF
  function csrf(){
    const m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.content : '';
  }

  // API base
  const BASE = (document.querySelector('base')?.getAttribute('href') || '/');
  const API_BASE = BASE + 'api/';

  function toFormData(obj){
    const fd = new FormData();
    for(const k in (obj||{})){
      const v = obj[k];
      if(v !== undefined && v !== null) fd.append(k, v);
    }
    return fd;
  }

  async function apiPost(endpoint, payload={}, opts={}){
    console.log(endpoint);
    console.log(payload);
    const body = toFormData({ csrf_token: csrf(), ...payload });
    console.log(body);
    let res, data, isJson=false;
    try{
      res = await fetch(API_BASE + endpoint, { method:'POST', body, credentials:'same-origin', headers:opts.headers||{} });
      console.log(res);
      const ct = res.headers.get('content-type') || '';
      isJson = ct.includes('application/json');
      data = isJson ? await res.json() : await res.text();
      if (!res.ok) {
        const errMsg = isJson && data?.error ? data.error : ('HTTP '+res.status);
        throw new Error(errMsg);
      }
      return data;
    }catch(e){
      // distingue réseau / serveur applicatif
      const network = !res;
      toastError(network ? 'Erreur réseau' : 'Erreur serveur', network ? "Impossible de contacter l'API." : (e.message || 'Réponse invalide.'));
      throw e;
    }
  }

  async function apiGet(endpoint, params={}){
    const usp = new URLSearchParams(params);
    const url = API_BASE + endpoint + (usp.toString() ? ('?'+usp.toString()) : '');
    let res, data, isJson=false;
    try{
      res = await fetch(url, { credentials:'same-origin' });
      const ct = res.headers.get('content-type') || '';
      isJson = ct.includes('application/json');
      data = isJson ? await res.json() : await res.text();
      if(!res.ok){
        const errMsg = isJson && data?.error ? data.error : ('HTTP '+res.status);
        throw new Error(errMsg);
      }
      return data;
    }catch(e){
      const network = !res;
      toastError(network ? 'Erreur réseau' : 'Erreur serveur', network ? "Impossible de contacter l'API." : (e.message || 'Réponse invalide.'));
      throw e;
    }
  }

  // Expose global
  window.$ol = { $, $$, html, show, hide, loading, ask, toastError, showInlineError, apiPost, apiGet };
})();

// Helpers DOM
function displayReponse(sText, place) { $ol.html(document.getElementById(place), sText); }
