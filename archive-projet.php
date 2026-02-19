<?php get_header(); ?>

<section class="archive-hero">
    <div class="section-wrap">
        <a href="<?php echo esc_url( home_url( '/#research' ) ); ?>" class="back-link">&larr; Back to home</a>
        <p class="section-label">Research Areas</p>
        <h1 class="section-title">All Projects</h1>
        <p class="section-sub">Multimodal systems, virtual reality and physiology for HCI.</p>
    </div>
</section>

<section class="archive-content">
    <div class="section-wrap">
        <?php if ( have_posts() ) : ?>
        <div class="research-grid">
            <?php $i = 1; while ( have_posts() ) : the_post(); ?>
            <a class="research-card fade-up" href="<?php the_permalink(); ?>">
                <div class="research-num"><?php echo str_pad( $i, 2, '0', STR_PAD_LEFT ); ?></div>
                <span class="research-icon"><?php
                    $p_icon = deladem_render_svg_icon( get_post_meta( get_the_ID(), '_projet_icon_id', true ), 24 );
                    echo $p_icon ?: deladem_default_projet_icon();
                ?></span>
                <h3><?php the_title(); ?></h3>
                <p><?php echo wp_trim_words( get_the_excerpt() ?: strip_tags( get_the_content() ), 30, '...' ); ?></p>
                <?php deladem_render_projet_tags( get_the_ID() ); ?>
            </a>
            <?php $i++; endwhile; ?>
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
            <p><strong>No projects published yet.</strong></p>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
