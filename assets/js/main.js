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

  // Badges — marquee on mobile, parallax on desktop
  var badgesContainer = document.querySelector('.sensor-badges');
  var heroRight = document.querySelector('.hero-right');
  var isMobile = window.matchMedia('(max-width: 768px)').matches;

  if (badgesContainer) {
    if (isMobile && !prefersReducedMotion) {
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
        // Adjust speed: ~3s per badge
        var duration = Math.max(badges.length * 3, 10);
        badgesContainer.style.setProperty('--marquee-duration', duration + 's');
      }
    } else if (heroRight && !prefersReducedMotion) {
      // Desktop: mouse parallax
      var desktopBadges = heroRight.querySelectorAll('.sensor-badge');
      heroRight.addEventListener('mousemove', function(e) {
        var rect = heroRight.getBoundingClientRect();
        var mx = (e.clientX - rect.left) / rect.width - 0.5;
        var my = (e.clientY - rect.top) / rect.height - 0.5;
        desktopBadges.forEach(function(badge, i) {
          var depth = 0.5 + (i % 3) * 0.25;
          var tx = mx * 30 * depth;
          var ty = my * 20 * depth;
          badge.style.setProperty('--parallax-x', tx + 'px');
          badge.style.setProperty('--parallax-y', ty + 'px');
        });
      }, { passive: true });
      heroRight.addEventListener('mouseleave', function() {
        desktopBadges.forEach(function(badge) {
          badge.style.setProperty('--parallax-x', '0px');
          badge.style.setProperty('--parallax-y', '0px');
        });
      });
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
