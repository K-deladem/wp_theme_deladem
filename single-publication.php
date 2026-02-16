<?php get_header(); ?>

<?php while ( have_posts() ) : the_post();
    $annee   = get_post_meta( get_the_ID(), '_pub_annee', true );
    $auteurs = get_post_meta( get_the_ID(), '_pub_auteurs', true );
    $revue   = get_post_meta( get_the_ID(), '_pub_revue', true );
    $type    = get_post_meta( get_the_ID(), '_pub_type', true );
    $doi     = get_post_meta( get_the_ID(), '_pub_doi', true );
    $labels  = [ 'conference' => 'Conférence', 'journal' => 'Revue', 'poster' => 'Poster', 'rapport' => 'Rapport', 'these' => 'Thèse', 'workshop' => 'Workshop' ];
?>

<article class="single-publication">
    <div class="section-wrap">
        <a href="<?php echo esc_url( home_url( '/#publications' ) ); ?>" class="back-link">&larr; Retour aux publications</a>

        <div class="pub-single-header">
            <?php if ( $type ) : ?>
            <span class="pub-type <?php echo esc_attr( in_array( $type, ['conference','journal','poster'] ) ? $type : '' ); ?>">
                <?php echo esc_html( $labels[ $type ] ?? 'Publication' ); ?>
            </span>
            <?php endif; ?>

            <?php if ( $annee ) : ?>
            <span class="pub-year-single"><?php echo esc_html( $annee ); ?></span>
            <?php endif; ?>
        </div>

        <h1 class="section-title"><?php the_title(); ?></h1>

        <?php if ( $auteurs ) : ?>
        <p class="pub-auteurs-single"><?php echo esc_html( $auteurs ); ?></p>
        <?php endif; ?>

        <?php if ( $revue ) : ?>
        <p class="pub-revue-single"><?php echo esc_html( $revue ); ?></p>
        <?php endif; ?>

        <?php if ( $doi ) : ?>
        <a href="<?php echo esc_url( $doi ); ?>" class="btn-primary" target="_blank" rel="noopener noreferrer" style="margin-top:1.5rem;">
            Voir la publication &rarr;
        </a>
        <?php endif; ?>

        <?php if ( get_the_content() ) : ?>
        <div class="pub-body-single" style="margin-top:3rem;">
            <?php the_content(); ?>
        </div>
        <?php endif; ?>
    </div>
</article>

<?php endwhile; ?>

<?php get_footer(); ?>
