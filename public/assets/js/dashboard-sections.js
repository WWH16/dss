document.addEventListener('DOMContentLoaded', function () {
  const links = document.querySelectorAll('.sidebar-nav a');
  const sections = Array.from(document.querySelectorAll('.dashboard-section'));
  const shell = document.querySelector('.app-shell');
  const toggle = document.querySelector('.sidebar-toggle');
  const overlay = document.querySelector('.sidebar-overlay');
  const openBtn = document.querySelector('.sidebar-open-btn');
  // ensure overlay isn't left active on desktop pages
  if (overlay && shell && !window.matchMedia('(max-width: 767px)').matches) {
    shell.classList.remove('overlay-visible');
    try {
      overlay.style.pointerEvents = 'none';
      overlay.style.visibility = 'hidden';
      overlay.style.opacity = '0';
    } catch (e) {}
  }
  function showSection(id) {
    const prev = sections.find(s => s.style.display !== 'none');
    const next = document.getElementById(id);
    if (!next) return;
    // fade out previous
    if (prev && prev !== next) {
      prev.style.transition = 'opacity 180ms ease';
      prev.style.opacity = 0;
      setTimeout(() => { prev.style.display = 'none'; }, 200);
    }
    // show next
    next.style.display = 'block';
    next.style.opacity = 0;
    // force reflow
    void next.offsetWidth;
    next.style.transition = 'opacity 220ms ease';
    next.style.opacity = 1;
    links.forEach(a => {
      a.classList.toggle('active', a.getAttribute('href') === ('#' + id));
    });
    if (history.replaceState) history.replaceState(null, '', '#' + id);
    // on small screens hide sidebar after click and hide overlay
    if (window.matchMedia('(max-width: 767px)').matches && shell) {
      shell.classList.add('collapsed');
      updateOverlay();
      try { localStorage.setItem('sidebarCollapsed', '1'); } catch(e){}
    }
  }
  links.forEach(a => {
    const href = a.getAttribute('href') || '';
    if (href.startsWith('#')) {
      a.addEventListener('click', function (e) {
        e.preventDefault();
        const id = href.substring(1);
        const target = document.getElementById(id);
        if (target) showSection(id);
      });
    } else {
      // external link (SAW/AHP/logout/etc) — ensure overlay and collapsed state cleared so target page is interactive
      a.addEventListener('click', function () {
        try {
          if (shell) shell.classList.remove('overlay-visible');
          if (overlay) {
            overlay.style.pointerEvents = 'none';
            overlay.style.visibility = 'hidden';
            overlay.style.opacity = '0';
            overlay.style.display = 'none';
          }
        } catch (e) {}
        try { localStorage.removeItem('sidebarCollapsed'); } catch(e) {}
      });
    }
  });
  // sidebar toggle
  if (toggle && shell) {
    toggle.addEventListener('click', function () {
      shell.classList.toggle('collapsed');
      try {
        localStorage.setItem('sidebarCollapsed', shell.classList.contains('collapsed') ? '1' : '0');
      } catch (e) {}
      updateOverlay();
    });
  }

  if (openBtn && shell) {
    openBtn.addEventListener('click', function () {
      shell.classList.remove('collapsed');
      updateOverlay();
      try { localStorage.setItem('sidebarCollapsed', '0'); } catch(e){}
    });
  }

  // restore persisted sidebar state
  try {
    const stored = localStorage.getItem('sidebarCollapsed');
    if (stored === '1' && shell) shell.classList.add('collapsed');
  } catch (e) {}

  function updateOverlay() {
    if (!shell || !overlay) return;
    const isSmall = window.matchMedia('(max-width: 767px)').matches;
    const isOpen = !shell.classList.contains('collapsed');
    if (isSmall && isOpen) {
      shell.classList.add('overlay-visible');
      try {
        overlay.style.pointerEvents = 'auto';
        overlay.style.visibility = 'visible';
        overlay.style.opacity = '1';
        overlay.style.display = 'block';
      } catch (e) {}
    } else {
      shell.classList.remove('overlay-visible');
      try {
        overlay.style.pointerEvents = 'none';
        overlay.style.visibility = 'hidden';
        overlay.style.opacity = '0';
        overlay.style.display = 'none';
      } catch (e) {}
    }
  }

  if (overlay) {
    overlay.addEventListener('click', function () {
      if (!shell) return;
      shell.classList.add('collapsed');
      updateOverlay();
      try { localStorage.setItem('sidebarCollapsed', '1'); } catch (e) {}
    });
  }

  // ensure overlay state on resize
  window.addEventListener('resize', updateOverlay);
  // initial overlay state
  updateOverlay();
  // ensure any form submit removes overlay so it doesn't block clicks
  document.addEventListener('submit', function (e) {
    try {
      if (shell) shell.classList.remove('overlay-visible');
      updateOverlay();
    } catch (err) {}
  }, true);
  // initial show: hide all then reveal the chosen section
  sections.forEach(s => { s.style.display = 'none'; s.style.opacity = 0; });
  const hashId = location.hash ? location.hash.substring(1) : null;
  const initial = (hashId && document.getElementById(hashId)) ? hashId : (document.getElementById('section-dashboard') ? 'section-dashboard' : (document.getElementById('section-profile') ? 'section-profile' : (sections[0] ? sections[0].id : null)));
  if (initial) showSection(initial);
});
