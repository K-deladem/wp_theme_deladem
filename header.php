<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
      <?php bloginfo('name'); ?> <span>·</span> IHM
    </a>
  </div>

  <nav id="site-navigation" class="main-navigation" role="navigation">
    <?php wp_nav_menu([ 'theme_location' => 'primary', 'menu_id' => 'primary-menu', 'container' => false, 'fallback_cb' => 'deladem_fallback_menu' ]); ?>
  </nav>

  <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" aria-label="Menu"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
</header>

<nav id="mobile-navigation" class="main-navigation main-navigation--mobile" role="navigation" aria-label="Menu mobile">
  <?php wp_nav_menu([ 'theme_location' => 'primary', 'menu_id' => 'mobile-menu', 'container' => false, 'fallback_cb' => 'deladem_fallback_menu' ]); ?>
</nav>

<div id="page" class="site">
<main id="content" class="site-main" role="main">
