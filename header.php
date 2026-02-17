<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script>
  (function(){
    var d=document.documentElement;
    var t=localStorage.getItem('dlm-theme')||(window.matchMedia('(prefers-color-scheme:dark)').matches?'dark':'light');
    d.setAttribute('data-theme',t);
    document.addEventListener('DOMContentLoaded',function(){
      document.querySelectorAll('.theme-toggle').forEach(function(btn){
        btn.addEventListener('click',function(){
          t=(t==='light'?'dark':'light');
          d.setAttribute('data-theme',t);
          localStorage.setItem('dlm-theme',t);
        });
      });
      var closeBtn=document.querySelector('.mobile-nav-close');
      if(closeBtn) closeBtn.addEventListener('click',function(){
        var nav=document.querySelector('.main-navigation--mobile');
        var tog=document.querySelector('.menu-toggle');
        if(nav){nav.classList.remove('toggled');document.body.style.overflow='';}
        if(tog) tog.setAttribute('aria-expanded','false');
      });
    });
  })();
  </script>
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header" role="banner">
  <div class="site-branding">
    <a href="<?php echo esc_url( home_url('/') ); ?>">
      <?php if ( has_custom_logo() ) :
        $logo_id  = get_theme_mod( 'custom_logo' );
        $logo_url = wp_get_attachment_image_url( $logo_id, 'thumbnail' );
        if ( $logo_url ) : ?>
          <img src="<?php echo esc_url( $logo_url ); ?>" alt="" class="site-avatar">
        <?php endif;
      endif; ?>
      <?php bloginfo('name'); ?> <span>·</span> HCI
    </a>
  </div>

  <nav id="site-navigation" class="main-navigation" role="navigation">
    <?php wp_nav_menu([ 'theme_location' => 'primary', 'menu_id' => 'primary-menu', 'container' => false, 'fallback_cb' => 'deladem_fallback_menu' ]); ?>
  </nav>

  <div class="header-controls">
    <button class="theme-toggle" aria-label="Toggle theme" title="Light/Dark theme">
      <svg class="icon-sun" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
      <svg class="icon-moon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
    </button>
    <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" aria-label="Menu"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
  </div>
</header>

<nav id="mobile-navigation" class="main-navigation main-navigation--mobile" role="navigation" aria-label="Mobile menu">
  <div class="mobile-nav-header">
    <span class="mobile-nav-title">Menu</span>
    <button class="mobile-nav-close" aria-label="Close menu">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
  </div>
  <?php wp_nav_menu([ 'theme_location' => 'primary', 'menu_id' => 'mobile-menu', 'container' => false, 'fallback_cb' => 'deladem_fallback_menu' ]); ?>
  <div class="mobile-nav-footer">
    <button class="theme-toggle mobile-theme-toggle" aria-label="Toggle theme">
      <svg class="icon-sun" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
      <svg class="icon-moon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
      Theme
    </button>
  </div>
</nav>

<div id="page" class="site">
<main id="content" class="site-main" role="main">
