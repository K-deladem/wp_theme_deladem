<?php get_header(); ?>

<?php while ( have_posts() ) : the_post();
    $annee   = get_post_meta( get_the_ID(), '_pub_annee', true );
    $auteurs = get_post_meta( get_the_ID(), '_pub_auteurs', true );
    $revue   = get_post_meta( get_the_ID(), '_pub_revue', true );
    $type    = get_post_meta( get_the_ID(), '_pub_type', true );
    $doi     = get_post_meta( get_the_ID(), '_pub_doi', true );
    $labels  = [ 'conference' => 'Conference', 'journal' => 'Journal', 'poster' => 'Poster', 'rapport' => 'Report', 'these' => 'Thesis', 'workshop' => 'Workshop' ];
?>

<section class="single-publication-hero">
    <div class="section-wrap">
        <a href="<?php echo esc_url( home_url( '/#publications' ) ); ?>" class="back-link">&larr; Back to publications</a>

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
            View publication &rarr;
        </a>
        <?php endif; ?>
    </div>
</section>

<?php if ( get_the_content() ) : ?>
<article class="single-publication-content">
    <div class="section-wrap">
        <div class="pub-body-single">
            <?php the_content(); ?>
        </div>
    </div>
</article>
<?php endif; ?>

<?php endwhile; ?>

<?php get_footer(); ?>
