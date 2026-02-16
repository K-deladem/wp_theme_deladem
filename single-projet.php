<?php get_header(); ?>

<?php while ( have_posts() ) : the_post();
    $tags  = get_post_meta( get_the_ID(), '_projet_tags', true );
?>

<section class="single-projet-hero">
    <div class="section-wrap">
        <a href="<?php echo esc_url( home_url( '/#research' ) ); ?>" class="back-link">&larr; Retour aux projets</a>
        <span class="projet-icon-large"><?php
            $p_icon = deladem_render_svg_icon( get_post_meta( get_the_ID(), '_projet_icon_id', true ), 48 );
            echo $p_icon ?: deladem_default_projet_icon( 48 );
        ?></span>
        <h1 class="section-title"><?php the_title(); ?></h1>
        <?php if ( $tags ) : ?>
        <div class="research-tags" style="margin-top:1rem;">
            <?php foreach ( array_map( 'trim', explode( ',', $tags ) ) as $t ) :
                if ( $t ) : ?>
                <span class="tag"><?php echo esc_html( $t ); ?></span>
            <?php endif; endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<article class="single-projet-content">
    <div class="section-wrap">
        <?php if ( has_post_thumbnail() ) : ?>
        <div class="projet-thumbnail">
            <?php the_post_thumbnail( 'large', [ 'class' => 'projet-featured-img' ] ); ?>
        </div>
        <?php endif; ?>

        <div class="projet-body">
            <?php the_content(); ?>
        </div>
    </div>
</article>

<?php endwhile; ?>

<?php get_footer(); ?>
