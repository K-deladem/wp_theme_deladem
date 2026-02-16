</main></div>

<footer class="site-footer" role="contentinfo">
  <p><?php echo wp_kses_post( dlm_opt('footer_texte', '&copy; ' . date('Y') . ' ' . get_bloginfo('name')) ); ?></p>
  <p><?php echo wp_kses_post( dlm_opt('footer_mention', 'Québec, Canada') ); ?></p>
</footer>

<?php wp_footer(); ?>
</body>
</html>
