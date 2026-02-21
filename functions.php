<?php
/**
 * Deladem IHM v2 — functions.php
 * Tout le contenu est gérable depuis l'admin WordPress.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'DELADEM_VERSION', '2.1.0' );

require_once get_template_directory() . '/inc/seeder.php';
require_once get_template_directory() . '/inc/seo.php';

/* ============================================================
   1. SETUP
   ============================================================ */
function deladem_setup() {
    load_theme_textdomain( 'deladem-ihm', get_template_directory() . '/languages' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_image_size( 'partner-logo', 300, 150, false );
    add_theme_support( 'html5', [ 'search-form', 'comment-form', 'gallery', 'caption', 'style', 'script' ] );
    add_theme_support( 'custom-logo', [ 'height' => 40, 'width' => 40, 'flex-height' => true, 'flex-width' => true ] );
    register_nav_menus( [ 'primary' => __( 'Primary Menu', 'deladem-ihm' ) ] );
}
add_action( 'after_setup_theme', 'deladem_setup' );

// Allow SVG uploads for administrators
add_filter( 'upload_mimes', function( $mimes ) {
    if ( current_user_can( 'manage_options' ) ) {
        $mimes['svg'] = 'image/svg+xml';
    }
    return $mimes;
} );
add_filter( 'wp_check_filetype_and_ext', function( $data, $file, $filename, $mimes ) {
    if ( pathinfo( $filename, PATHINFO_EXTENSION ) === 'svg' ) {
        $data['type'] = 'image/svg+xml';
        $data['ext']  = 'svg';
        $data['proper_filename'] = $filename;
    }
    return $data;
}, 10, 4 );

// Flush rewrite rules + seed demo data on theme activation
add_action( 'after_switch_theme', function() {
    deladem_cpt_projets();
    deladem_cpt_publications();
    deladem_cpt_partners();
    deladem_cpt_cv();
    deladem_cpt_financements();
    flush_rewrite_rules();
    deladem_maybe_seed();
} );


/* ============================================================
   2. TYPOGRAPHY PRESETS
   ============================================================ */
function deladem_get_typographies() {
    return [
        'editorial' => [
            'label' => 'Editorial (DM Serif + DM Sans)',
            'serif' => "'DM Serif Display', serif",
            'sans'  => "'DM Sans', sans-serif",
            'mono'  => "'JetBrains Mono', monospace",
            'url'   => 'https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap',
        ],
        'modern' => [
            'label' => 'Modern (Inter + Inter)',
            'serif' => "'Inter', sans-serif",
            'sans'  => "'Inter', sans-serif",
            'mono'  => "'Fira Code', monospace",
            'url'   => 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Fira+Code:wght@400;500&display=swap',
        ],
        'classic' => [
            'label' => 'Classic (Playfair + Source Sans)',
            'serif' => "'Playfair Display', serif",
            'sans'  => "'Source Sans 3', sans-serif",
            'mono'  => "'Source Code Pro', monospace",
            'url'   => 'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Source+Sans+3:wght@300;400;500;600&family=Source+Code+Pro:wght@400;500&display=swap',
        ],
        'elegant' => [
            'label' => 'Elegant (Cormorant + Nunito Sans)',
            'serif' => "'Cormorant Garamond', serif",
            'sans'  => "'Nunito Sans', sans-serif",
            'mono'  => "'Inconsolata', monospace",
            'url'   => 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Nunito+Sans:wght@300;400;500;600&family=Inconsolata:wght@400;500&display=swap',
        ],
        'swiss' => [
            'label' => 'Swiss (Libre Baskerville + IBM Plex)',
            'serif' => "'Libre Baskerville', serif",
            'sans'  => "'IBM Plex Sans', sans-serif",
            'mono'  => "'IBM Plex Mono', monospace",
            'url'   => 'https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=IBM+Plex+Sans:wght@300;400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap',
        ],
        'geometric' => [
            'label' => 'Geometric (Outfit + Outfit)',
            'serif' => "'Outfit', sans-serif",
            'sans'  => "'Outfit', sans-serif",
            'mono'  => "'Space Mono', monospace",
            'url'   => 'https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap',
        ],
    ];
}

/* ============================================================
   3. ASSETS
   ============================================================ */
function deladem_enqueue_assets() {
    $typo = deladem_get_typographies();
    $active = get_option( 'dlm_typography', 'editorial' );
    $fonts_url = isset( $typo[ $active ] ) ? $typo[ $active ]['url'] : $typo['editorial']['url'];

    wp_enqueue_style( 'deladem-fonts', $fonts_url, [], null );
    wp_enqueue_style( 'deladem-main',
        get_template_directory_uri() . '/assets/css/main.css',
        [ 'deladem-fonts' ], filemtime( get_template_directory() . '/assets/css/main.css' )
    );
    $layout_file = get_template_directory() . '/assets/css/skins-layout.css';
    if ( file_exists( $layout_file ) ) {
        wp_enqueue_style( 'deladem-layouts',
            get_template_directory_uri() . '/assets/css/skins-layout.css',
            [ 'deladem-main' ], filemtime( $layout_file )
        );
    }

    wp_enqueue_script( 'deladem-main',
        get_template_directory_uri() . '/assets/js/main.js',
        [], filemtime( get_template_directory() . '/assets/js/main.js' ), true
    );
}
add_action( 'wp_enqueue_scripts', 'deladem_enqueue_assets' );

// Enqueue wp.media on project edit + theme options pages
add_action( 'admin_enqueue_scripts', function( $hook ) {
    global $post_type;
    $is_projet = in_array( $hook, [ 'post.php', 'post-new.php' ] ) && $post_type === 'projet';
    $is_options = $hook === 'appearance_page_deladem-options';
    if ( ! $is_projet && ! $is_options ) return;
    wp_enqueue_media();
    wp_enqueue_script( 'deladem-admin-media',
        get_template_directory_uri() . '/assets/js/admin-media.js',
        [ 'jquery' ], DELADEM_VERSION, true
    );
} );

// Inject custom color + typography overrides into <head>
add_action( 'wp_head', function() {
    $defaults = [
        'color_accent'  => '#C94A2D',
        'color_accent2' => '#3B5BDB',
        'color_bg'      => '#F5F2EE',
        'color_ink'     => '#1A1714',
        'color_card'    => '#FDFAF7',
        'color_border'  => '#E2DDD8',
        'color_muted'   => '#7A7067',
    ];
    $map = [
        'color_accent'  => 'accent',
        'color_accent2' => 'accent2',
        'color_bg'      => 'bg',
        'color_ink'     => 'ink',
        'color_card'    => 'card',
        'color_border'  => 'border',
        'color_muted'   => 'muted',
    ];

    // Helper: hex to "r,g,b" string
    $hex_to_rgb = function( $hex ) {
        $hex = ltrim( $hex, '#' );
        return hexdec( substr($hex,0,2) ) . ',' . hexdec( substr($hex,2,2) ) . ',' . hexdec( substr($hex,4,2) );
    };

    $css = '';

    // Colors
    foreach ( $defaults as $key => $default ) {
        $val = get_option( 'dlm_' . $key, $default );
        if ( $val && $val !== $default ) {
            $prop = esc_attr( $map[ $key ] );
            $css .= '  --' . $prop . ': ' . esc_attr( $val ) . ";\n";
            if ( $key === 'color_accent' || $key === 'color_accent2' ) {
                $css .= '  --' . $prop . '-rgb: ' . $hex_to_rgb( $val ) . ";\n";
            }
        }
    }

    // Typography
    $active_typo = get_option( 'dlm_typography', 'editorial' );
    if ( $active_typo !== 'editorial' ) {
        $typos = deladem_get_typographies();
        if ( isset( $typos[ $active_typo ] ) ) {
            $t = $typos[ $active_typo ];
            $css .= '  --serif: ' . $t['serif'] . ";\n";
            $css .= '  --sans: ' . $t['sans'] . ";\n";
            $css .= '  --mono: ' . $t['mono'] . ";\n";
        }
    }

    if ( $css ) {
        echo "<style id=\"deladem-custom-overrides\">\n:root,\nhtml[data-theme=\"dark\"] {\n" . $css . "}\n</style>\n";
    }
}, 50 );

// Layout body classes
add_filter( 'body_class', function( $classes ) {
    $layouts = [
        'layout_projects'     => 'grid',
        'layout_publications' => 'list',
        'layout_cv'           => 'two-col',
        'layout_contact'      => 'two-col',
    ];
    foreach ( $layouts as $key => $default ) {
        $val = get_option( 'dlm_' . $key, $default );
        if ( $val !== $default ) {
            $classes[] = $key . '-' . sanitize_html_class( $val );
        }
    }
    return $classes;
} );


/* ============================================================
   3. CUSTOM POST TYPES
   ============================================================ */

// — Projets —
function deladem_cpt_projets() {
    register_post_type( 'projet', [
        'labels'       => [ 'name' => 'Projects', 'singular_name' => 'Project', 'add_new_item' => 'Add a project', 'edit_item' => 'Edit project', 'all_items' => 'All projects', 'menu_name' => 'Projects' ],
        'public'       => true,
        'has_archive'  => true,
        'supports'     => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
        'menu_icon'    => 'dashicons-search',
        'show_in_rest' => true,
        'rewrite'      => [ 'slug' => 'projet' ],
    ] );
}
add_action( 'init', 'deladem_cpt_projets' );

// — Publications —
function deladem_cpt_publications() {
    register_post_type( 'publication', [
        'labels'       => [ 'name' => 'Publications', 'singular_name' => 'Publication', 'add_new_item' => 'Add a publication', 'edit_item' => 'Edit publication', 'all_items' => 'All publications', 'menu_name' => 'Publications' ],
        'public'       => true,
        'has_archive'  => true,
        'supports'     => [ 'title', 'custom-fields' ],
        'menu_icon'    => 'dashicons-welcome-write-blog',
        'show_in_rest' => true,
    ] );
}
add_action( 'init', 'deladem_cpt_publications' );

// — Partenaires / Logos —
function deladem_cpt_partners() {
    register_post_type( 'partenaire', [
        'labels'       => [ 'name' => 'Partners', 'singular_name' => 'Partner', 'add_new_item' => 'Add a partner', 'edit_item' => 'Edit partner', 'all_items' => 'All partners', 'menu_name' => 'Partners' ],
        'public'       => false,
        'show_ui'      => true,
        'show_in_menu' => true,
        'supports'     => [ 'title', 'thumbnail' ],
        'menu_icon'    => 'dashicons-building',
        'show_in_rest' => true,
    ] );
}
add_action( 'init', 'deladem_cpt_partners' );

// — Entrées CV (Formations & Expériences) —
function deladem_cpt_cv() {
    register_post_type( 'cv_entree', [
        'labels'       => [ 'name' => 'CV / Background', 'singular_name' => 'CV Entry', 'add_new_item' => 'Add an entry', 'edit_item' => 'Edit entry', 'all_items' => 'All entries', 'menu_name' => 'CV / Background' ],
        'public'       => false,
        'show_ui'      => true,
        'show_in_menu' => true,
        'supports'     => [ 'title' ],
        'menu_icon'    => 'dashicons-id-alt',
        'show_in_rest' => true,
    ] );
}
add_action( 'init', 'deladem_cpt_cv' );

// — Financements —
function deladem_cpt_financements() {
    register_post_type( 'financement', [
        'labels'       => [ 'name' => 'Funding', 'singular_name' => 'Funding', 'add_new_item' => 'Add funding', 'edit_item' => 'Edit funding', 'all_items' => 'All funding', 'menu_name' => 'Funding' ],
        'public'       => true,
        'has_archive'  => true,
        'supports'     => [ 'title', 'editor' ],
        'menu_icon'    => 'dashicons-money-alt',
        'show_in_rest' => true,
        'rewrite'      => [ 'slug' => 'financement' ],
    ] );
}
add_action( 'init', 'deladem_cpt_financements' );


/* ============================================================
   4. META BOXES — Projets
   ============================================================ */
add_action( 'add_meta_boxes', function() {
    add_meta_box( 'projet_meta', 'Project Details', 'deladem_projet_meta_cb', 'projet', 'normal', 'high' );
} );

function deladem_projet_meta_cb( $post ) {
    wp_nonce_field( 'deladem_projet', 'deladem_projet_nonce' );
    $tags    = get_post_meta( $post->ID, '_projet_tags',    true );
    $ordre   = get_post_meta( $post->ID, '_projet_ordre',   true );
    $icon_id = get_post_meta( $post->ID, '_projet_icon_id', true );
    $projet_periode      = get_post_meta( $post->ID, '_projet_periode',      true );
    $projet_financements = get_post_meta( $post->ID, '_projet_financements', true );
    $projet_financements = is_array( $projet_financements ) ? $projet_financements : [];
    $projet_partenaires  = get_post_meta( $post->ID, '_projet_partenaires',  true );
    $projet_partenaires  = is_array( $projet_partenaires ) ? $projet_partenaires : [];

    $type_labels = [ 'bourse' => 'Scholarship', 'financement' => 'Funding', 'subvention' => 'Grant', 'contrat' => 'Contract', 'prix' => 'Award' ];
    $type_colors = [ 'bourse' => '#16a34a', 'financement' => '#2563eb', 'subvention' => '#C94A2D', 'contrat' => '#7c3aed', 'prix' => '#d97706' ];
    ?>
    <style>
        .dlm-meta-section { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin-bottom: 16px; }
        .dlm-meta-section-title { font-size: 13px; font-weight: 600; color: #374151; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 12px; padding-bottom: 8px; border-bottom: 2px solid #C94A2D; }
        .dlm-meta-row { display: flex; gap: 16px; margin-bottom: 12px; flex-wrap: wrap; }
        .dlm-meta-row:last-child { margin-bottom: 0; }
        .dlm-meta-field { flex: 1; min-width: 200px; }
        .dlm-meta-field label { display: block; font-weight: 600; font-size: 12px; color: #6b7280; margin-bottom: 4px; }
        .dlm-meta-field input[type="text"],
        .dlm-meta-field input[type="number"] { width: 100%; }
        .dlm-meta-field--small { flex: 0 0 100px; min-width: 80px; }
        .dlm-checkbox-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 6px; max-height: 200px; overflow-y: auto; padding: 8px; background: #fff; border: 1px solid #d1d5db; border-radius: 6px; }
        .dlm-checkbox-item { display: flex; align-items: center; gap: 8px; padding: 6px 10px; border-radius: 4px; transition: background .15s; cursor: pointer; font-size: 13px; }
        .dlm-checkbox-item:hover { background: #f0f6ff; }
        .dlm-checkbox-item input[type="checkbox"]:checked + .dlm-checkbox-label { font-weight: 600; color: #1d4ed8; }
        .dlm-checkbox-label { flex: 1; }
        .dlm-checkbox-badge { display: inline-block; font-size: 10px; font-weight: 600; padding: 1px 6px; border-radius: 3px; color: #fff; }
        .dlm-empty-state { padding: 12px; text-align: center; color: #9ca3af; font-size: 13px; background: #fff; border: 1px dashed #d1d5db; border-radius: 6px; }
        .dlm-empty-state a { color: #C94A2D; text-decoration: none; font-weight: 600; }
    </style>

    <!-- Main Information -->
    <div class="dlm-meta-section">
        <h4 class="dlm-meta-section-title">Main Information</h4>
        <div class="dlm-meta-row">
            <div class="dlm-meta-field">
                <label>SVG Icon</label>
                <div class="dlm-icon-field">
                    <input type="hidden" name="projet_icon_id" value="<?php echo esc_attr( $icon_id ); ?>" class="dlm-icon-id">
                    <div class="dlm-icon-preview" style="margin-bottom:8px;">
                        <?php if ( $icon_id ) :
                            $url = wp_get_attachment_url( $icon_id );
                            if ( $url ) : ?>
                                <img src="<?php echo esc_url( $url ); ?>" style="width:48px;height:48px;" alt="">
                            <?php endif;
                        endif; ?>
                    </div>
                    <button type="button" class="button dlm-projet-icon-upload">Choose</button>
                    <button type="button" class="button dlm-icon-remove" style="<?php echo $icon_id ? '' : 'display:none;'; ?>">Remove</button>
                    <p class="description">SVG recommended. Leave empty for default icon.</p>
                </div>
            </div>
        </div>
        <div class="dlm-meta-row">
            <div class="dlm-meta-field">
                <label>Technology Tags</label>
                <input type="text" name="projet_tags" value="<?php echo esc_attr($tags); ?>"
                    placeholder="Python, LSL, EEG, Unity (comma separated)">
            </div>
        </div>
        <div class="dlm-meta-row">
            <div class="dlm-meta-field">
                <label>Project Period</label>
                <input type="text" name="projet_periode" value="<?php echo esc_attr( $projet_periode ); ?>" placeholder="2023 — Present">
            </div>
            <div class="dlm-meta-field dlm-meta-field--small">
                <label>Order</label>
                <input type="number" name="projet_ordre" value="<?php echo esc_attr($ordre ?: 10); ?>" min="1" max="99">
            </div>
        </div>
    </div>

    <!-- Financements liés -->
    <div class="dlm-meta-section">
        <h4 class="dlm-meta-section-title">Linked Funding</h4>
        <?php
        $fin_q = new WP_Query([ 'post_type' => 'financement', 'posts_per_page' => -1, 'post_status' => 'publish', 'meta_key' => '_financement_ordre', 'orderby' => 'meta_value_num', 'order' => 'ASC' ]);
        if ( $fin_q->have_posts() ) : ?>
        <div class="dlm-checkbox-grid">
            <?php while ( $fin_q->have_posts() ) : $fin_q->the_post();
                $ft = get_post_meta( get_the_ID(), '_financement_type', true );
                $fc = $type_colors[ $ft ] ?? '#6b7280';
                $fl = $type_labels[ $ft ] ?? '';
            ?>
            <label class="dlm-checkbox-item">
                <input type="checkbox" name="projet_financements[]" value="<?php echo get_the_ID(); ?>"
                    <?php checked( in_array( get_the_ID(), $projet_financements ) ); ?>>
                <span class="dlm-checkbox-label"><?php the_title(); ?></span>
                <?php if ( $fl ) : ?>
                <span class="dlm-checkbox-badge" style="background:<?php echo esc_attr( $fc ); ?>"><?php echo esc_html( $fl ); ?></span>
                <?php endif; ?>
            </label>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
        <?php else : ?>
        <div class="dlm-empty-state">
            No funding created yet. <a href="<?php echo admin_url('post-new.php?post_type=financement'); ?>">Add funding</a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Partenaires / Collaborateurs -->
    <div class="dlm-meta-section">
        <h4 class="dlm-meta-section-title">Partners / Collaborators</h4>
        <?php
        $part_q = new WP_Query([ 'post_type' => 'partenaire', 'posts_per_page' => -1, 'post_status' => 'publish', 'meta_key' => '_partenaire_ordre', 'orderby' => 'meta_value_num', 'order' => 'ASC' ]);
        if ( $part_q->have_posts() ) : ?>
        <div class="dlm-checkbox-grid">
            <?php while ( $part_q->have_posts() ) : $part_q->the_post();
                $p_init = get_post_meta( get_the_ID(), '_partenaire_initiales', true ) ?: mb_substr( get_the_title(), 0, 2 );
            ?>
            <label class="dlm-checkbox-item">
                <input type="checkbox" name="projet_partenaires[]" value="<?php echo get_the_ID(); ?>"
                    <?php checked( in_array( get_the_ID(), $projet_partenaires ) ); ?>>
                <span class="dlm-checkbox-badge" style="background:#64748b"><?php echo esc_html( $p_init ); ?></span>
                <span class="dlm-checkbox-label"><?php the_title(); ?></span>
            </label>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
        <?php else : ?>
        <div class="dlm-empty-state">
            No partners created yet. <a href="<?php echo admin_url('post-new.php?post_type=partenaire'); ?>">Add a partner</a>
        </div>
        <?php endif; ?>
    </div>
    <?php
}

add_action( 'save_post_projet', function( $post_id ) {
    if ( !isset($_POST['deladem_projet_nonce']) || !wp_verify_nonce($_POST['deladem_projet_nonce'], 'deladem_projet') ) return;
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
    if ( !current_user_can('edit_post', $post_id) ) return;
    update_post_meta( $post_id, '_projet_tags',    sanitize_text_field( wp_unslash( $_POST['projet_tags']  ?? '' ) ) );
    update_post_meta( $post_id, '_projet_ordre',   absint($_POST['projet_ordre'] ?? 10) );
    update_post_meta( $post_id, '_projet_icon_id', absint($_POST['projet_icon_id'] ?? 0) );
    update_post_meta( $post_id, '_projet_periode', sanitize_text_field( wp_unslash( $_POST['projet_periode'] ?? '' ) ) );
    $fin_ids = array_map( 'absint', (array) ( $_POST['projet_financements'] ?? [] ) );
    update_post_meta( $post_id, '_projet_financements', array_filter( $fin_ids ) );
    $part_ids = array_map( 'absint', (array) ( $_POST['projet_partenaires'] ?? [] ) );
    update_post_meta( $post_id, '_projet_partenaires', array_filter( $part_ids ) );
} );


/* ============================================================
   5. META BOXES — Publications
   ============================================================ */
add_action( 'add_meta_boxes', function() {
    add_meta_box( 'pub_meta', 'Publication Details', 'deladem_pub_meta_cb', 'publication', 'normal', 'high' );
} );

function deladem_pub_meta_cb( $post ) {
    wp_nonce_field( 'deladem_pub', 'deladem_pub_nonce' );
    $annee   = get_post_meta( $post->ID, '_pub_annee',   true );
    $auteurs = get_post_meta( $post->ID, '_pub_auteurs', true );
    $revue   = get_post_meta( $post->ID, '_pub_revue',   true );
    $type    = get_post_meta( $post->ID, '_pub_type',    true );
    $doi     = get_post_meta( $post->ID, '_pub_doi',     true );
    ?>
    <table class="form-table">
        <tr>
            <th><label>Year</label></th>
            <td><input type="number" name="pub_annee" value="<?php echo esc_attr($annee ?: date('Y')); ?>" style="width:100px" min="1990" max="2099"></td>
        </tr>
        <tr>
            <th><label>Authors</label></th>
            <td><input type="text" name="pub_auteurs" value="<?php echo esc_attr($auteurs); ?>" class="large-text" placeholder="Deladem, B.-A. J. Ménélas"></td>
        </tr>
        <tr>
            <th><label>Journal / Conference</label></th>
            <td><input type="text" name="pub_revue" value="<?php echo esc_attr($revue); ?>" class="large-text" placeholder="ACM CHI 2024, Proceedings of..."></td>
        </tr>
        <tr>
            <th><label>Type</label></th>
            <td>
                <select name="pub_type">
                    <?php
                    $types = [ 'conference' => 'Conference', 'journal' => 'Journal', 'poster' => 'Poster', 'rapport' => 'Report', 'these' => 'Thesis', 'workshop' => 'Workshop' ];
                    foreach ( $types as $val => $label ) {
                        printf( '<option value="%s" %s>%s</option>', $val, selected($type, $val, false), $label );
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label>DOI / URL</label></th>
            <td>
                <input type="url" name="pub_doi" value="<?php echo esc_attr($doi); ?>" class="large-text" placeholder="https://doi.org/...">
                <p class="description">Link to the publication (optional)</p>
            </td>
        </tr>
    </table>
    <?php
}

add_action( 'save_post_publication', function( $post_id ) {
    if ( !isset($_POST['deladem_pub_nonce']) || !wp_verify_nonce($_POST['deladem_pub_nonce'], 'deladem_pub') ) return;
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
    if ( !current_user_can('edit_post', $post_id) ) return;
    update_post_meta( $post_id, '_pub_annee',   absint($_POST['pub_annee'] ?? date('Y')) );
    update_post_meta( $post_id, '_pub_auteurs', sanitize_text_field( wp_unslash( $_POST['pub_auteurs'] ?? '' ) ) );
    update_post_meta( $post_id, '_pub_revue',   sanitize_text_field( wp_unslash( $_POST['pub_revue']   ?? '' ) ) );
    update_post_meta( $post_id, '_pub_type',    sanitize_text_field( wp_unslash( $_POST['pub_type']    ?? 'conference' ) ) );
    update_post_meta( $post_id, '_pub_doi',     esc_url_raw($_POST['pub_doi']             ?? '') );
} );


/* ============================================================
   6. META BOXES — Partenaires
   ============================================================ */
add_action( 'add_meta_boxes', function() {
    add_meta_box( 'partenaire_meta', 'Partner Information', 'deladem_partenaire_meta_cb', 'partenaire', 'normal', 'high' );
} );

function deladem_partenaire_meta_cb( $post ) {
    wp_nonce_field( 'deladem_partenaire', 'deladem_partenaire_nonce' );
    $url     = get_post_meta( $post->ID, '_partenaire_url',     true );
    $type    = get_post_meta( $post->ID, '_partenaire_type',    true );
    $ordre   = get_post_meta( $post->ID, '_partenaire_ordre',   true );
    $initiales = get_post_meta( $post->ID, '_partenaire_initiales', true );
    ?>
    <table class="form-table">
        <tr>
            <th><label>Partnership Type</label></th>
            <td>
                <select name="partenaire_type">
                    <?php
                    $types = [ 'institution' => 'Institution / University', 'entreprise' => 'Company', 'labo' => 'Laboratory', 'consulting' => 'Consulting / Freelance', 'autre' => 'Other' ];
                    foreach ( $types as $val => $label ) {
                        printf( '<option value="%s" %s>%s</option>', $val, selected($type, $val, false), $label );
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label>Initials (if no logo)</label></th>
            <td>
                <input type="text" name="partenaire_initiales" value="<?php echo esc_attr($initiales); ?>" style="width:80px;text-align:center" maxlength="4" placeholder="UQAC">
                <p class="description">Displayed if no featured image is set</p>
            </td>
        </tr>
        <tr>
            <th><label>Website URL</label></th>
            <td><input type="url" name="partenaire_url" value="<?php echo esc_attr($url); ?>" class="large-text" placeholder="https://..."></td>
        </tr>
        <tr>
            <th><label>Display Order</label></th>
            <td><input type="number" name="partenaire_ordre" value="<?php echo esc_attr($ordre ?: 10); ?>" style="width:80px" min="1" max="99"></td>
        </tr>
    </table>
    <p class="description" style="margin-top:1rem;padding:1rem;background:#f8f8f8;border-left:3px solid #2271b1;">
        <strong>How to add a logo:</strong> Use the <strong>Featured Image</strong> panel (right sidebar) to upload the company logo.
        Recommended formats: transparent PNG or SVG. Recommended size: 300x100px.
    </p>
    <?php
}

add_action( 'save_post_partenaire', function( $post_id ) {
    if ( !isset($_POST['deladem_partenaire_nonce']) || !wp_verify_nonce($_POST['deladem_partenaire_nonce'], 'deladem_partenaire') ) return;
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
    if ( !current_user_can('edit_post', $post_id) ) return;
    update_post_meta( $post_id, '_partenaire_url',       esc_url_raw($_POST['partenaire_url']         ?? '') );
    update_post_meta( $post_id, '_partenaire_type',      sanitize_text_field( wp_unslash( $_POST['partenaire_type'] ?? '' ) ) );
    update_post_meta( $post_id, '_partenaire_ordre',     absint($_POST['partenaire_ordre']             ?? 10) );
    update_post_meta( $post_id, '_partenaire_initiales', sanitize_text_field( wp_unslash( $_POST['partenaire_initiales'] ?? '' ) ) );
} );


/* ============================================================
   7. META BOXES — Entrées CV
   ============================================================ */
add_action( 'add_meta_boxes', function() {
    add_meta_box( 'cv_meta', 'Entry Details', 'deladem_cv_meta_cb', 'cv_entree', 'normal', 'high' );
} );

function deladem_cv_meta_cb( $post ) {
    wp_nonce_field( 'deladem_cv', 'deladem_cv_nonce' );
    $categorie    = get_post_meta( $post->ID, '_cv_categorie',    true );
    $periode      = get_post_meta( $post->ID, '_cv_periode',      true );
    $etablissement = get_post_meta( $post->ID, '_cv_etablissement', true );
    $ordre        = get_post_meta( $post->ID, '_cv_ordre',        true );
    ?>
    <table class="form-table">
        <tr>
            <th><label>Category</label></th>
            <td>
                <select name="cv_categorie">
                    <?php
                    $cats = [ 'formation' => 'Education', 'experience' => 'Professional Experience', 'competence' => 'Skills' ];
                    foreach ( $cats as $val => $label ) {
                        printf( '<option value="%s" %s>%s</option>', $val, selected($categorie, $val, false), $label );
                    }
                    ?>
                </select>
                <p class="description">Determines which column the entry appears in</p>
            </td>
        </tr>
        <tr>
            <th><label>Period / Dates</label></th>
            <td>
                <input type="text" name="cv_periode" value="<?php echo esc_attr($periode); ?>" class="regular-text" placeholder="2022 — Present">
                <p class="description">E.g.: 2022 — Present, 2019 — 2022, Prior</p>
            </td>
        </tr>
        <tr>
            <th><label>Institution / Location</label></th>
            <td>
                <input type="text" name="cv_etablissement" value="<?php echo esc_attr($etablissement); ?>" class="large-text" placeholder="UQAC, Chicoutimi QC">
                <p class="description">The main editor content will be used as subtitle/description</p>
            </td>
        </tr>
        <tr>
            <th><label>Display Order</label></th>
            <td>
                <input type="number" name="cv_ordre" value="<?php echo esc_attr($ordre ?: 10); ?>" style="width:80px" min="1">
                <p class="description">1 = displayed first in its category</p>
            </td>
        </tr>
    </table>
    <?php
}

add_action( 'save_post_cv_entree', function( $post_id ) {
    if ( !isset($_POST['deladem_cv_nonce']) || !wp_verify_nonce($_POST['deladem_cv_nonce'], 'deladem_cv') ) return;
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
    if ( !current_user_can('edit_post', $post_id) ) return;
    update_post_meta( $post_id, '_cv_categorie',     sanitize_text_field( wp_unslash( $_POST['cv_categorie']      ?? 'formation' ) ) );
    update_post_meta( $post_id, '_cv_periode',       sanitize_text_field( wp_unslash( $_POST['cv_periode']        ?? '' ) ) );
    update_post_meta( $post_id, '_cv_etablissement', sanitize_text_field( wp_unslash( $_POST['cv_etablissement']  ?? '' ) ) );
    update_post_meta( $post_id, '_cv_ordre',         absint($_POST['cv_ordre'] ?? 10) );
} );


/* ============================================================
   7b. META BOXES — Financements
   ============================================================ */
add_action( 'add_meta_boxes', function() {
    add_meta_box( 'financement_meta', 'Funding Details', 'deladem_financement_meta_cb', 'financement', 'normal', 'high' );
} );

function deladem_financement_meta_cb( $post ) {
    wp_nonce_field( 'deladem_financement', 'deladem_financement_nonce' );
    $partenaire_id = get_post_meta( $post->ID, '_financement_partenaire',   true );
    $type          = get_post_meta( $post->ID, '_financement_type',         true );
    $montant       = get_post_meta( $post->ID, '_financement_montant',      true );
    $devise        = get_post_meta( $post->ID, '_financement_devise',       true );
    $modalite      = get_post_meta( $post->ID, '_financement_modalite',     true );
    $statut        = get_post_meta( $post->ID, '_financement_statut',       true );
    $role          = get_post_meta( $post->ID, '_financement_role',          true );
    $beneficiaire  = get_post_meta( $post->ID, '_financement_beneficiaire', true );
    $periode       = get_post_meta( $post->ID, '_financement_periode',      true );
    $description   = get_post_meta( $post->ID, '_financement_description',  true );
    $url_externe   = get_post_meta( $post->ID, '_financement_url',          true );
    $ordre         = get_post_meta( $post->ID, '_financement_ordre',        true );

    $partenaires_q = new WP_Query([ 'post_type' => 'partenaire', 'posts_per_page' => -1, 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'ASC' ]);
    ?>
    <table class="form-table">
        <tr>
            <th><label>Partner / Organization</label></th>
            <td>
                <select name="financement_partenaire">
                    <option value="">— Select a partner —</option>
                    <?php while ( $partenaires_q->have_posts() ) : $partenaires_q->the_post(); ?>
                    <option value="<?php echo get_the_ID(); ?>" <?php selected( $partenaire_id, get_the_ID() ); ?>><?php the_title(); ?></option>
                    <?php endwhile; wp_reset_postdata(); ?>
                </select>
                <p class="description">Add the partner in <strong>Partners</strong> first if needed.</p>
            </td>
        </tr>
        <tr>
            <th><label>Funding Type</label></th>
            <td>
                <select name="financement_type">
                    <?php
                    $types = [ 'bourse' => 'Scholarship', 'financement' => 'Funding', 'subvention' => 'Grant', 'contrat' => 'Contract', 'prix' => 'Award' ];
                    foreach ( $types as $val => $label ) {
                        printf( '<option value="%s" %s>%s</option>', $val, selected( $type, $val, false ), $label );
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label>Status</label></th>
            <td>
                <select name="financement_statut">
                    <?php
                    $statuts = [ 'actif' => 'Active / Ongoing', 'termine' => 'Completed', 'en_attente' => 'Pending / Submitted', 'refuse' => 'Declined', 'suspendu' => 'Suspended' ];
                    foreach ( $statuts as $val => $label ) {
                        printf( '<option value="%s" %s>%s</option>', $val, selected( $statut, $val, false ), $label );
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label>R&ocirc;le</label></th>
            <td>
                <select name="financement_role">
                    <?php
                    $roles = [
                        'chercheur_principal' => 'Principal Investigator (PI)',
                        'co_chercheur'        => 'Co-Investigator (Co-PI)',
                        'collaborateur'       => 'Collaborator',
                        'stagiaire'           => 'Intern / Student',
                        'postdoc'             => 'Postdoctoral Researcher',
                        'assistant'           => 'Research Assistant',
                        'coordonnateur'       => 'Coordinator',
                        'responsable'         => 'Scientific Director',
                        'membre'              => 'Team Member',
                        'beneficiaire'        => 'Beneficiary',
                        'autre'               => 'Other',
                    ];
                    foreach ( $roles as $val => $label ) {
                        printf( '<option value="%s" %s>%s</option>', $val, selected( $role, $val, false ), $label );
                    }
                    ?>
                </select>
                <p class="description">Your role in this funding</p>
            </td>
        </tr>
        <tr>
            <th><label>Amount</label></th>
            <td>
                <div style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap;">
                    <input type="text" name="financement_montant" value="<?php echo esc_attr( $montant ); ?>" style="width:160px" placeholder="15 000">
                    <select name="financement_devise" style="width:120px">
                        <?php
                        $devises = [ 'CAD' => 'CAD ($)', 'USD' => 'USD ($)', 'EUR' => 'EUR (€)', 'GBP' => 'GBP (£)', 'XOF' => 'XOF (FCFA)', 'autre' => 'Other' ];
                        foreach ( $devises as $val => $label ) {
                            printf( '<option value="%s" %s>%s</option>', $val, selected( $devise, $val, false ), $label );
                        }
                        ?>
                    </select>
                    <select name="financement_modalite" style="width:220px">
                        <?php
                        $modalites = [
                            'ponctuel'    => 'One-time (single payment)',
                            'par_an'      => 'Per year',
                            'par_mois'    => 'Per month',
                            'par_session' => 'Per session / term',
                            'par_semestre'=> 'Per semester',
                            'total'       => 'Total amount (full duration)',
                            'renouvelable'=> 'Renewable',
                            'degressif'   => 'Decreasing',
                            'variable'    => 'Variable (conditional)',
                            'en_nature'   => 'In-kind (equipment, services)',
                            'exoneration' => 'Tuition waiver / exemption',
                            'autre'       => 'Other',
                        ];
                        foreach ( $modalites as $val => $label ) {
                            printf( '<option value="%s" %s>%s</option>', $val, selected( $modalite, $val, false ), $label );
                        }
                        ?>
                    </select>
                </div>
                <p class="description">Leave amount empty if not applicable or confidential</p>
            </td>
        </tr>
        <tr>
            <th><label>Beneficiary</label></th>
            <td>
                <input type="text" name="financement_beneficiaire" value="<?php echo esc_attr( $beneficiaire ); ?>" class="regular-text" placeholder="Self / Beneficiary name">
            </td>
        </tr>
        <tr>
            <th><label>Period</label></th>
            <td>
                <input type="text" name="financement_periode" value="<?php echo esc_attr( $periode ); ?>" class="regular-text" placeholder="2022 — 2024">
                <p class="description">E.g.: 2022 — 2024, 2023 — Present</p>
            </td>
        </tr>
        <tr>
            <th><label>Description / Notes</label></th>
            <td>
                <textarea name="financement_description" rows="3" class="large-text" placeholder="Special conditions, funding details..."><?php echo esc_textarea( $description ); ?></textarea>
                <p class="description">Optional. Additional details visible on the site.</p>
            </td>
        </tr>
        <tr>
            <th><label>External Link</label></th>
            <td>
                <input type="url" name="financement_url" value="<?php echo esc_attr( $url_externe ); ?>" class="large-text" placeholder="https://example.com/funding-page">
                <p class="description">Optional. Link to the funding program, award page or grant portal.</p>
            </td>
        </tr>
        <tr>
            <th><label>Display Order</label></th>
            <td>
                <input type="number" name="financement_ordre" value="<?php echo esc_attr( $ordre ?: 10 ); ?>" style="width:80px" min="1" max="99">
                <p class="description">1 = displayed first</p>
            </td>
        </tr>
    </table>
    <?php
}

add_action( 'save_post_financement', function( $post_id ) {
    if ( !isset($_POST['deladem_financement_nonce']) || !wp_verify_nonce($_POST['deladem_financement_nonce'], 'deladem_financement') ) return;
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
    if ( !current_user_can('edit_post', $post_id) ) return;
    update_post_meta( $post_id, '_financement_partenaire',   absint( $_POST['financement_partenaire'] ?? 0 ) );
    update_post_meta( $post_id, '_financement_type',         sanitize_text_field( wp_unslash( $_POST['financement_type']    ?? 'financement' ) ) );
    update_post_meta( $post_id, '_financement_montant',      sanitize_text_field( wp_unslash( $_POST['financement_montant'] ?? '' ) ) );
    update_post_meta( $post_id, '_financement_devise',       sanitize_text_field( wp_unslash( $_POST['financement_devise']  ?? 'CAD' ) ) );
    update_post_meta( $post_id, '_financement_modalite',     sanitize_text_field( wp_unslash( $_POST['financement_modalite'] ?? 'ponctuel' ) ) );
    update_post_meta( $post_id, '_financement_statut',       sanitize_text_field( wp_unslash( $_POST['financement_statut']  ?? 'actif' ) ) );
    update_post_meta( $post_id, '_financement_role',          sanitize_text_field( wp_unslash( $_POST['financement_role'] ?? 'chercheur_principal' ) ) );
    update_post_meta( $post_id, '_financement_beneficiaire', sanitize_text_field( wp_unslash( $_POST['financement_beneficiaire'] ?? '' ) ) );
    update_post_meta( $post_id, '_financement_periode',      sanitize_text_field( wp_unslash( $_POST['financement_periode'] ?? '' ) ) );
    update_post_meta( $post_id, '_financement_description',  sanitize_textarea_field( wp_unslash( $_POST['financement_description'] ?? '' ) ) );
    update_post_meta( $post_id, '_financement_url',          esc_url_raw( wp_unslash( $_POST['financement_url'] ?? '' ) ) );
    update_post_meta( $post_id, '_financement_ordre',        absint( $_POST['financement_ordre'] ?? 10 ) );
} );


/* ============================================================
   8. PAGE D'OPTIONS — Informations du site
   ============================================================ */
add_action( 'admin_menu', function() {
    add_theme_page(
        'Theme Options',
        'Deladem Options',
        'manage_options',
        'deladem-options',
        'deladem_options_page'
    );
} );

function deladem_options_page() {
    if ( isset($_POST['deladem_save']) && check_admin_referer('deladem_options_save') ) {

        // Site tag
        update_option( 'dlm_site_tag', sanitize_text_field( wp_unslash( $_POST['site_tag'] ?? 'HCI' ) ) );

        // Typography
        $typo_val = sanitize_text_field( wp_unslash( $_POST['typography'] ?? 'editorial' ) );
        $valid_typos = array_keys( deladem_get_typographies() );
        update_option( 'dlm_typography', in_array( $typo_val, $valid_typos, true ) ? $typo_val : 'editorial' );

        $text_fields = [
            'hero_etiquette', 'hero_titre_ligne1', 'hero_titre_em', 'hero_titre_ligne3',
            'hero_btn1_label', 'hero_btn1_url', 'hero_btn2_label', 'hero_btn2_url',
            'hero_stat1_num', 'hero_stat1_label', 'hero_stat2_num', 'hero_stat2_label',
            'hero_stat3_num', 'hero_stat3_label', 'hero_stat4_num', 'hero_stat4_label',
            'about_titre', 'about_sous_titre', 'about_description',
            'info_institution', 'info_labo', 'info_directeur', 'info_localisation', 'info_langues',
            'contact_email', 'contact_github', 'contact_linkedin', 'contact_orcid', 'contact_institution_url',
            'seo_description',
            'partners_titre', 'partners_label', 'partners_description',
            'financements_label', 'financements_titre', 'financements_description',
            'footer_texte', 'footer_mention',
        ];

        foreach ( $text_fields as $f ) {
            update_option( 'dlm_' . $f, sanitize_textarea_field( wp_unslash( $_POST[$f] ?? '' ) ) );
        }

        // Champs riches (HTML autorisé)
        update_option( 'dlm_hero_description', wp_kses_post( wp_unslash( $_POST['hero_description'] ?? '' ) ) );
        update_option( 'dlm_about_texte', wp_kses_post( wp_unslash( $_POST['about_texte'] ?? '' ) ) );

        // Badges illimités
        $badges = array_filter( array_map( 'sanitize_text_field', wp_unslash( (array) ( $_POST['hero_badges'] ?? [] ) ) ) );
        update_option( 'dlm_hero_badges', $badges );

        // Directeurs de mémoire multiples
        $directeurs = array_filter( array_map( 'sanitize_text_field', wp_unslash( (array) ( $_POST['info_directeurs'] ?? [] ) ) ) );
        update_option( 'dlm_info_directeurs', $directeurs );

        // Laboratoires multiples
        $labos = array_filter( array_map( 'sanitize_text_field', wp_unslash( (array) ( $_POST['info_labos'] ?? [] ) ) ) );
        update_option( 'dlm_info_labos', $labos );

        // Intérêts de recherche
        $interets = [];
        $int_titres  = wp_unslash( (array) ( $_POST['interet_titre'] ?? [] ) );
        $int_descs   = wp_unslash( (array) ( $_POST['interet_desc'] ?? [] ) );
        $int_urls    = (array) ( $_POST['interet_url'] ?? [] );
        $int_icon_ids = (array) ( $_POST['interet_icon_id'] ?? [] );
        foreach ( $int_titres as $idx => $titre ) {
            $titre = sanitize_text_field( $titre );
            if ( $titre ) {
                $interets[] = [
                    'titre'   => $titre,
                    'desc'    => wp_kses_post( $int_descs[ $idx ] ?? '' ),
                    'url'     => esc_url_raw( $int_urls[ $idx ] ?? '' ),
                    'icon_id' => absint( $int_icon_ids[ $idx ] ?? 0 ),
                ];
            }
        }
        update_option( 'dlm_interets', $interets );

        echo '<div class="notice notice-success is-dismissible"><p>Options saved successfully!</p></div>';
    }

    // Valeurs par défaut
    $d = [
        'hero_etiquette'    => 'PhD Student in Computer Science',
        'hero_titre_ligne1' => 'Researcher in',
        'hero_titre_em'     => 'Human–Computer Interaction',
        'hero_titre_ligne3' => '',
        'hero_description'  => 'I design multi-sensor physiological data acquisition systems to understand how humans interact with digital technologies.',
        'hero_btn1_label'   => 'My Research →',
        'hero_btn1_url'     => '#research',
        'hero_btn2_label'   => 'Publications',
        'hero_btn2_url'     => '#publications',
        'hero_stat1_num'    => '4+', 'hero_stat1_label' => 'Integrated Sensors',
        'hero_stat2_num'    => 'LSL', 'hero_stat2_label' => 'Synchronization',
        'hero_stat3_num'    => 'VR',  'hero_stat3_label' => 'Unity / XR',
        'hero_stat4_num'    => 'PhD', 'hero_stat4_label' => 'UQAC · LRIT',
        'about_titre'       => 'Understanding Humans Through Data',
        'about_description' => 'Background, research context and academic information.',
        'about_texte'       => '',
        'info_institution'  => 'Université du Québec à Chicoutimi (UQAC)',
        'info_labo'         => 'Interfaces & Technologies Research Lab',
        'info_directeur'    => 'Prof. Bob-Antoine Jerry Ménélas',
        'info_localisation' => 'Quebec, Canada',
        'info_langues'      => 'French · English',
        'contact_email'     => '',
        'contact_github'    => '',
        'contact_linkedin'  => '',
        'contact_orcid'     => '',
        'contact_institution_url' => 'https://uqac.ca',
        'seo_description'   => '',
        'partners_label'    => 'Collaborations',
        'partners_titre'    => 'Partner Companies & Institutions',
        'partners_description' => 'Research partners and institutional collaborators.',
        'financements_label'       => 'Funding',
        'financements_titre'       => 'Research Funding & Awards',
        'financements_description' => 'Scholarships, grants and institutional support.',
        'footer_texte'      => '© ' . date('Y') . ' Deladem — HCI Researcher',
        'footer_mention'    => 'Built with passion · Quebec, Canada',
    ];

    function g( $key, $defaults ) {
        return esc_attr( get_option( 'dlm_' . $key, $defaults[$key] ?? '' ) );
    }
    ?>

    <div class="wrap">
        <h1>Deladem IHM Theme Options</h1>
        <p style="color:#666;margin-bottom:2rem;">All information displayed on the site can be edited here. Don't forget to save.</p>

        <form method="post">
            <?php wp_nonce_field('deladem_options_save'); ?>

            <!-- ──── HEADER ──── -->
            <div style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:1.5rem;margin-bottom:1.5rem;">
                <h2 style="margin-bottom:1rem;border-bottom:2px solid #C94A2D;padding-bottom:.5rem;">Header</h2>
                <table class="form-table">
                    <tr><th>Site Tag</th>
                        <td>
                            <input type="text" name="site_tag" value="<?php echo esc_attr( get_option('dlm_site_tag','HCI') ); ?>" class="regular-text" placeholder="HCI">
                            <p class="description">Displayed next to the site name: "Name · <strong>Tag</strong>". Leave empty to hide.</p>
                        </td></tr>
                    <tr><th>Typography</th>
                        <td>
                            <select name="typography">
                                <?php $current_typo = get_option('dlm_typography','editorial');
                                foreach ( deladem_get_typographies() as $slug => $t ) :
                                    printf('<option value="%s"%s>%s</option>', esc_attr($slug), selected($current_typo, $slug, false), esc_html($t['label']));
                                endforeach; ?>
                            </select>
                            <p class="description">Font combination for headings, body text and code.</p>
                        </td></tr>
                </table>
            </div>

            <!-- ──── HERO ──── -->
            <div style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:1.5rem;margin-bottom:1.5rem;">
                <h2 style="margin-bottom:1rem;border-bottom:2px solid #C94A2D;padding-bottom:.5rem;">Hero Section (main banner)</h2>
                <table class="form-table">
                    <tr><th>Top Label</th>
                        <td><input type="text" name="hero_etiquette" value="<?php echo g('hero_etiquette',$d); ?>" class="regular-text" placeholder="PhD Student in Computer Science"></td></tr>
                    <tr><th>Title — Line 1</th>
                        <td><input type="text" name="hero_titre_ligne1" value="<?php echo g('hero_titre_ligne1',$d); ?>" class="regular-text" placeholder="Researcher in"></td></tr>
                    <tr><th>Title — Accent text (italic)</th>
                        <td>
                            <input type="text" name="hero_titre_em" value="<?php echo g('hero_titre_em',$d); ?>" class="large-text" placeholder="Interaction Humain–Machine">
                            <p class="description">This text is displayed in red italic</p>
                        </td></tr>
                    <tr><th>Title — Line 3 (optional)</th>
                        <td><input type="text" name="hero_titre_ligne3" value="<?php echo g('hero_titre_ligne3',$d); ?>" class="regular-text"></td></tr>
                    <tr><th>Description</th>
                        <td>
                            <?php wp_editor( get_option( 'dlm_hero_description', $d['hero_description'] ), 'hero_description', [
                                'textarea_name' => 'hero_description',
                                'media_buttons' => false,
                                'textarea_rows' => 4,
                                'teeny'         => true,
                                'quicktags'     => true,
                            ] ); ?>
                        </td></tr>
                    <tr><th>Primary Button — Text</th>
                        <td><input type="text" name="hero_btn1_label" value="<?php echo g('hero_btn1_label',$d); ?>" class="regular-text"></td></tr>
                    <tr><th>Primary Button — Link</th>
                        <td><input type="text" name="hero_btn1_url" value="<?php echo g('hero_btn1_url',$d); ?>" class="regular-text" placeholder="#research ou https://..."></td></tr>
                    <tr><th>Secondary Button — Text</th>
                        <td><input type="text" name="hero_btn2_label" value="<?php echo g('hero_btn2_label',$d); ?>" class="regular-text"></td></tr>
                    <tr><th>Secondary Button — Link</th>
                        <td><input type="text" name="hero_btn2_url" value="<?php echo g('hero_btn2_url',$d); ?>" class="regular-text"></td></tr>
                </table>
                <h3 style="margin:1rem 0 .5rem;">Floating Badges (sensors / technologies) — unlimited</h3>
                <div id="dlm-badges-wrap">
                    <?php
                    $badges = get_option( 'dlm_hero_badges', [ 'EEG · Neurosity Crown', 'ECG · Polar H10', 'Eye Tracking', 'Thermal Camera' ] );
                    foreach ( (array) $badges as $b ) : ?>
                    <div class="dlm-repeater-row" style="display:flex;gap:.5rem;margin-bottom:.5rem;align-items:center;">
                        <input type="text" name="hero_badges[]" value="<?php echo esc_attr( $b ); ?>" class="regular-text" placeholder="Badge name">
                        <button type="button" class="button dlm-remove-row" title="Remove">&times;</button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="button" id="dlm-add-badge">+ Add a badge</button>
                <h3 style="margin:1rem 0 .5rem;">Statistics (dark panel)</h3>
                <table class="form-table">
                    <?php for ( $i = 1; $i <= 4; $i++ ) : ?>
                    <tr>
                        <th>Stat <?php echo $i; ?></th>
                        <td>
                            <input type="text" name="hero_stat<?php echo $i; ?>_num" value="<?php echo g("hero_stat{$i}_num",$d); ?>" style="width:100px" placeholder="Value"> &nbsp;
                            <input type="text" name="hero_stat<?php echo $i; ?>_label" value="<?php echo g("hero_stat{$i}_label",$d); ?>" style="width:200px" placeholder="Label">
                        </td>
                    </tr>
                    <?php endfor; ?>
                </table>
            </div>

            <!-- ──── À PROPOS ──── -->
            <div style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:1.5rem;margin-bottom:1.5rem;">
                <h2 style="margin-bottom:1rem;border-bottom:2px solid #C94A2D;padding-bottom:.5rem;">About Section</h2>
                <table class="form-table">
                    <tr><th>Section Title</th>
                        <td><input type="text" name="about_titre" value="<?php echo g('about_titre',$d); ?>" class="large-text"></td></tr>
                    <tr><th>Section Subtitle</th>
                        <td><input type="text" name="about_description" value="<?php echo g('about_description',$d); ?>" class="large-text"></td></tr>
                    <tr><th>Biographical Text</th>
                        <td>
                            <?php wp_editor( get_option( 'dlm_about_texte', '' ), 'about_texte', [
                                'textarea_name' => 'about_texte',
                                'media_buttons' => false,
                                'textarea_rows' => 10,
                                'teeny'         => true,
                                'quicktags'     => true,
                            ] ); ?>
                        </td></tr>
                </table>
                <h3 style="margin:1rem 0 .5rem;">Information Panel (sidebar)</h3>
                <table class="form-table">
                    <tr><th>Institution</th><td><input type="text" name="info_institution" value="<?php echo g('info_institution',$d); ?>" class="large-text"></td></tr>
                    <tr><th>Laboratory(ies)</th>
                        <td>
                            <div id="dlm-labos-wrap">
                            <?php
                            $labos = get_option( 'dlm_info_labos', [] );
                            if ( empty( $labos ) ) {
                                $legacy = get_option( 'dlm_info_labo', $d['info_labo'] ?? '' );
                                $labos = $legacy ? [ $legacy ] : [];
                            }
                            foreach ( (array) $labos as $lb ) : ?>
                            <div class="dlm-repeater-row" style="display:flex;gap:.5rem;margin-bottom:.5rem;align-items:center;">
                                <input type="text" name="info_labos[]" value="<?php echo esc_attr( $lb ); ?>" class="large-text">
                                <button type="button" class="button dlm-remove-row" title="Remove">&times;</button>
                            </div>
                            <?php endforeach; ?>
                            </div>
                            <button type="button" class="button dlm-add-row" data-target="dlm-labos-wrap" data-name="info_labos[]" data-class="large-text">+ Add a laboratory</button>
                        </td>
                    </tr>
                    <tr><th>Supervisor(s)</th>
                        <td>
                            <div id="dlm-directeurs-wrap">
                            <?php
                            $directeurs = get_option( 'dlm_info_directeurs', [] );
                            if ( empty( $directeurs ) ) {
                                $legacy = get_option( 'dlm_info_directeur', $d['info_directeur'] ?? '' );
                                $directeurs = $legacy ? [ $legacy ] : [];
                            }
                            foreach ( (array) $directeurs as $dir ) : ?>
                            <div class="dlm-repeater-row" style="display:flex;gap:.5rem;margin-bottom:.5rem;align-items:center;">
                                <input type="text" name="info_directeurs[]" value="<?php echo esc_attr( $dir ); ?>" class="large-text">
                                <button type="button" class="button dlm-remove-row" title="Remove">&times;</button>
                            </div>
                            <?php endforeach; ?>
                            </div>
                            <button type="button" class="button dlm-add-row" data-target="dlm-directeurs-wrap" data-name="info_directeurs[]" data-class="large-text">+ Add a supervisor</button>
                        </td>
                    </tr>
                    <tr><th>Location</th><td><input type="text" name="info_localisation" value="<?php echo g('info_localisation',$d); ?>" class="regular-text"></td></tr>
                    <tr><th>Languages</th><td><input type="text" name="info_langues" value="<?php echo g('info_langues',$d); ?>" class="regular-text" placeholder="French · English"></td></tr>
                </table>
            </div>

            <!-- ──── INTÉRÊTS DE RECHERCHE ──── -->
            <div style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:1.5rem;margin-bottom:1.5rem;">
                <h2 style="margin-bottom:1rem;border-bottom:2px solid #C94A2D;padding-bottom:.5rem;">Research Interests</h2>
                <p class="description" style="margin-bottom:1rem;">Each interest appears as a collapsible panel on the site. You can add an SVG icon, a description and a link.</p>
                <div id="dlm-interets-wrap">
                    <?php
                    $interets = get_option( 'dlm_interets', [] );
                    foreach ( (array) $interets as $idx_int => $int ) :
                        $iid = $int['icon_id'] ?? 0;
                        $iurl = $iid ? wp_get_attachment_url( $iid ) : '';
                    ?>
                    <div class="dlm-interet-row" style="margin-bottom:1rem;padding:1.25rem;background:#f9f9f9;border-radius:8px;border:1px solid #e0e0e0;position:relative;">
                        <button type="button" class="button dlm-remove-interet" title="Remove" style="position:absolute;top:10px;right:10px;color:#a00;border-color:#ddd;min-width:auto;padding:0 8px;line-height:28px;">&times;</button>
                        <div style="display:grid;grid-template-columns:auto 1fr;gap:1rem;align-items:start;">
                            <div style="text-align:center;">
                                <input type="hidden" name="interet_icon_id[]" value="<?php echo esc_attr( $iid ); ?>" class="dlm-interet-icon-id">
                                <div class="dlm-interet-icon-preview" style="width:48px;height:48px;border:2px dashed #ccc;border-radius:8px;display:flex;align-items:center;justify-content:center;background:#fff;margin-bottom:6px;<?php echo $iurl ? 'border-style:solid;border-color:#C94A2D;' : ''; ?>"><?php if ( $iurl ) : ?><img src="<?php echo esc_url( $iurl ); ?>" style="width:36px;height:36px;" alt=""><?php else : ?><span style="color:#bbb;font-size:20px;">+</span><?php endif; ?></div>
                                <button type="button" class="button button-small dlm-interet-icon-upload" style="font-size:11px;">Icon</button>
                                <button type="button" class="button button-small dlm-interet-icon-remove" style="font-size:11px;color:#a00;<?php echo $iid ? '' : 'display:none;'; ?>">Remove</button>
                            </div>
                            <div>
                                <div style="margin-bottom:.6rem;">
                                    <label style="font-weight:600;font-size:12px;text-transform:uppercase;letter-spacing:.04em;color:#555;display:block;margin-bottom:3px;">Title</label>
                                    <input type="text" name="interet_titre[]" value="<?php echo esc_attr( $int['titre'] ?? '' ); ?>" class="large-text" placeholder="E.g.: Multi-sensor acquisition">
                                </div>
                                <div style="margin-bottom:.6rem;">
                                    <label style="font-weight:600;font-size:12px;text-transform:uppercase;letter-spacing:.04em;color:#555;display:block;margin-bottom:3px;">Description</label>

                                    <?php
                                    $editor_id = 'interet_desc_' . $idx_int;
                                    wp_editor( $int['desc'] ?? '', $editor_id, [
                                        'textarea_name' => 'interet_desc[]',
                                        'media_buttons' => false,
                                        'textarea_rows' => 4,
                                        'teeny'         => true,
                                        'quicktags'     => true,
                                        'editor_css'    => '<style>.wp-editor-container{border-radius:4px;}</style>',
                                    ] );
                                    ?>
                                </div>
                                <div>
                                    <label style="font-weight:600;font-size:12px;text-transform:uppercase;letter-spacing:.04em;color:#555;display:block;margin-bottom:3px;">Link (optional)</label>
                                    <input type="url" name="interet_url[]" value="<?php echo esc_attr( $int['url'] ?? '' ); ?>" class="large-text" placeholder="https://lien-vers-details.com">
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="button button-primary" id="dlm-add-interet" style="margin-top:.5rem;">+ Add an interest</button>
            </div>

            <!-- ──── PARTENAIRES ──── -->
            <div style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:1.5rem;margin-bottom:1.5rem;">
                <h2 style="margin-bottom:1rem;border-bottom:2px solid #C94A2D;padding-bottom:.5rem;">Partners / Logos Section</h2>
                <table class="form-table">
                    <tr><th>Section Label</th>
                        <td><input type="text" name="partners_label" value="<?php echo g('partners_label',$d); ?>" class="regular-text" placeholder="Collaborations"></td></tr>
                    <tr><th>Section Title</th>
                        <td><input type="text" name="partners_titre" value="<?php echo g('partners_titre',$d); ?>" class="large-text"></td></tr>
                    <tr><th>Section Description</th>
                        <td><input type="text" name="partners_description" value="<?php echo g('partners_description',$d); ?>" class="large-text" placeholder="Research partners and institutional collaborators."></td></tr>
                </table>
                <div style="background:#f0f6ff;padding:1rem;border-radius:6px;margin-top:1rem;">
                    <strong>To add logos:</strong> go to <strong>Partners</strong> in the sidebar menu.
                    For each partner, upload the logo via "Featured Image".
                    Recommendation: transparent PNG, width ~300px.
                </div>
            </div>

            <!-- ──── FINANCEMENTS ──── -->
            <div style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:1.5rem;margin-bottom:1.5rem;">
                <h2 style="margin-bottom:1rem;border-bottom:2px solid #C94A2D;padding-bottom:.5rem;">Funding Section</h2>
                <table class="form-table">
                    <tr><th>Section Label</th>
                        <td><input type="text" name="financements_label" value="<?php echo g('financements_label',$d); ?>" class="regular-text" placeholder="Funding"></td></tr>
                    <tr><th>Section Title</th>
                        <td><input type="text" name="financements_titre" value="<?php echo g('financements_titre',$d); ?>" class="large-text" placeholder="Research Funding & Awards"></td></tr>
                    <tr><th>Section Description</th>
                        <td><input type="text" name="financements_description" value="<?php echo g('financements_description',$d); ?>" class="large-text" placeholder="Scholarships, grants and institutional support."></td></tr>
                </table>
                <div style="background:#f0f6ff;padding:1rem;border-radius:6px;margin-top:1rem;">
                    <strong>To add funding:</strong> go to <strong>Funding</strong> in the sidebar menu.
                    Each funding entry can be linked to an existing partner and attached to one or more projects.
                </div>
            </div>

            <!-- ──── CONTACT ──── -->
            <div style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:1.5rem;margin-bottom:1.5rem;">
                <h2 style="margin-bottom:1rem;border-bottom:2px solid #C94A2D;padding-bottom:.5rem;">Contact & Social Links</h2>
                <table class="form-table">
                    <tr><th>Contact Email</th><td><input type="email" name="contact_email" value="<?php echo g('contact_email',$d); ?>" class="regular-text" placeholder="your@email.com"></td></tr>
                    <tr><th>URL GitHub</th><td><input type="url" name="contact_github" value="<?php echo g('contact_github',$d); ?>" class="regular-text" placeholder="https://github.com/..."></td></tr>
                    <tr><th>URL LinkedIn</th><td><input type="url" name="contact_linkedin" value="<?php echo g('contact_linkedin',$d); ?>" class="regular-text" placeholder="https://linkedin.com/in/..."></td></tr>
                    <tr><th>URL ORCID</th><td><input type="url" name="contact_orcid" value="<?php echo g('contact_orcid',$d); ?>" class="regular-text" placeholder="https://orcid.org/0000-0000-0000-0000"></td></tr>
                    <tr><th>URL Institution</th><td><input type="url" name="contact_institution_url" value="<?php echo g('contact_institution_url',$d); ?>" class="regular-text" placeholder="https://uqac.ca"></td></tr>
                </table>
            </div>

            <!-- ──── SEO ──── -->
            <div style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:1.5rem;margin-bottom:1.5rem;">
                <h2 style="margin-bottom:1rem;border-bottom:2px solid #C94A2D;padding-bottom:.5rem;">SEO & Search Engines</h2>
                <table class="form-table">
                    <tr>
                        <th>Meta description</th>
                        <td>
                            <textarea name="seo_description" rows="3" class="large-text" placeholder="Site description for Google (max 160 chars)"><?php echo g('seo_description',$d); ?></textarea>
                            <p class="description">Appears in Google search results. If empty, the hero description will be used.</p>
                        </td>
                    </tr>
                </table>
                <div style="background:#f0f6ff;padding:1rem;border-radius:6px;margin-top:1rem;">
                    <strong>Automatic SEO:</strong> The theme automatically generates Open Graph tags (LinkedIn, Facebook), Twitter Cards, JSON-LD structured data (Person, ScholarlyArticle, ResearchProject) and the canonical URL.
                </div>
            </div>

            <!-- ──── FOOTER ──── -->
            <div style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:1.5rem;margin-bottom:1.5rem;">
                <h2 style="margin-bottom:1rem;border-bottom:2px solid #C94A2D;padding-bottom:.5rem;">Footer</h2>
                <table class="form-table">
                    <tr><th>Left Text</th><td><input type="text" name="footer_texte"   value="<?php echo g('footer_texte',$d);   ?>" class="large-text"></td></tr>
                    <tr><th>Right Text</th><td><input type="text" name="footer_mention" value="<?php echo g('footer_mention',$d); ?>" class="large-text"></td></tr>
                </table>
            </div>

            <?php submit_button( 'Save All Options', 'primary large', 'deladem_save' ); ?>
        </form>
    </div>

    <script>
    (function(){
        // Badge repeater
        document.getElementById('dlm-add-badge').addEventListener('click', function(){
            var wrap = document.getElementById('dlm-badges-wrap');
            var row = document.createElement('div');
            row.className = 'dlm-repeater-row';
            row.style.cssText = 'display:flex;gap:.5rem;margin-bottom:.5rem;align-items:center;';
            row.innerHTML = '<input type="text" name="hero_badges[]" value="" class="regular-text" placeholder="Badge name">'
                + '<button type="button" class="button dlm-remove-row" title="Remove">&times;</button>';
            wrap.appendChild(row);
        });

        // Generic repeater (labos, directeurs)
        document.querySelectorAll('.dlm-add-row').forEach(function(btn){
            btn.addEventListener('click', function(){
                var wrap = document.getElementById(this.dataset.target);
                var row = document.createElement('div');
                row.className = 'dlm-repeater-row';
                row.style.cssText = 'display:flex;gap:.5rem;margin-bottom:.5rem;align-items:center;';
                row.innerHTML = '<input type="text" name="' + this.dataset.name + '" value="" class="' + this.dataset['class'] + '">'
                    + '<button type="button" class="button dlm-remove-row" title="Remove">&times;</button>';
                wrap.appendChild(row);
            });
        });

        // Intérêt repeater
        var interetCounter = document.querySelectorAll('.dlm-interet-row').length;
        document.getElementById('dlm-add-interet').addEventListener('click', function(){
            var wrap = document.getElementById('dlm-interets-wrap');
            var row = document.createElement('div');
            var editorId = 'interet_desc_' + interetCounter;
            interetCounter++;
            row.className = 'dlm-interet-row';
            row.style.cssText = 'margin-bottom:1rem;padding:1.25rem;background:#f9f9f9;border-radius:8px;border:1px solid #e0e0e0;position:relative;';
            var lbl = 'font-weight:600;font-size:12px;text-transform:uppercase;letter-spacing:.04em;color:#555;display:block;margin-bottom:3px;';
            row.innerHTML = '<button type="button" class="button dlm-remove-interet" title="Remove" style="position:absolute;top:10px;right:10px;color:#a00;border-color:#ddd;min-width:auto;padding:0 8px;line-height:28px;">&times;</button>'
                + '<div style="display:grid;grid-template-columns:auto 1fr;gap:1rem;align-items:start;">'
                + '<div style="text-align:center;">'
                + '<input type="hidden" name="interet_icon_id[]" value="" class="dlm-interet-icon-id">'
                + '<div class="dlm-interet-icon-preview" style="width:48px;height:48px;border:2px dashed #ccc;border-radius:8px;display:flex;align-items:center;justify-content:center;background:#fff;margin-bottom:6px;"><span style="color:#bbb;font-size:20px;">+</span></div>'
                + '<button type="button" class="button button-small dlm-interet-icon-upload" style="font-size:11px;">Icon</button>'
                + '<button type="button" class="button button-small dlm-interet-icon-remove" style="font-size:11px;color:#a00;display:none;">Remove</button>'
                + '</div>'
                + '<div>'
                + '<div style="margin-bottom:.6rem;"><label style="' + lbl + '">Title</label><input type="text" name="interet_titre[]" class="large-text" placeholder="E.g.: Multi-sensor acquisition"></div>'
                + '<div style="margin-bottom:.6rem;"><label style="' + lbl + '">Description</label><textarea id="' + editorId + '" name="interet_desc[]" rows="4" class="large-text"></textarea></div>'
                + '<div><label style="' + lbl + '">Link (optional)</label><input type="url" name="interet_url[]" class="large-text" placeholder="https://link-to-details.com"></div>'
                + '</div>'
                + '</div>';
            wrap.appendChild(row);
            // Initialize TinyMCE on the new textarea
            if (typeof wp !== 'undefined' && wp.editor) {
                wp.editor.initialize(editorId, {
                    tinymce: {
                        wpautop: true,
                        plugins: 'charmap,colorpicker,hr,lists,paste,tabfocus,wordpress,wpautoresize,wpeditimage,wpgallery,wplink,wptextpattern',
                        toolbar1: 'bold,italic,underline,bullist,numlist,link,unlink,undo,redo',
                        toolbar2: '',
                        menubar: false,
                        statusbar: false,
                        resize: false,
                        height: 120
                    },
                    quicktags: true,
                    mediaButtons: false
                });
            }
        });

        // Remove handlers (delegated)
        document.addEventListener('click', function(e){
            if(e.target.classList.contains('dlm-remove-row') || e.target.classList.contains('dlm-remove-interet')){
                var row = e.target.closest('.dlm-repeater-row,.dlm-interet-row');
                // Destroy TinyMCE instances in this row before removing
                if (row && typeof wp !== 'undefined' && wp.editor) {
                    var editors = row.querySelectorAll('textarea[id^="interet_desc_"]');
                    editors.forEach(function(ta) { wp.editor.remove(ta.id); });
                }
                row.remove();
            }
        });
    })();
    </script>
    <?php
}


/* ============================================================
   9. CUSTOMIZER — Option Deladem dans Personnaliser
   ============================================================ */
function deladem_customizer_register( $wp_customize ) {
    // Panel Deladem
    $wp_customize->add_panel( 'deladem_panel', [
        'title'    => 'Deladem IHM',
        'priority' => 30,
    ] );

    // Section Header
    $wp_customize->add_section( 'deladem_header', [
        'title'    => 'Header',
        'panel'    => 'deladem_panel',
        'priority' => 5,
    ] );
    $wp_customize->add_setting( 'dlm_site_tag', [ 'type' => 'option', 'default' => 'HCI', 'sanitize_callback' => 'sanitize_text_field' ] );
    $wp_customize->add_control( 'dlm_site_tag', [ 'label' => 'Site Tag (next to site name)', 'section' => 'deladem_header', 'type' => 'text', 'description' => 'Displayed as "Site Name · Tag". Leave empty to hide.' ] );

    // Section Typography
    $wp_customize->add_section( 'deladem_typography', [
        'title'    => 'Typography',
        'panel'    => 'deladem_panel',
        'priority' => 6,
    ] );
    $wp_customize->add_setting( 'dlm_typography', [
        'type'              => 'option',
        'default'           => 'editorial',
        'sanitize_callback' => function( $val ) {
            $valid = array_keys( deladem_get_typographies() );
            return in_array( $val, $valid, true ) ? $val : 'editorial';
        },
    ] );
    $typo_choices = [];
    foreach ( deladem_get_typographies() as $slug => $t ) {
        $typo_choices[ $slug ] = $t['label'];
    }
    $wp_customize->add_control( 'dlm_typography', [
        'label'       => 'Font Pairing',
        'section'     => 'deladem_typography',
        'type'        => 'select',
        'choices'     => $typo_choices,
        'description' => 'Choose a font combination for headings, body text and code.',
    ] );

    // Section Colors
    $wp_customize->add_section( 'deladem_colors', [
        'title'    => 'Colors',
        'panel'    => 'deladem_panel',
        'priority' => 6,
    ] );

    $color_controls = [
        'color_accent'  => [ 'Accent color',     '#C94A2D', 'Main accent: links, icons, buttons' ],
        'color_accent2' => [ 'Secondary accent',  '#3B5BDB', 'Badges, gradients, highlights' ],
        'color_bg'      => [ 'Background',        '#F5F2EE', 'Page background' ],
        'color_ink'     => [ 'Text',              '#1A1714', 'Main text color' ],
        'color_card'    => [ 'Card background',   '#FDFAF7', 'Cards and panels' ],
        'color_border'  => [ 'Borders',           '#E2DDD8', 'Borders and dividers' ],
        'color_muted'   => [ 'Muted text',        '#7A7067', 'Secondary text' ],
    ];
    foreach ( $color_controls as $key => $cfg ) {
        $wp_customize->add_setting( 'dlm_' . $key, [
            'type'              => 'option',
            'default'           => $cfg[1],
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dlm_' . $key, [
            'label'       => $cfg[0],
            'section'     => 'deladem_colors',
            'description' => $cfg[2],
        ] ) );
    }

    // Section Layouts (per section)
    $wp_customize->add_section( 'deladem_layouts', [
        'title'       => 'Section Layouts',
        'panel'       => 'deladem_panel',
        'priority'    => 7,
        'description' => 'Choose the layout for each section independently.',
    ] );

    $layout_options = [
        'layout_projects' => [
            'label'   => 'Projects',
            'default' => 'grid',
            'choices' => [ 'grid' => 'Grid (auto-fill)', 'two-col' => 'Two columns', 'list' => 'List (single column)' ],
        ],
        'layout_publications' => [
            'label'   => 'Publications',
            'default' => 'list',
            'choices' => [ 'list' => 'List (default)', 'card-grid' => 'Card grid', 'compact' => 'Compact (no type badge)' ],
        ],
        'layout_cv' => [
            'label'   => 'CV / Background',
            'default' => 'two-col',
            'choices' => [ 'two-col' => 'Two columns (default)', 'single' => 'Single column (timeline)' ],
        ],
        'layout_contact' => [
            'label'   => 'Contact',
            'default' => 'two-col',
            'choices' => [ 'two-col' => 'Two columns (default)', 'stacked' => 'Stacked (single column)' ],
        ],
    ];
    foreach ( $layout_options as $key => $cfg ) {
        $wp_customize->add_setting( 'dlm_' . $key, [
            'type'              => 'option',
            'default'           => $cfg['default'],
            'sanitize_callback' => function( $val ) use ( $cfg ) {
                return array_key_exists( $val, $cfg['choices'] ) ? $val : $cfg['default'];
            },
        ] );
        $wp_customize->add_control( 'dlm_' . $key, [
            'label'   => $cfg['label'],
            'section' => 'deladem_layouts',
            'type'    => 'select',
            'choices' => $cfg['choices'],
        ] );
    }

    // Section Hero
    $wp_customize->add_section( 'deladem_hero', [
        'title' => 'Hero (main banner)',
        'panel' => 'deladem_panel',
    ] );

    $hero_fields = [
        'hero_etiquette'    => 'Label',
        'hero_titre_ligne1' => 'Title - Line 1',
        'hero_titre_em'     => 'Title - Accent text',
    ];
    foreach ( $hero_fields as $key => $label ) {
        $wp_customize->add_setting( 'dlm_' . $key, [ 'type' => 'option', 'sanitize_callback' => 'sanitize_text_field' ] );
        $wp_customize->add_control( 'dlm_' . $key, [ 'label' => $label, 'section' => 'deladem_hero', 'type' => 'text' ] );
    }
    $wp_customize->add_setting( 'dlm_hero_description', [ 'type' => 'option', 'sanitize_callback' => 'wp_kses_post' ] );
    $wp_customize->add_control( 'dlm_hero_description', [ 'label' => 'Description', 'section' => 'deladem_hero', 'type' => 'textarea' ] );

    // Section À propos
    $wp_customize->add_section( 'deladem_about', [
        'title' => 'About',
        'panel' => 'deladem_panel',
    ] );
    $wp_customize->add_setting( 'dlm_about_titre', [ 'type' => 'option', 'sanitize_callback' => 'sanitize_text_field' ] );
    $wp_customize->add_control( 'dlm_about_titre', [ 'label' => 'Title', 'section' => 'deladem_about', 'type' => 'text' ] );
    $wp_customize->add_setting( 'dlm_about_description', [ 'type' => 'option', 'sanitize_callback' => 'sanitize_text_field' ] );
    $wp_customize->add_control( 'dlm_about_description', [ 'label' => 'Subtitle', 'section' => 'deladem_about', 'type' => 'text' ] );
    $wp_customize->add_setting( 'dlm_about_texte', [ 'type' => 'option', 'sanitize_callback' => 'wp_kses_post' ] );
    $wp_customize->add_control( 'dlm_about_texte', [ 'label' => 'Biographical text', 'section' => 'deladem_about', 'type' => 'textarea' ] );

    // Section Contact
    $wp_customize->add_section( 'deladem_contact', [
        'title' => 'Contact & Social',
        'panel' => 'deladem_panel',
    ] );
    $contact_fields = [
        'contact_email'    => 'Email',
        'contact_github'   => 'GitHub URL',
        'contact_linkedin' => 'LinkedIn URL',
        'contact_orcid'    => 'ORCID URL',
    ];
    foreach ( $contact_fields as $key => $label ) {
        $wp_customize->add_setting( 'dlm_' . $key, [ 'type' => 'option', 'sanitize_callback' => 'sanitize_text_field' ] );
        $wp_customize->add_control( 'dlm_' . $key, [ 'label' => $label, 'section' => 'deladem_contact', 'type' => 'text' ] );
    }

    // Section Financements
    $wp_customize->add_section( 'deladem_financements', [
        'title' => 'Funding',
        'panel' => 'deladem_panel',
    ] );
    $fin_fields = [
        'financements_label'       => 'Section Label',
        'financements_titre'       => 'Section Title',
        'financements_description' => 'Description',
    ];
    foreach ( $fin_fields as $key => $label ) {
        $wp_customize->add_setting( 'dlm_' . $key, [ 'type' => 'option', 'sanitize_callback' => 'sanitize_text_field' ] );
        $wp_customize->add_control( 'dlm_' . $key, [ 'label' => $label, 'section' => 'deladem_financements', 'type' => 'text' ] );
    }

    // Section Footer
    $wp_customize->add_section( 'deladem_footer', [
        'title' => 'Footer',
        'panel' => 'deladem_panel',
    ] );
    $wp_customize->add_setting( 'dlm_footer_texte', [ 'type' => 'option', 'sanitize_callback' => 'wp_kses_post' ] );
    $wp_customize->add_control( 'dlm_footer_texte', [ 'label' => 'Left text', 'section' => 'deladem_footer', 'type' => 'text' ] );
    $wp_customize->add_setting( 'dlm_footer_mention', [ 'type' => 'option', 'sanitize_callback' => 'wp_kses_post' ] );
    $wp_customize->add_control( 'dlm_footer_mention', [ 'label' => 'Right text', 'section' => 'deladem_footer', 'type' => 'text' ] );

    // Lien vers la page d'options complète
    $wp_customize->add_section( 'deladem_options_link', [
        'title'       => 'Advanced Options',
        'panel'       => 'deladem_panel',
        'description' => sprintf(
            '<a href="%s" class="button" style="margin-top:.5rem">Open full options</a><br><br>Badges, interests, supervisors and laboratories are managed on the advanced options page.',
            esc_url( admin_url( 'themes.php?page=deladem-options' ) )
        ),
    ] );
    // Dummy setting to make the section visible
    $wp_customize->add_setting( 'dlm_customizer_link', [ 'type' => 'option', 'sanitize_callback' => 'sanitize_text_field' ] );
    $wp_customize->add_control( 'dlm_customizer_link', [ 'label' => '', 'section' => 'deladem_options_link', 'type' => 'hidden' ] );
}
add_action( 'customize_register', 'deladem_customizer_register' );


/* ============================================================
   10. HELPERS — Récupération des données
   ============================================================ */
function dlm_opt( $key, $default = '' ) {
    return get_option( 'dlm_' . $key, $default );
}

function deladem_default_projet_icon( $size = 24 ) {
    return '<svg width="' . (int) $size . '" height="' . (int) $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>';
}

function deladem_render_svg_icon( $attachment_id, $size = 24 ) {
    $attachment_id = absint( $attachment_id );
    if ( ! $attachment_id ) return '';
    $file = get_attached_file( $attachment_id );
    if ( ! $file || ! file_exists( $file ) ) return '';
    if ( get_post_mime_type( $attachment_id ) !== 'image/svg+xml' ) return '';
    $svg = @file_get_contents( $file );
    if ( ! $svg ) return '';
    // Sanitize: strip <script>, event handlers, and external references
    $svg = preg_replace( '/<script\b[^>]*>.*?<\/script>/is', '', $svg );
    $svg = preg_replace( '/\s+on\w+\s*=\s*["\'][^"\']*["\']/i', '', $svg );
    $svg = preg_replace( '/\s+xlink:href\s*=\s*["\'](?!#)[^"\']*["\']/i', '', $svg );
    // Resize
    $svg = preg_replace( '/(<svg[^>]*)\s*(width|height)="[^"]*"/i', '$1', $svg );
    $svg = preg_replace( '/<svg\b/i', '<svg width="' . (int) $size . '" height="' . (int) $size . '"', $svg, 1 );
    return $svg;
}

function deladem_get_projets( $limit = 6 ) {
    return new WP_Query([
        'post_type' => 'projet', 'posts_per_page' => $limit,
        'post_status' => 'publish', 'meta_key' => '_projet_ordre',
        'orderby' => 'meta_value_num', 'order' => 'ASC',
    ]);
}

function deladem_get_publications( $limit = 10 ) {
    return new WP_Query([
        'post_type' => 'publication', 'posts_per_page' => $limit,
        'post_status' => 'publish', 'meta_key' => '_pub_annee',
        'orderby' => 'meta_value_num', 'order' => 'DESC',
    ]);
}

function deladem_get_partenaires( $limit = 20 ) {
    return new WP_Query([
        'post_type' => 'partenaire', 'posts_per_page' => $limit,
        'post_status' => 'publish', 'meta_key' => '_partenaire_ordre',
        'orderby' => 'meta_value_num', 'order' => 'ASC',
    ]);
}

function deladem_get_financements( $limit = 20 ) {
    return new WP_Query([
        'post_type' => 'financement', 'posts_per_page' => $limit,
        'post_status' => 'publish', 'meta_key' => '_financement_ordre',
        'orderby' => 'meta_value_num', 'order' => 'ASC',
    ]);
}

function deladem_get_cv( $categorie ) {
    return new WP_Query([
        'post_type' => 'cv_entree', 'posts_per_page' => 20,
        'post_status' => 'publish',
        'meta_query' => [[ 'key' => '_cv_categorie', 'value' => $categorie ]],
        'meta_key' => '_cv_ordre', 'orderby' => 'meta_value_num', 'order' => 'ASC',
    ]);
}

function deladem_render_projet_tags( $post_id ) {
    $tags = get_post_meta( $post_id, '_projet_tags', true );
    if ( !$tags ) return;
    echo '<div class="research-tags">';
    foreach ( array_map('trim', explode(',', $tags)) as $t ) {
        if ( $t ) echo '<span class="tag">' . esc_html($t) . '</span>';
    }
    echo '</div>';
}

/**
 * Customize archive queries for CPTs.
 */
add_action( 'pre_get_posts', function( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) return;

    if ( $query->is_post_type_archive( 'projet' ) ) {
        $query->set( 'posts_per_page', 12 );
    }

    if ( $query->is_post_type_archive( 'publication' ) ) {
        $query->set( 'posts_per_page', 12 );
        $query->set( 'meta_key', '_pub_annee' );
        $query->set( 'orderby', 'meta_value_num' );
        $query->set( 'order', 'DESC' );
    }

    if ( $query->is_post_type_archive( 'financement' ) ) {
        $query->set( 'posts_per_page', 12 );
        $query->set( 'meta_key', '_financement_ordre' );
        $query->set( 'orderby', 'meta_value_num' );
        $query->set( 'order', 'ASC' );
    }
});

/**
 * Check which front-page sections have content (used by menus).
 * Returns an array of section IDs that should be hidden.
 */
function deladem_get_empty_sections() {
    static $empty = null;
    if ( $empty !== null ) return $empty;
    $empty = [];

    // #interets — hidden if no research interests
    $interets = get_option( 'dlm_interets', [] );
    if ( empty( $interets ) ) $empty[] = 'interets';

    // #partners — hidden if no partner posts
    $p_count = (int) wp_count_posts( 'partenaire' )->publish;
    if ( $p_count === 0 ) $empty[] = 'partners';

    // #research — hidden if no projet posts
    $r_count = (int) wp_count_posts( 'projet' )->publish;
    if ( $r_count === 0 ) $empty[] = 'research';

    // #publications — hidden if no publication posts
    $pub_count = (int) wp_count_posts( 'publication' )->publish;
    if ( $pub_count === 0 ) $empty[] = 'publications';

    // #financements — hidden if no active/finished financement posts
    $fin_q = new WP_Query([
        'post_type' => 'financement', 'posts_per_page' => 1, 'post_status' => 'publish',
        'meta_query' => [[ 'key' => '_financement_statut', 'value' => [ 'actif', 'termine', '' ], 'compare' => 'IN' ]],
        'fields' => 'ids', 'no_found_rows' => true,
    ]);
    if ( ! $fin_q->have_posts() ) $empty[] = 'financements';

    // #cv — hidden if no cv_entree posts
    $cv_count = (int) wp_count_posts( 'cv_entree' )->publish;
    if ( $cv_count === 0 ) $empty[] = 'cv';

    return $empty;
}

/* Fallback menu — only show sections that have content, with proper URLs */
function deladem_fallback_menu() {
    $all_items = [
        '#about'        => 'About',
        '#interets'     => 'Interests',
        '#partners'     => 'Partners',
        '#research'     => 'Research',
        '#publications' => 'Publications',
        '#financements' => 'Funding',
        '#cv'           => 'Background',
        '#contact'      => 'Contact',
    ];
    $empty  = deladem_get_empty_sections();
    $prefix = is_front_page() ? '' : esc_url( home_url( '/' ) );
    echo '<ul id="primary-menu">';
    foreach ( $all_items as $anchor => $label ) {
        $section_id = ltrim( $anchor, '#' );
        if ( in_array( $section_id, $empty, true ) ) continue;
        echo '<li><a href="' . esc_attr( $prefix . $anchor ) . '">' . esc_html( $label ) . '</a></li>';
    }
    echo '</ul>';
}

/* Filter registered WP nav menu — hide empty sections + fix anchor URLs on inner pages */
add_filter( 'wp_nav_menu_objects', function( $items ) {
    $empty       = deladem_get_empty_sections();
    $is_front    = is_front_page();
    $home_url    = trailingslashit( home_url() );

    foreach ( $items as $key => $item ) {
        $url = $item->url;
        // Match anchor links: #section, /#section, or home_url()/#section
        if ( preg_match( '/#([a-z_-]+)$/i', $url, $m ) ) {
            $section_id = $m[1];
            // Remove items linking to empty sections
            if ( in_array( $section_id, $empty, true ) ) {
                unset( $items[ $key ] );
                continue;
            }
            // On inner pages, ensure anchor links point to home_url/#section
            if ( ! $is_front && $url === '#' . $section_id ) {
                $item->url = $home_url . '#' . $section_id;
            }
        }
    }
    return $items;
} );

/* Traitement formulaire contact natif */
add_action( 'admin_post_deladem_contact', 'deladem_handle_contact' );
add_action( 'admin_post_nopriv_deladem_contact', 'deladem_handle_contact' );
function deladem_handle_contact() {
    if ( ! wp_verify_nonce( $_POST['deladem_contact_nonce'] ?? '', 'deladem_contact' ) ) {
        wp_die( 'Security error' );
    }
    $to      = dlm_opt( 'contact_email' ) ?: get_option( 'admin_email' );
    $subject = '[Contact site] ' . sanitize_text_field( $_POST['contact_subject'] ?? '' );
    $message = sanitize_textarea_field( $_POST['contact_message'] ?? '' );
    $name    = preg_replace( '/[\r\n]/', '', sanitize_text_field( $_POST['contact_name'] ?? '' ) );
    $email   = sanitize_email( $_POST['contact_email'] ?? '' );
    if ( ! is_email( $email ) ) {
        wp_safe_redirect( home_url( '/#contact' ) );
        exit;
    }
    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        'Reply-To: ' . $name . ' <' . $email . '>',
    ];
    wp_mail( $to, $subject, $message . "\n\nDe : {$name} <{$email}>", $headers );
    wp_safe_redirect( home_url( '/#contact' ) );
    exit;
}
