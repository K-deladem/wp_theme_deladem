(function() {
  'use strict';

  var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Fade-up au scroll
  var observer = new IntersectionObserver(function(entries) {
    entries.forEach(function(e) { if (e.isIntersecting) e.target.classList.add('visible'); });
  }, { threshold: 0.1 });
  document.querySelectorAll('.fade-up').forEach(function(el) { observer.observe(el); });

  // Menu mobile — targets the separate nav outside header
  var toggle = document.querySelector('.menu-toggle');
  var mobileNav = document.querySelector('.main-navigation--mobile');
  function closeMenu() {
    if (!toggle || !mobileNav) return;
    toggle.setAttribute('aria-expanded', 'false');
    mobileNav.classList.remove('toggled');
    document.body.style.overflow = '';
  }
  if (toggle && mobileNav) {
    toggle.addEventListener('click', function() {
      var exp = this.getAttribute('aria-expanded') === 'true';
      this.setAttribute('aria-expanded', String(!exp));
      mobileNav.classList.toggle('toggled');
      document.body.style.overflow = exp ? '' : 'hidden';
    });
    mobileNav.querySelectorAll('a').forEach(function(a) {
      a.addEventListener('click', closeMenu);
    });
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && mobileNav.classList.contains('toggled')) closeMenu();
    });
  }

  // Nav active au scroll
  var sections = document.querySelectorAll('section[id]');
  var navLinks = document.querySelectorAll('.main-navigation a');
  function updateNav() {
    var cur = '';
    sections.forEach(function(s) {
      if (window.scrollY >= s.offsetTop - 130) cur = s.id;
    });
    navLinks.forEach(function(a) {
      a.style.color = a.getAttribute('href') === '#' + cur ? 'var(--ink)' : '';
    });
  }
  window.addEventListener('scroll', updateNav, { passive: true });
  updateNav();

  // Smooth scroll liens internes
  document.querySelectorAll('a[href^="#"]').forEach(function(a) {
    a.addEventListener('click', function(e) {
      var href = this.getAttribute('href');
      if (href === '#') return;
      var t = document.querySelector(href);
      if (t) {
        e.preventDefault();
        window.scrollTo({
          top: t.getBoundingClientRect().top + window.scrollY - 80,
          behavior: prefersReducedMotion ? 'auto' : 'smooth'
        });
      }
    });
  });

  // Badges — marquee on mobile/editorial, interactive mouse effect on desktop
  var badgesContainer = document.querySelector('.sensor-badges');
  var heroSection = document.getElementById('hero');
  var heroRight = document.querySelector('.hero-right');
  var isMobile = window.matchMedia('(max-width: 768px)').matches;
  var isEditorial = document.body.classList.contains('layout_hero-editorial');
  var isCentered = document.body.classList.contains('layout_hero-centered');
  var isClassic = document.body.classList.contains('layout_hero-classic');
  var useMarquee = isEditorial || isCentered || isClassic;
  var mouseEffect = heroSection ? heroSection.getAttribute('data-mouse-effect') || 'parallax' : 'parallax';

  if (badgesContainer) {
    if ((isMobile || useMarquee) && !prefersReducedMotion) {
      // Wrap badges in a track, clone for seamless loop
      var badges = Array.from(badgesContainer.querySelectorAll('.sensor-badge'));
      if (badges.length) {
        var track = document.createElement('div');
        track.className = 'sensor-badges-track';
        badges.forEach(function(b) { track.appendChild(b); });
        var clone = track.cloneNode(true);
        clone.setAttribute('aria-hidden', 'true');
        badgesContainer.appendChild(track);
        badgesContainer.appendChild(clone);
        var duration = Math.max(badges.length * 3, 10);
        badgesContainer.style.setProperty('--marquee-duration', duration + 's');
      }
    } else if (heroRight && !prefersReducedMotion && mouseEffect !== 'none') {
      var desktopBadges = heroRight.querySelectorAll('.sensor-badge');
      // Determine the mouse zone (hero-right for split/compact, whole #hero for bold)
      var isBold = document.body.classList.contains('layout_hero-bold');
      var mouseZone = isBold ? heroSection : heroRight;

      if (mouseEffect === 'parallax') {
        // ── PARALLAX: badges float with depth-based offset ──
        mouseZone.addEventListener('mousemove', function(e) {
          var rect = mouseZone.getBoundingClientRect();
          var mx = (e.clientX - rect.left) / rect.width - 0.5;
          var my = (e.clientY - rect.top) / rect.height - 0.5;
          desktopBadges.forEach(function(badge, i) {
            var depth = 0.5 + (i % 3) * 0.25;
            badge.style.setProperty('--parallax-x', (mx * 30 * depth) + 'px');
            badge.style.setProperty('--parallax-y', (my * 20 * depth) + 'px');
          });
        }, { passive: true });
        mouseZone.addEventListener('mouseleave', function() {
          desktopBadges.forEach(function(badge) {
            badge.style.setProperty('--parallax-x', '0px');
            badge.style.setProperty('--parallax-y', '0px');
          });
        });

      } else if (mouseEffect === 'magnetic') {
        // ── MAGNETIC: badges attracted toward cursor when nearby ──
        mouseZone.addEventListener('mousemove', function(e) {
          var rect = mouseZone.getBoundingClientRect();
          var cx = e.clientX - rect.left;
          var cy = e.clientY - rect.top;
          desktopBadges.forEach(function(badge) {
            var br = badge.getBoundingClientRect();
            var bx = br.left + br.width / 2 - rect.left;
            var by = br.top + br.height / 2 - rect.top;
            var dx = cx - bx;
            var dy = cy - by;
            var dist = Math.sqrt(dx * dx + dy * dy);
            var radius = 200;
            if (dist < radius) {
              var strength = (1 - dist / radius) * 25;
              var angle = Math.atan2(dy, dx);
              badge.style.transform = 'translate(' + (Math.cos(angle) * strength) + 'px,' + (Math.sin(angle) * strength) + 'px)';
            } else {
              badge.style.transform = '';
            }
          });
        }, { passive: true });
        mouseZone.addEventListener('mouseleave', function() {
          desktopBadges.forEach(function(badge) {
            badge.style.transform = '';
          });
        });

      } else if (mouseEffect === 'glow') {
        // ── GLOW: radial glow follows cursor, badges brighten near cursor ──
        var accentRgb = getComputedStyle(document.documentElement).getPropertyValue('--accent-rgb').trim() || '201,74,45';
        var glow = document.createElement('div');
        glow.className = 'hero-mouse-glow';
        mouseZone.appendChild(glow);
        mouseZone.addEventListener('mousemove', function(e) {
          var rect = mouseZone.getBoundingClientRect();
          var x = e.clientX - rect.left;
          var y = e.clientY - rect.top;
          glow.style.left = x + 'px';
          glow.style.top = y + 'px';
          glow.style.opacity = '1';
          desktopBadges.forEach(function(badge) {
            var br = badge.getBoundingClientRect();
            var bx = br.left + br.width / 2 - rect.left;
            var by = br.top + br.height / 2 - rect.top;
            var dist = Math.sqrt((bx - x) * (bx - x) + (by - y) * (by - y));
            var intensity = Math.max(0, 1 - dist / 250);
            badge.style.boxShadow = intensity > 0.05
              ? '0 0 ' + (intensity * 18) + 'px rgba(' + accentRgb + ',' + (intensity * 0.6) + ')'
              : 'none';
            badge.style.borderColor = 'rgba(255,255,255,' + (0.15 + intensity * 0.5) + ')';
            badge.style.color = 'rgba(255,255,255,' + (0.75 + intensity * 0.25) + ')';
          });
        }, { passive: true });
        mouseZone.addEventListener('mouseleave', function() {
          glow.style.opacity = '0';
          desktopBadges.forEach(function(badge) {
            badge.style.boxShadow = 'none';
            badge.style.borderColor = '';
            badge.style.color = '';
          });
        });
      }
    }
  }

  // Collapsible interets
  document.querySelectorAll('.interet-header').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var item = this.closest('.interet-item');
      var isActive = item.classList.contains('active');

      // Fermer tous les autres
      document.querySelectorAll('.interet-item.active').forEach(function(other) {
        if (other !== item) {
          other.classList.remove('active');
          other.querySelector('.interet-header').setAttribute('aria-expanded', 'false');
          var body = other.querySelector('.interet-body');
          if (body) body.setAttribute('aria-hidden', 'true');
        }
      });

      // Toggle celui-ci
      item.classList.toggle('active');
      this.setAttribute('aria-expanded', String(!isActive));
      var body = item.querySelector('.interet-body');
      if (body) body.setAttribute('aria-hidden', String(isActive));
    });
  });
})();
