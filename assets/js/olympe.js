/* =========================
   OLYMPE — Helpers + UI + API + Modals
   Version complète avec design épuré luxe - Compatible Bootstrap 3
   ========================= */
(() => {
  // Shortcuts DOM
  const $  = (s, r=document)=>r.querySelector(s);
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
      overlay.innerHTML='<div class="box"><span class="fa fa-spinner fa-spin"></span> Chargement…</div>';
      document.body.appendChild(overlay);
    }
    overlay.style.display = on ? 'block' : 'none';
  }

  // ===== Toaster =====
  let toaster;
  function ensureToaster(){
    if(!toaster){
      toaster = document.createElement('div');
      toaster.className = 'ol-toaster';
      document.body.appendChild(toaster);
    }
  }
  function makeToast({type='error', title='Erreur', msg='Une erreur est survenue', ttl=1000, closable=true}={}){
    ensureToaster();
    const t = document.createElement('div');
    t.className = 'ol-toast' + (type && type!=='error' ? ' '+type : '');
    
    // Construction HTML compatible Bootstrap 3
    var html = '<div class="ol-ico"></div>';
    html += '<div class="ol-body">';
    if(title) html += '<div class="ol-title">' + title + '</div>';
    if(msg) html += '<div class="ol-msg">' + msg + '</div>';
    html += '</div>';
    if(closable) html += '<button class="ol-close" aria-label="Fermer">×</button>';
    
    t.innerHTML = html;
    toaster.appendChild(t);

    const remove = function(){
      if (t.classList) {
        t.classList.remove('show');
      } else {
        // Fallback IE
        t.className = t.className.replace(/\bshow\b/g, '');
      }
      t.style.opacity = '0';
      setTimeout(function(){
        if (t.parentNode) {
          t.parentNode.removeChild(t);
        }
      }, 200);
    };

    // Animation d'apparition
    setTimeout(function(){
      if (t.classList) {
        t.classList.add('show');
      } else {
        // Fallback IE
        t.className += ' show';
      }
    }, 10);

    // Bouton fermeture
    if (closable) {
      var closeBtn = t.querySelector('.ol-close');
      if (closeBtn) {
        if (closeBtn.addEventListener) {
          closeBtn.addEventListener('click', remove);
        } else {
          closeBtn.attachEvent('onclick', remove);
        }
      }
    }
    
    // Auto-removal avec pause au survol
    if (ttl > 0) {
      var tm = setTimeout(remove, ttl);
      
      // Gestion simplifiée des événements pour éviter les bugs
      if (t.addEventListener) {
        t.addEventListener('mouseenter', function(){
          if(tm) {
            clearTimeout(tm);
            tm = null;
          }
        });
        t.addEventListener('mouseleave', function(){
          if(!tm) {
            tm = setTimeout(remove, 1500);
          }
        });
      }
    }
    
    return { close: remove, el: t };
  }
  function toastError(title='Erreur', msg='Une erreur est survenue', opts={}){ return makeToast({type:'error',   title, msg, ...opts}); }
  function toastSuccess(title='Succès', msg='Opération réussie',      opts={}){ return makeToast({type:'success', title, msg, ...opts}); }
  function toastInfo(title='Info', msg='',                            opts={}){ return makeToast({type:'info',    title, msg, ...opts}); }
  function toastWarn(title='Attention', msg='',                       opts={}){ return makeToast({type:'warn',    title, msg, ...opts}); }

  // Inline badge (près d'un input)
  function showInlineError(target, msg='Erreur'){
    const el = (typeof target==='string') ? document.getElementById(target) : target;
    if(!el) return;
    // n'ajoute qu'un badge, pas des doublons
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

  // ===== Modal Dialog épurée (Compatible Bootstrap 3) =====
  function showModal({
    title = 'Confirmation',
    message = 'Êtes-vous sûr ?',
    confirmText = 'Confirmer',
    cancelText = 'Annuler',
    type = 'confirm',
    onConfirm = null,
    onCancel = null,
    closeOnOverlay = true
  } = {}) {
    
    return new Promise((resolve) => {
      // Créer l'overlay avec structure table-cell
      const overlay = document.createElement('div');
      overlay.className = 'ol-modal-overlay';
      
      // Wrapper pour centrage (technique Bootstrap 3)
      const wrapper = document.createElement('div');
      wrapper.className = 'ol-modal-wrapper';
      wrapper.style.cssText = 'display:table-cell!important;vertical-align:middle!important;text-align:center!important;width:100%!important;height:100%!important';
      
      // Créer le modal
      const modal = document.createElement('div');
      modal.className = 'ol-modal';
      
      // Construire le contenu
      let buttonsHtml = '';
      
      if (type === 'alert') {
        buttonsHtml = '<button class="ol-modal-btn primary" data-action="confirm">' + confirmText + '</button>';
      } else if (type === 'confirm') {
        buttonsHtml = '<button class="ol-modal-btn primary" data-action="confirm">' + confirmText + '</button>' +
                      '<button class="ol-modal-btn secondary" data-action="cancel">' + cancelText + '</button>';
      }
      
      modal.innerHTML = (title ? '<h3 class="ol-modal-title">' + title + '</h3>' : '') +
                       (message ? '<div class="ol-modal-message">' + message + '</div>' : '') +
                       '<div class="ol-modal-buttons">' + buttonsHtml + '</div>';
      
      // Assemblage
      wrapper.appendChild(modal);
      overlay.appendChild(wrapper);
      
      // Ajouter au body
      document.body.appendChild(overlay);
      
      // Fonction de fermeture
      const closeModal = (result = false) => {
        overlay.classList.remove('show');
        setTimeout(() => {
          if (overlay.parentNode) {
            overlay.parentNode.removeChild(overlay);
          }
        }, 300);
        resolve(result);
      };
      
      // Gestionnaires d'événements
      modal.addEventListener('click', (e) => {
        const action = e.target.getAttribute('data-action');
        if (action === 'confirm') {
          if (onConfirm) onConfirm();
          closeModal(true);
        } else if (action === 'cancel') {
          if (onCancel) onCancel();
          closeModal(false);
        }
      });
      
      // Fermeture sur clic overlay
      if (closeOnOverlay) {
        overlay.addEventListener('click', (e) => {
          if (e.target === overlay || e.target === wrapper) {
            if (onCancel) onCancel();
            closeModal(false);
          }
        });
      }
      
      // Fermeture sur Escape
      const handleEscape = (e) => {
        if (e.keyCode === 27) { // keyCode pour compatibilité IE
          if (document.removeEventListener) {
            document.removeEventListener('keydown', handleEscape);
          } else {
            document.detachEvent('onkeydown', handleEscape);
          }
          if (onCancel) onCancel();
          closeModal(false);
        }
      };
      
      if (document.addEventListener) {
        document.addEventListener('keydown', handleEscape);
      } else {
        document.attachEvent('onkeydown', handleEscape);
      }
      
      // Afficher le modal avec délai pour le rendu
      setTimeout(() => {
        overlay.classList.add('show');
        // Focus sur le bouton annuler
        const cancelBtn = modal.querySelector('.ol-modal-btn.secondary');
        if (cancelBtn && cancelBtn.focus) {
          cancelBtn.focus();
        }
      }, 10);
    });
  }
  
  // Fonctions de convenance pour les modals
  function confirmDialog(message, title = 'Confirmation') {
    return showModal({
      type: 'confirm',
      title: title,
      message: message,
      confirmText: 'Oui',
      cancelText: 'Non'
    });
  }
  
  function alertDialog(message, title = 'Information') {
    return showModal({
      type: 'alert',
      title: title,
      message: message,
      confirmText: 'OK'
    });
  }
  
  function deleteConfirm(itemName = 'cet élément') {
    return showModal({
      type: 'confirm',
      title: 'Confirmer la suppression',
      message: 'Êtes-vous sûr de vouloir supprimer ' + itemName + ' ?<br><small>Cette action ne peut pas être annulée.</small>',
      confirmText: 'Supprimer',
      cancelText: 'Annuler'
    });
  }

  // Fonction ask modernisée (remplace window.confirm)
  function ask(message, title = 'Confirmation') {
    return confirmDialog(message, title);
  }

  // Version synchrone pour compatibilité avec ancien code
  function askSync(message, title = 'Confirmation') {
    return window.confirm(message);
  }

  // CSRF
  function csrf(){
    const m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.content : '';
  }

  // API base
  const BASE = (document.querySelector('base') && document.querySelector('base').getAttribute('href')) || '/';
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
    const body = toFormData({ csrf_token: csrf(), ...payload });
    let res, data, isJson=false;
    try{
      res = await fetch(API_BASE + endpoint, { method:'POST', body: body, credentials:'same-origin', headers: opts.headers||{} });
      const ct = res.headers.get('content-type') || '';
      isJson = ct.includes('application/json');
      data = isJson ? await res.json() : await res.text();
      if (!res.ok) {
        const errMsg = isJson && data && data.error ? data.error : ('HTTP '+res.status);
        throw new Error(errMsg);
      }
      return data;
    }catch(e){
      const network = !res;
      toastError(network ? 'Erreur réseau' : 'Erreur serveur',
                 network ? "Impossible de contacter l'API." : (e.message || 'Réponse invalide.'));
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
        const errMsg = isJson && data && data.error ? data.error : ('HTTP '+res.status);
        throw new Error(errMsg);
      }
      return data;
    }catch(e){
      const network = !res;
      toastError(network ? 'Erreur réseau' : 'Erreur serveur',
                 network ? "Impossible de contacter l'API." : (e.message || 'Réponse invalide.'));
      throw e;
    }
  }

  // Expose global
  window.$ol = {
    // DOM helpers
    $: $, $$: $$, html: html, show: show, hide: hide,
    
    // UI components
    loading: loading,
    
    // Toast system
    toastError: toastError, toastSuccess: toastSuccess, toastInfo: toastInfo, toastWarn: toastWarn,
    showInlineError: showInlineError,
    
    // Modal system
    showModal: showModal, confirmDialog: confirmDialog, alertDialog: alertDialog, deleteConfirm: deleteConfirm, ask: ask, askSync: askSync,
    
    // API helpers
    apiPost: apiPost, apiGet: apiGet
  };
})();

// Helper global legacy (pour compatibilité)
function displayReponse(sText, place) {
  const el = document.getElementById(place);
  if (el) el.innerHTML = sText;
}