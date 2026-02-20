</main></div>

<footer class="site-footer" role="contentinfo">
  <div class="footer-inner">
    <div class="footer-left">
      <div class="footer-brand">
        <a href="<?php echo esc_url( home_url('/') ); ?>">
          <?php bloginfo('name'); ?> <span>&middot;</span> HCI
        </a>
      </div>
      <p class="footer-copy"><?php echo wp_kses_post( dlm_opt('footer_texte', '&copy; ' . date('Y') . ' ' . get_bloginfo('name')) ); ?></p>
    </div>
    <div class="footer-right">
      <div class="footer-links">
        <?php $gh = dlm_opt('contact_github'); if ($gh) : ?>
        <a href="<?php echo esc_url($gh); ?>" target="_blank" rel="noopener noreferrer" aria-label="GitHub">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>
        </a>
        <?php endif; ?>
        <?php $li = dlm_opt('contact_linkedin'); if ($li) : ?>
        <a href="<?php echo esc_url($li); ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
        </a>
        <?php endif; ?>
        <?php $orcid = dlm_opt('contact_orcid'); if ($orcid) : ?>
        <a href="<?php echo esc_url($orcid); ?>" target="_blank" rel="noopener noreferrer" aria-label="ORCID">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8 7v10M12 7h2a3 3 0 0 1 0 6h-2v4M12 7v10"/></svg>
        </a>
        <?php endif; ?>
        <?php $em = dlm_opt('contact_email'); if ($em) : ?>
        <a href="mailto:<?php echo esc_attr($em); ?>" aria-label="Email">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        </a>
        <?php endif; ?>
      </div>
      <p class="footer-mention"><?php echo wp_kses_post( dlm_opt('footer_mention', 'Quebec, Canada') ); ?></p>
    </div>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
