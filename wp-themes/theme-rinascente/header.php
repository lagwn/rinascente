<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="<?php echo esc_url( get_template_directory_uri() . '/assets/img/favicon.png' ); ?>">
  <link rel="apple-touch-icon" href="<?php echo esc_url( get_template_directory_uri() . '/assets/img/favicon.png' ); ?>">
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php
$rinascente_member_page_url = rinascente_member_page_url();
$rinascente_member_login_url = rinascente_member_login_url( $rinascente_member_page_url );
?>

  <!-- ============ HEADER ============ -->
  <header class="site-header<?php if ( ! is_front_page() ) echo ' scrolled'; ?>">
    <div class="header-inner">
      <a href="<?php echo esc_url( home_url('/') ); ?>" class="header-logo">
        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo.svg' ); ?>" alt="" class="header-logo__img">
        <span class="header-logo__text">
          <span class="logo-wordmark">Rinascente</span>
          <span class="logo-sub">復活する。再生する。</span>
        </span>
      </a>
      <button class="menu-toggle" aria-label="メニュー" aria-expanded="false">
        <span class="bar"></span><span class="bar"></span><span class="bar"></span>
      </button>
      <nav class="header-nav header-nav--desktop" role="navigation">
        <a href="<?php echo esc_url( home_url('/') ); ?>" class="nav-item<?php if ( is_front_page() ) echo ' active'; ?>">Home</a>
        <a href="<?php echo esc_url( home_url('/identity/') ); ?>" class="nav-item<?php if ( is_page('identity') ) echo ' active'; ?>">Identity</a>
        <a href="<?php echo esc_url( home_url('/press/') ); ?>" class="nav-item<?php if ( is_post_type_archive('news') || is_singular('news') || is_page('press') ) echo ' active'; ?>">Press</a>
        <a href="<?php echo esc_url( home_url('/cases/') ); ?>" class="nav-item<?php if ( is_post_type_archive('case_study') || is_singular('case_study') || is_page('cases') ) echo ' active'; ?>">Cases</a>
        <a href="<?php echo esc_url( home_url('/column/') ); ?>" class="nav-item<?php if ( is_post_type_archive('column') || is_singular('column') || is_tax('column_category') ) echo ' active'; ?>">Column</a>
        <a href="<?php echo esc_url( home_url('/contact/') ); ?>" class="header-cta">Contact</a>
        <a href="<?php echo esc_url( is_user_logged_in() ? $rinascente_member_page_url : $rinascente_member_login_url ); ?>" class="btn btn-outline-light" style="min-height:auto;padding:8px 20px;font-size:0.8rem;"><?php echo is_user_logged_in() ? 'My Page' : 'Login'; ?></a>
      </nav>
    </div>
  </header>

  <!-- Mobile fullscreen nav -->
  <nav class="mobile-nav" id="mobileNav" role="navigation" aria-hidden="true">
    <a href="<?php echo esc_url( home_url('/') ); ?>" class="mobile-nav__item<?php if ( is_front_page() ) echo ' active'; ?>">Home</a>
    <a href="<?php echo esc_url( home_url('/identity/') ); ?>" class="mobile-nav__item<?php if ( is_page('identity') ) echo ' active'; ?>">Identity</a>
    <a href="<?php echo esc_url( home_url('/press/') ); ?>" class="mobile-nav__item<?php if ( is_post_type_archive('news') || is_singular('news') || is_page('press') ) echo ' active'; ?>">Press</a>
    <a href="<?php echo esc_url( home_url('/cases/') ); ?>" class="mobile-nav__item<?php if ( is_post_type_archive('case_study') || is_singular('case_study') || is_page('cases') ) echo ' active'; ?>">Cases</a>
    <a href="<?php echo esc_url( home_url('/column/') ); ?>" class="mobile-nav__item<?php if ( is_post_type_archive('column') || is_singular('column') || is_tax('column_category') ) echo ' active'; ?>">Column</a>
    <a href="<?php echo esc_url( home_url('/contact/') ); ?>" class="mobile-nav__item">Contact</a>
    <a href="<?php echo esc_url( is_user_logged_in() ? $rinascente_member_page_url : $rinascente_member_login_url ); ?>" class="mobile-nav__item<?php if ( is_page('login') || is_page('member') ) echo ' active'; ?>"><?php echo is_user_logged_in() ? 'My Page' : 'Login'; ?></a>
    <div class="mobile-nav__divider"></div>
    <a href="<?php echo esc_url( rinascente_related_site_url( 'yumeho' ) ); ?>" class="nav-external" target="_blank" rel="noopener">
      <span class="nav-external__label">Product Site</span>
      <span class="nav-external__name">YUMEHO</span>
      <svg width="12" height="12" viewBox="0 0 12 12" fill="none" style="flex-shrink:0;"><path d="M4 1.5H2.5A1 1 0 001.5 2.5v7a1 1 0 001 1h7a1 1 0 001-1V8M7.5 1.5h3v3M6 6l4.5-4.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </a>
  </nav>

  <main>
