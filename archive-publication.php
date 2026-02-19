<?php get_header(); ?>

<section class="archive-hero">
    <div class="section-wrap">
        <a href="<?php echo esc_url( home_url( '/#publications' ) ); ?>" class="back-link">&larr; Back to home</a>
        <p class="section-label">Publications</p>
        <h1 class="section-title">All Academic Works</h1>
        <p class="section-sub">Articles, conferences and scientific contributions.</p>
    </div>
</section>

<section class="archive-content">
    <div class="section-wrap">
        <?php if ( have_posts() ) : ?>
        <div class="pub-list fade-up">
            <?php
            $labels = [ 'conference' => 'Conference', 'journal' => 'Journal', 'poster' => 'Poster', 'rapport' => 'Report', 'these' => 'Thesis', 'workshop' => 'Workshop' ];
            while ( have_posts() ) : the_post();
                $annee   = get_post_meta( get_the_ID(), '_pub_annee',   true );
                $auteurs = get_post_meta( get_the_ID(), '_pub_auteurs', true );
                $revue   = get_post_meta( get_the_ID(), '_pub_revue',   true );
                $type    = get_post_meta( get_the_ID(), '_pub_type',    true );
                $doi     = get_post_meta( get_the_ID(), '_pub_doi',     true );
                $url      = $doi ?: get_permalink();
                $external = (bool) $doi;
            ?>
            <a class="pub-item" href="<?php echo esc_url( $url ); ?>" <?php if ( $external ) echo 'target="_blank" rel="noopener noreferrer"'; ?>>
                <div class="pub-year"><?php echo esc_html( $annee ); ?></div>
                <div class="pub-body">
                    <h3><?php the_title(); ?></h3>
                    <?php if ( $auteurs ) echo '<div class="authors">' . esc_html( $auteurs ) . '</div>'; ?>
                    <?php if ( $revue ) echo '<div class="venue">' . esc_html( $revue ) . '</div>'; ?>
                </div>
                <span class="pub-type <?php echo esc_attr( in_array( $type, ['conference','journal','poster'] ) ? $type : '' ); ?>">
                    <?php echo esc_html( $labels[ $type ] ?? 'Publication' ); ?>
                </span>
            </a>
            <?php endwhile; ?>
        </div>

        <?php
        $pagination = paginate_links([
            'prev_text' => '&larr;',
            'next_text' => '&rarr;',
            'type'      => 'array',
        ]);
        if ( $pagination ) : ?>
        <nav class="archive-pagination">
            <?php foreach ( $pagination as $page ) : ?>
                <?php echo $page; ?>
            <?php endforeach; ?>
        </nav>
        <?php endif; ?>

        <?php else : ?>
        <div class="archive-empty">
            <p><strong>No publications yet.</strong></p>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
