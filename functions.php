<?php
/**
 * Deladem IHM v2 — functions.php
 * Tout le contenu est gérable depuis l'admin WordPress.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'DELADEM_VERSION', '2.1.0' );

require_once get_template_directory() . '/inc/seeder.php';

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
    flush_rewrite_rules();
    deladem_maybe_seed();
} );


/* ============================================================
   2. ASSETS
   ============================================================ */
function deladem_enqueue_assets() {
    wp_enqueue_style( 'deladem-fonts',
        'https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap',
        [], null
    );
    wp_enqueue_style( 'deladem-main',
        get_template_directory_uri() . '/assets/css/main.css',
        [ 'deladem-fonts' ], filemtime( get_template_directory() . '/assets/css/main.css' )
    );
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


/* ============================================================
   3. CUSTOM POST TYPES
   ============================================================ */

// — Projets —
function deladem_cpt_projets() {
    register_post_type( 'projet', [
        'labels'       => [ 'name' => 'Projets', 'singular_name' => 'Projet', 'add_new_item' => 'Ajouter un projet', 'edit_item' => 'Modifier le projet', 'all_items' => 'Tous les projets', 'menu_name' => 'Projets' ],
        'public'       => true,
        'has_archive'  => false,
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
        'labels'       => [ 'name' => 'Publications', 'singular_name' => 'Publication', 'add_new_item' => 'Ajouter une publication', 'edit_item' => 'Modifier la publication', 'all_items' => 'Toutes les publications', 'menu_name' => 'Publications' ],
        'public'       => true,
        'has_archive'  => false,
        'supports'     => [ 'title', 'custom-fields' ],
        'menu_icon'    => 'dashicons-welcome-write-blog',
        'show_in_rest' => true,
    ] );
}
add_action( 'init', 'deladem_cpt_publications' );

// — Partenaires / Logos —
function deladem_cpt_partners() {
    register_post_type( 'partenaire', [
        'labels'       => [ 'name' => 'Partenaires', 'singular_name' => 'Partenaire', 'add_new_item' => 'Ajouter un partenaire', 'edit_item' => 'Modifier le partenaire', 'all_items' => 'Tous les partenaires', 'menu_name' => 'Partenaires' ],
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
        'labels'       => [ 'name' => 'CV / Parcours', 'singular_name' => 'Entrée CV', 'add_new_item' => 'Ajouter une entrée', 'edit_item' => 'Modifier l\'entrée', 'all_items' => 'Tout le parcours', 'menu_name' => 'CV / Parcours' ],
        'public'       => false,
        'show_ui'      => true,
        'show_in_menu' => true,
        'supports'     => [ 'title' ],
        'menu_icon'    => 'dashicons-id-alt',
        'show_in_rest' => true,
    ] );
}
add_action( 'init', 'deladem_cpt_cv' );


/* ============================================================
   4. META BOXES — Projets
   ============================================================ */
add_action( 'add_meta_boxes', function() {
    add_meta_box( 'projet_meta', 'Détails du projet', 'deladem_projet_meta_cb', 'projet', 'normal', 'high' );
} );

function deladem_projet_meta_cb( $post ) {
    wp_nonce_field( 'deladem_projet', 'deladem_projet_nonce' );
    $tags    = get_post_meta( $post->ID, '_projet_tags',    true );
    $ordre   = get_post_meta( $post->ID, '_projet_ordre',   true );
    $icon_id = get_post_meta( $post->ID, '_projet_icon_id', true );
    ?>
    <table class="form-table">
        <tr>
            <th><label>Ic&ocirc;ne SVG</label></th>
            <td>
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
                    <button type="button" class="button dlm-projet-icon-upload">Choisir une ic&ocirc;ne</button>
                    <button type="button" class="button dlm-icon-remove" style="<?php echo $icon_id ? '' : 'display:none;'; ?>">Supprimer</button>
                    <p class="description">Uploadez un fichier SVG. Laissez vide pour l'ic&ocirc;ne par d&eacute;faut.</p>
                </div>
            </td>
        </tr>
        <tr>
            <th><label>Tags technologiques</label></th>
            <td>
                <input type="text" name="projet_tags" value="<?php echo esc_attr($tags); ?>" class="large-text"
                    placeholder="Python, LSL, EEG, Unity (séparés par virgule)">
                <p class="description">Séparés par des virgules</p>
            </td>
        </tr>
        <tr>
            <th><label>Ordre d'affichage</label></th>
            <td>
                <input type="number" name="projet_ordre" value="<?php echo esc_attr($ordre ?: 10); ?>" style="width:80px" min="1" max="99">
                <p class="description">1 = affiché en premier</p>
            </td>
        </tr>
    </table>
    <?php
}

add_action( 'save_post_projet', function( $post_id ) {
    if ( !isset($_POST['deladem_projet_nonce']) || !wp_verify_nonce($_POST['deladem_projet_nonce'], 'deladem_projet') ) return;
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
    if ( !current_user_can('edit_post', $post_id) ) return;
    update_post_meta( $post_id, '_projet_tags',    sanitize_text_field( wp_unslash( $_POST['projet_tags']  ?? '' ) ) );
    update_post_meta( $post_id, '_projet_ordre',   absint($_POST['projet_ordre'] ?? 10) );
    update_post_meta( $post_id, '_projet_icon_id', absint($_POST['projet_icon_id'] ?? 0) );
} );


/* ============================================================
   5. META BOXES — Publications
   ============================================================ */
add_action( 'add_meta_boxes', function() {
    add_meta_box( 'pub_meta', 'Détails de la publication', 'deladem_pub_meta_cb', 'publication', 'normal', 'high' );
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
            <th><label>Année</label></th>
            <td><input type="number" name="pub_annee" value="<?php echo esc_attr($annee ?: date('Y')); ?>" style="width:100px" min="1990" max="2099"></td>
        </tr>
        <tr>
            <th><label>Auteurs</label></th>
            <td><input type="text" name="pub_auteurs" value="<?php echo esc_attr($auteurs); ?>" class="large-text" placeholder="Deladem, B.-A. J. Ménélas"></td>
        </tr>
        <tr>
            <th><label>Revue / Conférence</label></th>
            <td><input type="text" name="pub_revue" value="<?php echo esc_attr($revue); ?>" class="large-text" placeholder="ACM CHI 2024, Proceedings of..."></td>
        </tr>
        <tr>
            <th><label>Type</label></th>
            <td>
                <select name="pub_type">
                    <?php
                    $types = [ 'conference' => 'Conférence', 'journal' => 'Revue', 'poster' => 'Poster', 'rapport' => 'Rapport', 'these' => 'Thèse', 'workshop' => 'Workshop' ];
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
                <p class="description">Lien vers la publication (optionnel)</p>
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
    add_meta_box( 'partenaire_meta', 'Informations du partenaire', 'deladem_partenaire_meta_cb', 'partenaire', 'normal', 'high' );
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
            <th><label>Type de partenariat</label></th>
            <td>
                <select name="partenaire_type">
                    <?php
                    $types = [ 'institution' => 'Institution / Université', 'entreprise' => 'Entreprise', 'labo' => 'Laboratoire', 'consulting' => 'Consulting / Freelance', 'autre' => 'Autre' ];
                    foreach ( $types as $val => $label ) {
                        printf( '<option value="%s" %s>%s</option>', $val, selected($type, $val, false), $label );
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label>Initiales (si pas de logo)</label></th>
            <td>
                <input type="text" name="partenaire_initiales" value="<?php echo esc_attr($initiales); ?>" style="width:80px;text-align:center" maxlength="4" placeholder="UQAC">
                <p class="description">Affichées si aucune image n'est définie comme vignette</p>
            </td>
        </tr>
        <tr>
            <th><label>URL du site</label></th>
            <td><input type="url" name="partenaire_url" value="<?php echo esc_attr($url); ?>" class="large-text" placeholder="https://..."></td>
        </tr>
        <tr>
            <th><label>Ordre d'affichage</label></th>
            <td><input type="number" name="partenaire_ordre" value="<?php echo esc_attr($ordre ?: 10); ?>" style="width:80px" min="1" max="99"></td>
        </tr>
    </table>
    <p class="description" style="margin-top:1rem;padding:1rem;background:#f8f8f8;border-left:3px solid #2271b1;">
        <strong>Comment ajouter le logo :</strong> Utilisez la <strong>Image mise en avant</strong> (encadré à droite) pour uploader le logo de l'entreprise. 
        Formats recommandés : PNG transparent ou SVG. Taille recommandée : 300×100px.
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
    add_meta_box( 'cv_meta', 'Détails de l\'entrée', 'deladem_cv_meta_cb', 'cv_entree', 'normal', 'high' );
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
            <th><label>Catégorie</label></th>
            <td>
                <select name="cv_categorie">
                    <?php
                    $cats = [ 'formation' => 'Formation', 'experience' => 'Expérience professionnelle', 'competence' => 'Compétences' ];
                    foreach ( $cats as $val => $label ) {
                        printf( '<option value="%s" %s>%s</option>', $val, selected($categorie, $val, false), $label );
                    }
                    ?>
                </select>
                <p class="description">Détermine dans quelle colonne l'entrée apparaît</p>
            </td>
        </tr>
        <tr>
            <th><label>Période / Dates</label></th>
            <td>
                <input type="text" name="cv_periode" value="<?php echo esc_attr($periode); ?>" class="regular-text" placeholder="2022 — En cours">
                <p class="description">Ex: 2022 — En cours, 2019 — 2022, Antérieur</p>
            </td>
        </tr>
        <tr>
            <th><label>Établissement / Lieu</label></th>
            <td>
                <input type="text" name="cv_etablissement" value="<?php echo esc_attr($etablissement); ?>" class="large-text" placeholder="UQAC, Chicoutimi QC">
                <p class="description">Le contenu de l'éditeur principal sera utilisé comme sous-titre/description</p>
            </td>
        </tr>
        <tr>
            <th><label>Ordre d'affichage</label></th>
            <td>
                <input type="number" name="cv_ordre" value="<?php echo esc_attr($ordre ?: 10); ?>" style="width:80px" min="1">
                <p class="description">1 = affiché en premier dans sa catégorie</p>
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
   8. PAGE D'OPTIONS — Informations du site
   ============================================================ */
add_action( 'admin_menu', function() {
    add_theme_page(
        'Options du thème',
        'Options Deladem',
        'manage_options',
        'deladem-options',
        'deladem_options_page'
    );
} );

function deladem_options_page() {
    if ( isset($_POST['deladem_save']) && check_admin_referer('deladem_options_save') ) {

        $text_fields = [
            'hero_etiquette', 'hero_titre_ligne1', 'hero_titre_em', 'hero_titre_ligne3',
            'hero_btn1_label', 'hero_btn1_url', 'hero_btn2_label', 'hero_btn2_url',
            'hero_stat1_num', 'hero_stat1_label', 'hero_stat2_num', 'hero_stat2_label',
            'hero_stat3_num', 'hero_stat3_label', 'hero_stat4_num', 'hero_stat4_label',
            'about_titre', 'about_sous_titre',
            'info_institution', 'info_labo', 'info_directeur', 'info_localisation', 'info_langues',
            'contact_email', 'contact_github', 'contact_linkedin', 'contact_institution_url',
            'partners_titre', 'partners_label',
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
        'about_texte'       => '',
        'info_institution'  => 'Université du Québec à Chicoutimi (UQAC)',
        'info_labo'         => 'Interfaces & Technologies Research Lab',
        'info_directeur'    => 'Prof. Bob-Antoine Jerry Ménélas',
        'info_localisation' => 'Quebec, Canada',
        'info_langues'      => 'French · English',
        'contact_email'     => '',
        'contact_github'    => '',
        'contact_linkedin'  => '',
        'contact_institution_url' => 'https://uqac.ca',
        'partners_label'    => 'Collaborations',
        'partners_titre'    => 'Partner Companies & Institutions',
        'footer_texte'      => '© ' . date('Y') . ' Deladem — HCI Researcher',
        'footer_mention'    => 'Built with passion · Quebec, Canada',
    ];

    function g( $key, $defaults ) {
        return esc_attr( get_option( 'dlm_' . $key, $defaults[$key] ?? '' ) );
    }
    ?>

    <div class="wrap">
        <h1>Options du thème Deladem IHM</h1>
        <p style="color:#666;margin-bottom:2rem;">Toutes les informations affichées sur le site sont modifiables ici. N'oubliez pas de sauvegarder.</p>

        <form method="post">
            <?php wp_nonce_field('deladem_options_save'); ?>

            <!-- ──── HERO ──── -->
            <div style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:1.5rem;margin-bottom:1.5rem;">
                <h2 style="margin-bottom:1rem;border-bottom:2px solid #C94A2D;padding-bottom:.5rem;">Section Hero (bandeau principal)</h2>
                <table class="form-table">
                    <tr><th>Étiquette supérieure</th>
                        <td><input type="text" name="hero_etiquette" value="<?php echo g('hero_etiquette',$d); ?>" class="regular-text" placeholder="Doctorant en Informatique"></td></tr>
                    <tr><th>Titre — Ligne 1</th>
                        <td><input type="text" name="hero_titre_ligne1" value="<?php echo g('hero_titre_ligne1',$d); ?>" class="regular-text" placeholder="Chercheur en"></td></tr>
                    <tr><th>Titre — Texte en rouge (italique)</th>
                        <td>
                            <input type="text" name="hero_titre_em" value="<?php echo g('hero_titre_em',$d); ?>" class="large-text" placeholder="Interaction Humain–Machine">
                            <p class="description">Ce texte s'affiche en rouge italique</p>
                        </td></tr>
                    <tr><th>Titre — Ligne 3 (optionnel)</th>
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
                    <tr><th>Bouton principal — Texte</th>
                        <td><input type="text" name="hero_btn1_label" value="<?php echo g('hero_btn1_label',$d); ?>" class="regular-text"></td></tr>
                    <tr><th>Bouton principal — Lien</th>
                        <td><input type="text" name="hero_btn1_url" value="<?php echo g('hero_btn1_url',$d); ?>" class="regular-text" placeholder="#research ou https://..."></td></tr>
                    <tr><th>Bouton secondaire — Texte</th>
                        <td><input type="text" name="hero_btn2_label" value="<?php echo g('hero_btn2_label',$d); ?>" class="regular-text"></td></tr>
                    <tr><th>Bouton secondaire — Lien</th>
                        <td><input type="text" name="hero_btn2_url" value="<?php echo g('hero_btn2_url',$d); ?>" class="regular-text"></td></tr>
                </table>
                <h3 style="margin:1rem 0 .5rem;">Badges flottants (capteurs / technologies) — illimit&eacute;</h3>
                <div id="dlm-badges-wrap">
                    <?php
                    $badges = get_option( 'dlm_hero_badges', [ 'EEG · Neurosity Crown', 'ECG · Polar H10', 'Eye Tracking', 'Thermal Camera' ] );
                    foreach ( (array) $badges as $b ) : ?>
                    <div class="dlm-repeater-row" style="display:flex;gap:.5rem;margin-bottom:.5rem;align-items:center;">
                        <input type="text" name="hero_badges[]" value="<?php echo esc_attr( $b ); ?>" class="regular-text" placeholder="Nom du badge">
                        <button type="button" class="button dlm-remove-row" title="Supprimer">&times;</button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="button" id="dlm-add-badge">+ Ajouter un badge</button>
                <h3 style="margin:1rem 0 .5rem;">Statistiques (panneau sombre)</h3>
                <table class="form-table">
                    <?php for ( $i = 1; $i <= 4; $i++ ) : ?>
                    <tr>
                        <th>Stat <?php echo $i; ?></th>
                        <td>
                            <input type="text" name="hero_stat<?php echo $i; ?>_num" value="<?php echo g("hero_stat{$i}_num",$d); ?>" style="width:100px" placeholder="Valeur"> &nbsp;
                            <input type="text" name="hero_stat<?php echo $i; ?>_label" value="<?php echo g("hero_stat{$i}_label",$d); ?>" style="width:200px" placeholder="Libell&eacute;">
                        </td>
                    </tr>
                    <?php endfor; ?>
                </table>
            </div>

            <!-- ──── À PROPOS ──── -->
            <div style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:1.5rem;margin-bottom:1.5rem;">
                <h2 style="margin-bottom:1rem;border-bottom:2px solid #C94A2D;padding-bottom:.5rem;">Section À propos</h2>
                <table class="form-table">
                    <tr><th>Titre de section</th>
                        <td><input type="text" name="about_titre" value="<?php echo g('about_titre',$d); ?>" class="large-text"></td></tr>
                    <tr><th>Texte biographique</th>
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
                <h3 style="margin:1rem 0 .5rem;">Encadré d'informations (sidebar)</h3>
                <table class="form-table">
                    <tr><th>Institution</th><td><input type="text" name="info_institution" value="<?php echo g('info_institution',$d); ?>" class="large-text"></td></tr>
                    <tr><th>Laboratoire(s)</th>
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
                                <button type="button" class="button dlm-remove-row" title="Supprimer">&times;</button>
                            </div>
                            <?php endforeach; ?>
                            </div>
                            <button type="button" class="button dlm-add-row" data-target="dlm-labos-wrap" data-name="info_labos[]" data-class="large-text">+ Ajouter un laboratoire</button>
                        </td>
                    </tr>
                    <tr><th>Directeur(s) de m&eacute;moire</th>
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
                                <button type="button" class="button dlm-remove-row" title="Supprimer">&times;</button>
                            </div>
                            <?php endforeach; ?>
                            </div>
                            <button type="button" class="button dlm-add-row" data-target="dlm-directeurs-wrap" data-name="info_directeurs[]" data-class="large-text">+ Ajouter un directeur</button>
                        </td>
                    </tr>
                    <tr><th>Localisation</th><td><input type="text" name="info_localisation" value="<?php echo g('info_localisation',$d); ?>" class="regular-text"></td></tr>
                    <tr><th>Langues</th><td><input type="text" name="info_langues" value="<?php echo g('info_langues',$d); ?>" class="regular-text" placeholder="Français · Anglais"></td></tr>
                </table>
            </div>

            <!-- ──── INTÉRÊTS DE RECHERCHE ──── -->
            <div style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:1.5rem;margin-bottom:1.5rem;">
                <h2 style="margin-bottom:1rem;border-bottom:2px solid #C94A2D;padding-bottom:.5rem;">Int&eacute;r&ecirc;ts de recherche</h2>
                <p class="description" style="margin-bottom:1rem;">Chaque int&eacute;r&ecirc;t s'affiche dans un panneau d&eacute;pliant sur le site. Vous pouvez ajouter une ic&ocirc;ne SVG, une description et un lien.</p>
                <div id="dlm-interets-wrap">
                    <?php
                    $interets = get_option( 'dlm_interets', [] );
                    foreach ( (array) $interets as $idx_int => $int ) :
                        $iid = $int['icon_id'] ?? 0;
                        $iurl = $iid ? wp_get_attachment_url( $iid ) : '';
                    ?>
                    <div class="dlm-interet-row" style="margin-bottom:1rem;padding:1.25rem;background:#f9f9f9;border-radius:8px;border:1px solid #e0e0e0;position:relative;">
                        <button type="button" class="button dlm-remove-interet" title="Supprimer" style="position:absolute;top:10px;right:10px;color:#a00;border-color:#ddd;min-width:auto;padding:0 8px;line-height:28px;">&times;</button>
                        <div style="display:grid;grid-template-columns:auto 1fr;gap:1rem;align-items:start;">
                            <div style="text-align:center;">
                                <input type="hidden" name="interet_icon_id[]" value="<?php echo esc_attr( $iid ); ?>" class="dlm-interet-icon-id">
                                <div class="dlm-interet-icon-preview" style="width:48px;height:48px;border:2px dashed #ccc;border-radius:8px;display:flex;align-items:center;justify-content:center;background:#fff;margin-bottom:6px;<?php echo $iurl ? 'border-style:solid;border-color:#C94A2D;' : ''; ?>"><?php if ( $iurl ) : ?><img src="<?php echo esc_url( $iurl ); ?>" style="width:36px;height:36px;" alt=""><?php else : ?><span style="color:#bbb;font-size:20px;">+</span><?php endif; ?></div>
                                <button type="button" class="button button-small dlm-interet-icon-upload" style="font-size:11px;">Ic&ocirc;ne</button>
                                <button type="button" class="button button-small dlm-interet-icon-remove" style="font-size:11px;color:#a00;<?php echo $iid ? '' : 'display:none;'; ?>">Retirer</button>
                            </div>
                            <div>
                                <div style="margin-bottom:.6rem;">
                                    <label style="font-weight:600;font-size:12px;text-transform:uppercase;letter-spacing:.04em;color:#555;display:block;margin-bottom:3px;">Titre</label>
                                    <input type="text" name="interet_titre[]" value="<?php echo esc_attr( $int['titre'] ?? '' ); ?>" class="large-text" placeholder="Ex: Acquisition multi-capteurs">
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
                                    <label style="font-weight:600;font-size:12px;text-transform:uppercase;letter-spacing:.04em;color:#555;display:block;margin-bottom:3px;">Lien (optionnel)</label>
                                    <input type="url" name="interet_url[]" value="<?php echo esc_attr( $int['url'] ?? '' ); ?>" class="large-text" placeholder="https://lien-vers-details.com">
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="button button-primary" id="dlm-add-interet" style="margin-top:.5rem;">+ Ajouter un int&eacute;r&ecirc;t</button>
            </div>

            <!-- ──── PARTENAIRES ──── -->
            <div style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:1.5rem;margin-bottom:1.5rem;">
                <h2 style="margin-bottom:1rem;border-bottom:2px solid #C94A2D;padding-bottom:.5rem;">Section Partenaires / Logos</h2>
                <table class="form-table">
                    <tr><th>Étiquette de section</th>
                        <td><input type="text" name="partners_label" value="<?php echo g('partners_label',$d); ?>" class="regular-text" placeholder="Collaborations"></td></tr>
                    <tr><th>Titre de section</th>
                        <td><input type="text" name="partners_titre" value="<?php echo g('partners_titre',$d); ?>" class="large-text"></td></tr>
                </table>
                <div style="background:#f0f6ff;padding:1rem;border-radius:6px;margin-top:1rem;">
                    <strong>Pour ajouter des logos :</strong> allez dans <strong>Partenaires</strong> dans le menu latéral.
                    Pour chaque partenaire, uploadez le logo via "Image mise en avant". 
                    Recommandation : logo PNG fond transparent, largeur ~300px.
                </div>
            </div>

            <!-- ──── CONTACT ──── -->
            <div style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:1.5rem;margin-bottom:1.5rem;">
                <h2 style="margin-bottom:1rem;border-bottom:2px solid #C94A2D;padding-bottom:.5rem;">Contact & Réseaux sociaux</h2>
                <table class="form-table">
                    <tr><th>Email de contact</th><td><input type="email" name="contact_email" value="<?php echo g('contact_email',$d); ?>" class="regular-text" placeholder="votre@email.com"></td></tr>
                    <tr><th>URL GitHub</th><td><input type="url" name="contact_github" value="<?php echo g('contact_github',$d); ?>" class="regular-text" placeholder="https://github.com/..."></td></tr>
                    <tr><th>URL LinkedIn</th><td><input type="url" name="contact_linkedin" value="<?php echo g('contact_linkedin',$d); ?>" class="regular-text" placeholder="https://linkedin.com/in/..."></td></tr>
                    <tr><th>URL Institution</th><td><input type="url" name="contact_institution_url" value="<?php echo g('contact_institution_url',$d); ?>" class="regular-text" placeholder="https://uqac.ca"></td></tr>
                </table>
            </div>

            <!-- ──── FOOTER ──── -->
            <div style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:1.5rem;margin-bottom:1.5rem;">
                <h2 style="margin-bottom:1rem;border-bottom:2px solid #C94A2D;padding-bottom:.5rem;">Pied de page</h2>
                <table class="form-table">
                    <tr><th>Texte gauche</th><td><input type="text" name="footer_texte"   value="<?php echo g('footer_texte',$d);   ?>" class="large-text"></td></tr>
                    <tr><th>Texte droite</th><td><input type="text" name="footer_mention" value="<?php echo g('footer_mention',$d); ?>" class="large-text"></td></tr>
                </table>
            </div>

            <?php submit_button( 'Sauvegarder toutes les options', 'primary large', 'deladem_save' ); ?>
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
            row.innerHTML = '<input type="text" name="hero_badges[]" value="" class="regular-text" placeholder="Nom du badge">'
                + '<button type="button" class="button dlm-remove-row" title="Supprimer">&times;</button>';
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
                    + '<button type="button" class="button dlm-remove-row" title="Supprimer">&times;</button>';
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
            row.innerHTML = '<button type="button" class="button dlm-remove-interet" title="Supprimer" style="position:absolute;top:10px;right:10px;color:#a00;border-color:#ddd;min-width:auto;padding:0 8px;line-height:28px;">&times;</button>'
                + '<div style="display:grid;grid-template-columns:auto 1fr;gap:1rem;align-items:start;">'
                + '<div style="text-align:center;">'
                + '<input type="hidden" name="interet_icon_id[]" value="" class="dlm-interet-icon-id">'
                + '<div class="dlm-interet-icon-preview" style="width:48px;height:48px;border:2px dashed #ccc;border-radius:8px;display:flex;align-items:center;justify-content:center;background:#fff;margin-bottom:6px;"><span style="color:#bbb;font-size:20px;">+</span></div>'
                + '<button type="button" class="button button-small dlm-interet-icon-upload" style="font-size:11px;">Ic\u00f4ne</button>'
                + '<button type="button" class="button button-small dlm-interet-icon-remove" style="font-size:11px;color:#a00;display:none;">Retirer</button>'
                + '</div>'
                + '<div>'
                + '<div style="margin-bottom:.6rem;"><label style="' + lbl + '">Titre</label><input type="text" name="interet_titre[]" class="large-text" placeholder="Ex: Acquisition multi-capteurs"></div>'
                + '<div style="margin-bottom:.6rem;"><label style="' + lbl + '">Description</label><textarea id="' + editorId + '" name="interet_desc[]" rows="4" class="large-text"></textarea></div>'
                + '<div><label style="' + lbl + '">Lien (optionnel)</label><input type="url" name="interet_url[]" class="large-text" placeholder="https://lien-vers-details.com"></div>'
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

    // Section Hero
    $wp_customize->add_section( 'deladem_hero', [
        'title' => 'Hero (bandeau principal)',
        'panel' => 'deladem_panel',
    ] );

    $hero_fields = [
        'hero_etiquette'    => 'Etiquette',
        'hero_titre_ligne1' => 'Titre - Ligne 1',
        'hero_titre_em'     => 'Titre - Texte accent',
    ];
    foreach ( $hero_fields as $key => $label ) {
        $wp_customize->add_setting( 'dlm_' . $key, [ 'type' => 'option', 'sanitize_callback' => 'sanitize_text_field' ] );
        $wp_customize->add_control( 'dlm_' . $key, [ 'label' => $label, 'section' => 'deladem_hero', 'type' => 'text' ] );
    }
    $wp_customize->add_setting( 'dlm_hero_description', [ 'type' => 'option', 'sanitize_callback' => 'wp_kses_post' ] );
    $wp_customize->add_control( 'dlm_hero_description', [ 'label' => 'Description', 'section' => 'deladem_hero', 'type' => 'textarea' ] );

    // Section À propos
    $wp_customize->add_section( 'deladem_about', [
        'title' => 'A propos',
        'panel' => 'deladem_panel',
    ] );
    $wp_customize->add_setting( 'dlm_about_titre', [ 'type' => 'option', 'sanitize_callback' => 'sanitize_text_field' ] );
    $wp_customize->add_control( 'dlm_about_titre', [ 'label' => 'Titre', 'section' => 'deladem_about', 'type' => 'text' ] );
    $wp_customize->add_setting( 'dlm_about_texte', [ 'type' => 'option', 'sanitize_callback' => 'wp_kses_post' ] );
    $wp_customize->add_control( 'dlm_about_texte', [ 'label' => 'Texte biographique', 'section' => 'deladem_about', 'type' => 'textarea' ] );

    // Section Contact
    $wp_customize->add_section( 'deladem_contact', [
        'title' => 'Contact & Reseaux',
        'panel' => 'deladem_panel',
    ] );
    $contact_fields = [
        'contact_email'   => 'Email',
        'contact_github'  => 'GitHub URL',
        'contact_linkedin' => 'LinkedIn URL',
    ];
    foreach ( $contact_fields as $key => $label ) {
        $wp_customize->add_setting( 'dlm_' . $key, [ 'type' => 'option', 'sanitize_callback' => 'sanitize_text_field' ] );
        $wp_customize->add_control( 'dlm_' . $key, [ 'label' => $label, 'section' => 'deladem_contact', 'type' => 'text' ] );
    }

    // Section Footer
    $wp_customize->add_section( 'deladem_footer', [
        'title' => 'Pied de page',
        'panel' => 'deladem_panel',
    ] );
    $wp_customize->add_setting( 'dlm_footer_texte', [ 'type' => 'option', 'sanitize_callback' => 'wp_kses_post' ] );
    $wp_customize->add_control( 'dlm_footer_texte', [ 'label' => 'Texte gauche', 'section' => 'deladem_footer', 'type' => 'text' ] );
    $wp_customize->add_setting( 'dlm_footer_mention', [ 'type' => 'option', 'sanitize_callback' => 'wp_kses_post' ] );
    $wp_customize->add_control( 'dlm_footer_mention', [ 'label' => 'Texte droite', 'section' => 'deladem_footer', 'type' => 'text' ] );

    // Lien vers la page d'options complète
    $wp_customize->add_section( 'deladem_options_link', [
        'title'       => 'Options avancees',
        'panel'       => 'deladem_panel',
        'description' => sprintf(
            '<a href="%s" class="button" style="margin-top:.5rem">Ouvrir les options compl&egrave;tes</a><br><br>Les badges, int&eacute;r&ecirc;ts, directeurs et laboratoires se g&egrave;rent dans la page d\'options avanc&eacute;es.',
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

/* Fallback menu */
function deladem_fallback_menu() {
    $items = [ '#about' => 'About', '#research' => 'Research', '#publications' => 'Publications', '#cv' => 'Background', '#contact' => 'Contact' ];
    echo '<ul id="primary-menu">';
    foreach ( $items as $url => $label ) echo '<li><a href="' . esc_attr($url) . '">' . esc_html($label) . '</a></li>';
    echo '</ul>';
}

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
