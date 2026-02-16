<?php get_header(); ?>
<div class="section-wrap">
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
  <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="margin-bottom:2rem;padding-bottom:2rem;border-bottom:1px solid var(--border);">
    <h2 style="font-family:'DM Serif Display',serif;margin-bottom:.5rem;"><a href="<?php the_permalink(); ?>" style="text-decoration:none;color:var(--ink);"><?php the_title(); ?></a></h2>
    <p style="font-size:.8rem;color:var(--muted);font-family:var(--mono);margin-bottom:1rem;"><?php the_date(); ?></p>
    <?php the_excerpt(); ?>
  </article>
  <?php endwhile; the_posts_pagination(); else : ?>
  <p>Aucun contenu trouvé.</p>
  <?php endif; ?>
</div>
<?php get_footer(); ?>
