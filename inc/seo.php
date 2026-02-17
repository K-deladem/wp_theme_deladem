<?php
/**
 * SEO — Meta tags, Open Graph, Twitter Cards, JSON-LD, Performance
 *
 * @package Deladem_IHM
 */

defined( 'ABSPATH' ) || exit;

/* ─── Helper: build meta description ─── */
function deladem_seo_description() {
    if ( is_front_page() ) {
        $desc = dlm_opt( 'seo_description' );
        if ( ! $desc ) {
            $desc = wp_strip_all_tags( dlm_opt( 'hero_description' ) );
        }
    } elseif ( is_singular( 'projet' ) ) {
        $desc = wp_trim_words( get_the_excerpt(), 25, '...' );
        if ( ! $desc ) {
            $desc = wp_trim_words( wp_strip_all_tags( get_the_content() ), 25, '...' );
        }
    } elseif ( is_singular( 'publication' ) ) {
        $auteurs = get_post_meta( get_the_ID(), '_pub_auteurs', true );
        $revue   = get_post_meta( get_the_ID(), '_pub_revue',   true );
        $annee   = get_post_meta( get_the_ID(), '_pub_annee',   true );
        $parts   = array_filter( [ get_the_title(), $auteurs, $revue, $annee ] );
        $desc    = implode( ' — ', $parts );
    } else {
        $desc = get_bloginfo( 'description' );
    }
    return $desc ? mb_substr( wp_strip_all_tags( $desc ), 0, 160 ) : '';
}

/* ─── Helper: get OG image URL ─── */
function deladem_seo_image() {
    if ( is_singular() && has_post_thumbnail() ) {
        return get_the_post_thumbnail_url( get_the_ID(), 'large' );
    }
    $logo_id = get_theme_mod( 'custom_logo' );
    if ( $logo_id ) {
        $url = wp_get_attachment_image_url( $logo_id, 'full' );
        if ( $url ) return $url;
    }
    return '';
}

/* ─── Helper: canonical URL ─── */
function deladem_canonical_url() {
    if ( is_front_page() ) {
        return home_url( '/' );
    }
    return get_permalink();
}

/* ───────────────────────────────────────────────
   1. Meta description + canonical + author
   ─────────────────────────────────────────────── */
add_action( 'wp_head', function () {
    $desc = deladem_seo_description();
    $canonical = deladem_canonical_url();

    if ( $desc ) {
        echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
    }
    echo '<link rel="canonical" href="' . esc_url( $canonical ) . '">' . "\n";
    echo '<meta name="author" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
}, 5 );

/* ───────────────────────────────────────────────
   2. Open Graph + Twitter Cards
   ─────────────────────────────────────────────── */
add_action( 'wp_head', function () {
    $title = is_front_page()
        ? get_bloginfo( 'name' ) . ' — ' . dlm_opt( 'hero_etiquette', 'Researcher' )
        : get_the_title() . ' — ' . get_bloginfo( 'name' );
    $desc  = deladem_seo_description();
    $url   = deladem_canonical_url();
    $image = deladem_seo_image();
    $type  = is_front_page() ? 'website' : 'article';

    // Open Graph
    echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
    echo '<meta property="og:locale" content="' . esc_attr( get_locale() ) . '">' . "\n";
    echo '<meta property="og:type" content="' . esc_attr( $type ) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
    if ( $desc ) {
        echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
    }
    echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
    if ( $image ) {
        echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
    }

    // Twitter Card
    echo '<meta name="twitter:card" content="' . ( $image ? 'summary_large_image' : 'summary' ) . '">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
    if ( $desc ) {
        echo '<meta name="twitter:description" content="' . esc_attr( $desc ) . '">' . "\n";
    }
    if ( $image ) {
        echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";
    }
}, 6 );

/* ───────────────────────────────────────────────
   3. JSON-LD Structured Data
   ─────────────────────────────────────────────── */
add_action( 'wp_head', function () {
    $schema = null;

    /* ── Front page: Person ── */
    if ( is_front_page() ) {
        $same_as = array_filter( [
            dlm_opt( 'contact_orcid' ),
            dlm_opt( 'contact_linkedin' ),
            dlm_opt( 'contact_github' ),
        ] );

        $labos = get_option( 'dlm_info_labos', [] );
        $labo_name = ! empty( $labos[0] ) ? $labos[0] : dlm_opt( 'info_labo' );

        $schema = [
            '@context'    => 'https://schema.org',
            '@type'       => 'Person',
            'name'        => get_bloginfo( 'name' ),
            'jobTitle'    => dlm_opt( 'hero_etiquette', '' ),
            'url'         => home_url( '/' ),
            'description' => deladem_seo_description(),
            'affiliation' => [
                '@type' => 'Organization',
                'name'  => dlm_opt( 'info_institution', '' ),
            ],
            'workLocation' => [
                '@type'   => 'Place',
                'name'    => dlm_opt( 'info_localisation', '' ),
            ],
        ];

        if ( $labo_name ) {
            $schema['memberOf'] = [
                '@type' => 'ResearchOrganization',
                'name'  => $labo_name,
            ];
        }

        $email = dlm_opt( 'contact_email' );
        if ( $email ) {
            $schema['email'] = $email;
        }

        if ( ! empty( $same_as ) ) {
            $schema['sameAs'] = array_values( $same_as );
        }

        $image = deladem_seo_image();
        if ( $image ) {
            $schema['image'] = $image;
        }
    }

    /* ── Single projet: ResearchProject ── */
    if ( is_singular( 'projet' ) ) {
        $tags = get_post_meta( get_the_ID(), '_projet_tags', true );

        $schema = [
            '@context'    => 'https://schema.org',
            '@type'       => 'ResearchProject',
            'name'        => get_the_title(),
            'description' => wp_trim_words( wp_strip_all_tags( get_the_content() ), 50, '...' ),
            'url'         => get_permalink(),
        ];

        if ( has_post_thumbnail() ) {
            $schema['image'] = get_the_post_thumbnail_url( get_the_ID(), 'large' );
        }

        if ( $tags ) {
            $schema['keywords'] = $tags;
        }

        $schema['author'] = [
            '@type' => 'Person',
            'name'  => get_bloginfo( 'name' ),
            'url'   => home_url( '/' ),
        ];
    }

    /* ── Single publication: ScholarlyArticle ── */
    if ( is_singular( 'publication' ) ) {
        $auteurs = get_post_meta( get_the_ID(), '_pub_auteurs', true );
        $revue   = get_post_meta( get_the_ID(), '_pub_revue',   true );
        $annee   = get_post_meta( get_the_ID(), '_pub_annee',   true );
        $doi     = get_post_meta( get_the_ID(), '_pub_doi',     true );
        $type    = get_post_meta( get_the_ID(), '_pub_type',    true );

        // Map type to Schema.org
        $schema_type = 'ScholarlyArticle';
        if ( $type === 'these' ) {
            $schema_type = 'Thesis';
        }

        $schema = [
            '@context'      => 'https://schema.org',
            '@type'         => $schema_type,
            'name'          => get_the_title(),
            'headline'      => get_the_title(),
            'url'           => get_permalink(),
        ];

        if ( $annee ) {
            $schema['datePublished'] = (string) $annee;
        }

        if ( $auteurs ) {
            $authors_arr = array_map( 'trim', explode( ',', $auteurs ) );
            $schema['author'] = array_map( function ( $name ) {
                return [ '@type' => 'Person', 'name' => $name ];
            }, $authors_arr );
        }

        if ( $revue ) {
            $schema['isPartOf'] = [
                '@type' => 'Periodical',
                'name'  => $revue,
            ];
            $schema['publisher'] = [
                '@type' => 'Organization',
                'name'  => $revue,
            ];
        }

        if ( $doi ) {
            if ( strpos( $doi, 'doi.org' ) !== false ) {
                $schema['identifier'] = [
                    '@type'      => 'PropertyValue',
                    'propertyID' => 'DOI',
                    'value'      => $doi,
                ];
                $schema['sameAs'] = $doi;
            } else {
                $schema['sameAs'] = $doi;
            }
        }
    }

    if ( $schema ) {
        echo '<script type="application/ld+json">' . "\n";
        echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
        echo "\n" . '</script>' . "\n";
    }
}, 7 );

/* ───────────────────────────────────────────────
   4. Preconnect hints (priority 1 = before enqueues)
   ─────────────────────────────────────────────── */
add_action( 'wp_head', function () {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}, 1 );

/* ───────────────────────────────────────────────
   5. Defer main.js
   ─────────────────────────────────────────────── */
add_filter( 'script_loader_tag', function ( $tag, $handle ) {
    if ( 'deladem-main' === $handle && ! is_admin() ) {
        $tag = str_replace( ' src=', ' defer src=', $tag );
    }
    return $tag;
}, 10, 2 );
