<?php get_header(); ?>

<!-- ══════ HERO ══════ -->
<section id="hero">
  <div class="hero-left">
    <p class="hero-tag"><?php echo esc_html( dlm_opt('hero_etiquette', 'Doctorant en Informatique') ); ?></p>

    <h1 class="hero-title">
      <?php
        $l1 = dlm_opt('hero_titre_ligne1', 'Chercheur en');
        $em = dlm_opt('hero_titre_em', 'Interaction Humain–Machine');
        $l3 = dlm_opt('hero_titre_ligne3', '');
        if ($l1) echo esc_html($l1) . '<br>';
        if ($em) echo '<em>' . esc_html($em) . '</em>';
        if ($l3) echo '<br>' . esc_html($l3);
      ?>
    </h1>

    <div class="hero-desc"><?php echo wp_kses_post( dlm_opt('hero_description', '') ); ?></div>

    <div class="hero-actions">
      <a href="<?php echo esc_url( dlm_opt('hero_btn1_url','#research') ); ?>" class="btn-primary">
        <?php echo esc_html( dlm_opt('hero_btn1_label','Mes recherches →') ); ?>
      </a>
      <?php $l2 = dlm_opt('hero_btn2_label'); if ( $l2 ) : ?>
      <a href="<?php echo esc_url( dlm_opt('hero_btn2_url','#publications') ); ?>" class="btn-secondary">
        <?php echo esc_html($l2); ?>
      </a>
      <?php endif; ?>
    </div>
  </div>

  <div class="hero-right">
    <div class="hero-bg-pattern"></div>
    <div class="hero-grid"></div>

    <?php
    $badges = get_option( 'dlm_hero_badges', [ 'EEG · Neurosity Crown', 'ECG · Polar H10', 'Eye Tracking', 'Thermal Camera' ] );
    $badge_count = count( array_filter( (array) $badges ) );
    if ( $badge_count ) : ?>
    <div class="sensor-badges" style="--badge-total:<?php echo $badge_count; ?>">
      <?php $badge_idx = 0;
      foreach ( (array) $badges as $badge ) :
        if ( $badge ) : ?>
      <span class="sensor-badge" style="--badge-idx:<?php echo (int) $badge_idx; ?>"><?php echo esc_html( $badge ); ?></span>
      <?php $badge_idx++; endif; endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="hero-right-inner">
      <div class="hero-stat-block">
        <?php for ( $i = 1; $i <= 4; $i++ ) :
          $num   = dlm_opt("hero_stat{$i}_num");
          $label = dlm_opt("hero_stat{$i}_label");
          if ($num || $label) : ?>
        <div class="hero-stat">
          <div class="num"><?php echo esc_html($num); ?></div>
          <div class="label"><?php echo esc_html($label); ?></div>
        </div>
        <?php endif; endfor; ?>
      </div>
    </div>
  </div>
</section>


<!-- ══════ À PROPOS ══════ -->
<section id="about">
  <div class="section-wrap">
    <p class="section-label">À propos</p>
    <h2 class="section-title"><?php echo esc_html( dlm_opt('about_titre', 'Comprendre l\'humain par les données') ); ?></h2>

    <div class="about-grid">
      <div class="about-text fade-up">
        <?php
        $texte = dlm_opt('about_texte');
        if ($texte) {
            echo wp_kses( wpautop($texte), [ 'p'=>[], 'strong'=>[], 'em'=>[], 'a'=>['href'=>[],'target'=>[],'rel'=>[]], 'br'=>[] ] );
        } else { ?>
          <p>Je suis doctorant en informatique à l'<strong>Université du Québec à Chicoutimi (UQAC)</strong>, sous la direction du Professeur Bob-Antoine Jerry Ménélas.</p>
          <p>Mes travaux portent sur la conception de <strong>systèmes d'acquisition de données physiologiques multi-capteurs</strong> pour la recherche en IHM, combinant EEG, ECG, eye-tracker et caméra thermique via <strong>Lab Streaming Layer</strong>.</p>
          <p style="color:var(--accent);font-size:.9rem;"><em>→ Modifiez ce texte dans Apparence &gt; Options Deladem &gt; Section À propos</em></p>
        <?php } ?>
      </div>

      <div class="about-sidebar fade-up">
        <div class="info-block">
          <?php
          // Laboratoires multiples
          $labos = get_option( 'dlm_info_labos', [] );
          if ( empty( $labos ) ) {
              $legacy = dlm_opt( 'info_labo', '' );
              $labos = $legacy ? [ $legacy ] : [];
          }
          $labos_str = implode( ' · ', array_filter( $labos ) );

          // Directeurs multiples
          $directeurs = get_option( 'dlm_info_directeurs', [] );
          if ( empty( $directeurs ) ) {
              $legacy = dlm_opt( 'info_directeur', '' );
              $directeurs = $legacy ? [ $legacy ] : [];
          }
          $directeurs_str = implode( ' · ', array_filter( $directeurs ) );

          $rows = [
            '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 1.1 2.7 3 6 3s6-1.9 6-3v-5"/></svg>' => ['Institution', dlm_opt('info_institution','UQAC')],
            '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>' => ['Laboratoire(s)', $labos_str],
            '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>' => ['Directeur(s)', $directeurs_str],
            '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>' => ['Localisation', dlm_opt('info_localisation','Québec, Canada')],
            '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>' => ['Langues', dlm_opt('info_langues','Français · Anglais')],
          ];
          $email = dlm_opt('contact_email');
          if ( $email ) $rows['<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>'] = ['Email', $email];

          foreach ( $rows as $icon => [$label, $val] ) :
            if ( ! $val ) continue;
          ?>
          <div class="info-row">
            <span class="info-icon"><?php echo $icon; ?></span>
            <div class="info-content">
              <div class="label"><?php echo esc_html($label); ?></div>
              <div class="value">
                <?php if ($label === 'Email') : ?>
                  <a href="mailto:<?php echo esc_attr($val); ?>"><?php echo esc_html($val); ?></a>
                <?php else : echo esc_html($val); endif; ?>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>


<div class="section-divider"></div>

<!-- ══════ INTÉRÊTS DE RECHERCHE ══════ -->
<?php $interets = get_option( 'dlm_interets', [] ); if ( ! empty( $interets ) ) : ?>
<section id="interets">
  <div class="section-wrap">
    <p class="section-label">Domaines</p>
    <h2 class="section-title">Int&eacute;r&ecirc;ts de recherche</h2>
    <p class="section-sub">Cliquez pour en savoir plus sur chaque domaine.</p>
    <div class="interets-list">
      <?php foreach ( $interets as $idx => $int ) :
        $has_url  = ! empty( $int['url'] );
        $has_desc = ! empty( $int['desc'] );
      ?>
      <div class="interet-item fade-up" data-index="<?php echo (int) $idx; ?>">
        <button class="interet-header" type="button" aria-expanded="false">
          <span class="interet-num"></span>
          <?php $int_svg = deladem_render_svg_icon( $int['icon_id'] ?? 0, 20 );
            if ( $int_svg ) : ?>
            <span class="interet-icon-wrap"><?php echo $int_svg; ?></span>
          <?php endif; ?>
          <span class="interet-titre"><?php echo esc_html( $int['titre'] ); ?></span>
          <span class="interet-toggle" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          </span>
        </button>
        <?php if ( $has_desc || $has_url ) : ?>
        <div class="interet-body" aria-hidden="true">
          <div class="interet-body-inner">
            <?php if ( $has_desc ) : ?>
              <div class="interet-desc"><?php echo wp_kses_post( $int['desc'] ); ?></div>
            <?php endif; ?>
            <?php if ( $has_url ) : ?>
              <a href="<?php echo esc_url( $int['url'] ); ?>" class="interet-link" target="_blank" rel="noopener noreferrer">
                Voir sur internet &rarr;
              </a>
            <?php endif; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<div class="section-divider"></div>
<?php endif; ?>


<!-- ══════ PARTENAIRES / LOGOS ══════ -->
<?php $partners_q = deladem_get_partenaires(20); if ( $partners_q->have_posts() ) : ?>
<section id="partners">
  <div class="partners-inner">
    <p class="section-label"><?php echo esc_html( dlm_opt('partners_label','Collaborations') ); ?></p>
    <h2 class="section-title"><?php echo esc_html( dlm_opt('partners_titre','Entreprises & institutions partenaires') ); ?></h2>

    <div class="partners-grid">
      <?php while ( $partners_q->have_posts() ) : $partners_q->the_post();
        $url       = get_post_meta( get_the_ID(), '_partenaire_url',      true );
        $initiales = get_post_meta( get_the_ID(), '_partenaire_initiales', true );
      ?>
      <?php if ( $url ) : ?>
      <a class="partner-item" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer">
      <?php else : ?>
      <div class="partner-item">
      <?php endif; ?>
        <?php if ( has_post_thumbnail() ) : ?>
          <?php the_post_thumbnail( 'partner-logo', [ 'class' => 'partner-logo', 'alt' => esc_attr( get_the_title() ), 'loading' => 'lazy' ] ); ?>
        <?php else : ?>
          <div class="partner-initials"><?php echo esc_html( $initiales ?: mb_substr( get_the_title(), 0, 2 ) ); ?></div>
        <?php endif; ?>
        <span class="partner-name"><?php the_title(); ?></span>
      <?php if ( $url ) : ?>
      </a>
      <?php else : ?>
      </div>
      <?php endif; ?>
      <?php endwhile; wp_reset_postdata(); ?>
    </div>
  </div>
</section>
<?php endif; ?>

<div class="section-divider"></div>

<!-- ══════ PROJETS ══════ -->
<section id="research">
  <div class="section-wrap">
    <p class="section-label">Axes de recherche</p>
    <h2 class="section-title">Projets en cours</h2>
    <p class="section-sub">Systèmes multimodaux, réalité virtuelle et physiologie pour l'IHM.</p>

    <?php $projets_q = deladem_get_projets(6); ?>

    <?php if ( $projets_q->have_posts() ) : ?>
    <div class="research-grid">
      <?php $i = 1; while ( $projets_q->have_posts() ) : $projets_q->the_post();
      ?>
      <a class="research-card fade-up" href="<?php the_permalink(); ?>" style="transition-delay:<?php echo ($i-1)*0.1; ?>s">
        <div class="research-num"><?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?></div>
        <span class="research-icon"><?php
            $p_icon = deladem_render_svg_icon( get_post_meta( get_the_ID(), '_projet_icon_id', true ), 24 );
            echo $p_icon ?: deladem_default_projet_icon();
        ?></span>
        <h3><?php the_title(); ?></h3>
        <p><?php echo wp_trim_words( get_the_excerpt() ?: strip_tags(get_the_content()), 30, '…' ); ?></p>
        <?php deladem_render_projet_tags( get_the_ID() ); ?>
      </a>
      <?php $i++; endwhile; wp_reset_postdata(); ?>
    </div>
    <?php else : ?>
    <div style="padding:3rem;text-align:center;border:2px dashed var(--border);border-radius:16px;color:var(--muted);">
      <p style="margin-bottom:1rem;"><svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color:var(--muted)"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg></p>
      <p><strong>Aucun projet publié pour l'instant.</strong></p>
      <p>Allez dans <strong>Tableau de bord → Projets → Ajouter</strong> pour créer vos premiers projets de recherche.</p>
    </div>
    <?php endif; ?>
  </div>
</section>

<div class="section-divider"></div>


<!-- ══════ PUBLICATIONS ══════ -->
<section id="publications">
  <div class="section-wrap">
    <p class="section-label">Publications</p>
    <h2 class="section-title">Travaux académiques</h2>
    <p class="section-sub">Articles, conférences et contributions scientifiques.</p>

    <?php $pubs_q = deladem_get_publications(15); ?>

    <?php if ( $pubs_q->have_posts() ) : ?>
    <div class="pub-list fade-up">
      <?php while ( $pubs_q->have_posts() ) : $pubs_q->the_post();
        $annee   = get_post_meta( get_the_ID(), '_pub_annee',   true );
        $auteurs = get_post_meta( get_the_ID(), '_pub_auteurs', true );
        $revue   = get_post_meta( get_the_ID(), '_pub_revue',   true );
        $type    = get_post_meta( get_the_ID(), '_pub_type',    true );
        $doi     = get_post_meta( get_the_ID(), '_pub_doi',     true );
        $labels  = [ 'conference' => 'Conférence', 'journal' => 'Revue', 'poster' => 'Poster', 'rapport' => 'Rapport', 'these' => 'Thèse', 'workshop' => 'Workshop' ];
        $url = $doi ?: get_permalink();
        $external = (bool) $doi;
      ?>
      <a class="pub-item" href="<?php echo esc_url($url); ?>" <?php if($external) echo 'target="_blank" rel="noopener noreferrer"'; ?>>
        <div class="pub-year"><?php echo esc_html($annee); ?></div>
        <div class="pub-body">
          <h4><?php the_title(); ?></h4>
          <?php if ($auteurs) echo '<div class="authors">' . esc_html($auteurs) . '</div>'; ?>
          <?php if ($revue)   echo '<div class="venue">'   . esc_html($revue)   . '</div>'; ?>
        </div>
        <span class="pub-type <?php echo esc_attr(in_array($type,['conference','journal','poster'])?$type:''); ?>">
          <?php echo esc_html($labels[$type] ?? 'Publication'); ?>
        </span>
      </a>
      <?php endwhile; wp_reset_postdata(); ?>
    </div>
    <?php else : ?>
    <div style="padding:3rem;text-align:center;border:2px dashed var(--border);border-radius:16px;color:var(--muted);">
      <p style="margin-bottom:1rem;"><svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color:var(--muted)"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg></p>
      <p><strong>Aucune publication pour l'instant.</strong></p>
      <p>Allez dans <strong>Tableau de bord → Publications → Ajouter</strong>.</p>
    </div>
    <?php endif; ?>
  </div>
</section>

<div class="section-divider"></div>


<!-- ══════ CV / PARCOURS ══════ -->
<section id="cv">
  <div class="section-wrap">
    <p class="section-label">Parcours</p>
    <h2 class="section-title">Formation &amp; Expérience</h2>
    <p class="section-sub">Académique, enseignement et consulting.</p>

    <div class="cv-cols fade-up">
      <!-- Formations -->
      <div>
        <h3 class="cv-section-title">Formation</h3>
        <?php
        $formations = deladem_get_cv('formation');
        if ( $formations->have_posts() ) :
          $all = [];
          while ($formations->have_posts()) { $formations->the_post(); $all[] = get_the_ID(); }
          wp_reset_postdata();
          foreach ( $all as $idx => $pid ) :
            $last = ($idx === count($all)-1);
        ?>
        <div class="cv-entry">
          <div class="cv-dot-line">
            <div class="cv-dot"></div>
            <?php if (!$last) echo '<div class="cv-line"></div>'; ?>
          </div>
          <div class="cv-entry-body">
            <div class="cv-entry-period"><?php echo esc_html( get_post_meta($pid,'_cv_periode',true) ); ?></div>
            <div class="cv-entry-title"><?php echo esc_html( get_the_title($pid) ); ?></div>
            <div class="cv-entry-place"><?php
              $etab = get_post_meta($pid,'_cv_etablissement',true);
              echo esc_html($etab);
            ?></div>
          </div>
        </div>
        <?php endforeach;
        else : ?>
        <p style="color:var(--muted);font-size:.875rem;font-style:italic;">→ Ajoutez vos formations dans <strong>CV / Parcours → Ajouter</strong> avec la catégorie "Formation".</p>
        <?php endif; ?>

        <!-- Compétences -->
        <?php
        $competences_q = deladem_get_cv('competence');
        if ( $competences_q->have_posts() ) : ?>
        <h3 class="cv-section-title" style="margin-top:2rem;">Compétences</h3>
        <div class="skills-grid">
          <?php while ($competences_q->have_posts()) : $competences_q->the_post(); ?>
          <span class="skill-chip"><?php the_title(); ?></span>
          <?php endwhile; wp_reset_postdata(); ?>
        </div>
        <?php endif; ?>
      </div>

      <!-- Expériences -->
      <div>
        <h3 class="cv-section-title">Expérience</h3>
        <?php
        $experiences = deladem_get_cv('experience');
        if ( $experiences->have_posts() ) :
          $all = [];
          while ($experiences->have_posts()) { $experiences->the_post(); $all[] = get_the_ID(); }
          wp_reset_postdata();
          foreach ( $all as $idx => $pid ) :
            $last = ($idx === count($all)-1);
        ?>
        <div class="cv-entry">
          <div class="cv-dot-line">
            <div class="cv-dot"></div>
            <?php if (!$last) echo '<div class="cv-line"></div>'; ?>
          </div>
          <div class="cv-entry-body">
            <div class="cv-entry-period"><?php echo esc_html( get_post_meta($pid,'_cv_periode',true) ); ?></div>
            <div class="cv-entry-title"><?php echo esc_html( get_the_title($pid) ); ?></div>
            <div class="cv-entry-place"><?php echo esc_html( get_post_meta($pid,'_cv_etablissement',true) ); ?></div>
          </div>
        </div>
        <?php endforeach;
        else : ?>
        <p style="color:var(--muted);font-size:.875rem;font-style:italic;">→ Ajoutez vos expériences dans <strong>CV / Parcours → Ajouter</strong> avec la catégorie "Expérience professionnelle".</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>


<!-- ══════ CONTACT ══════ -->
<section id="contact" class="contact-section">
  <div class="contact-inner">
    <div>
      <p class="section-label">Contact</p>
      <h2 class="section-title">Travaillons ensemble</h2>
      <p class="section-sub">Collaborations de recherche, enseignement ou consulting IT.</p>

      <div class="contact-links">
        <?php $em = dlm_opt('contact_email'); if ( $em ) : ?>
        <a class="contact-link" href="mailto:<?php echo esc_attr($em); ?>">
          <span class="icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
          <div><div class="link-label">Email</div><div><?php echo esc_html($em); ?></div></div>
        </a>
        <?php endif; ?>

        <a class="contact-link" href="<?php echo esc_url( dlm_opt('contact_institution_url','https://uqac.ca') ); ?>" target="_blank" rel="noopener noreferrer">
          <span class="icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11M8 14v4M12 14v4M16 14v4"/></svg></span>
          <div><div class="link-label">Institution</div><div><?php echo esc_html( dlm_opt('info_institution','UQAC') ); ?></div></div>
        </a>

        <?php $gh = dlm_opt('contact_github'); if ( $gh ) : ?>
        <a class="contact-link" href="<?php echo esc_url($gh); ?>" target="_blank" rel="noopener noreferrer">
          <span class="icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg></span>
          <div><div class="link-label">GitHub</div><div>Projets &amp; code source</div></div>
        </a>
        <?php endif; ?>

        <?php $li = dlm_opt('contact_linkedin'); if ( $li ) : ?>
        <a class="contact-link" href="<?php echo esc_url($li); ?>" target="_blank" rel="noopener noreferrer">
          <span class="icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg></span>
          <div><div class="link-label">LinkedIn</div><div>Profil professionnel</div></div>
        </a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Formulaire -->
    <div>
      <?php if ( shortcode_exists('contact-form-7') ) :
        // Remplace "1" par l'ID de ton formulaire CF7
        echo do_shortcode('[contact-form-7 id="1" title="Contact"]');
      else : ?>
      <form class="contact-form-wp" method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
        <?php wp_nonce_field('deladem_contact','deladem_contact_nonce'); ?>
        <input type="hidden" name="action" value="deladem_contact">
        <label>Votre nom<input type="text" name="contact_name" placeholder="Marie Tremblay" required></label>
        <label>Email<input type="email" name="contact_email" placeholder="marie@exemple.com" required></label>
        <label>Sujet<input type="text" name="contact_subject" placeholder="Collaboration de recherche" required></label>
        <label>Message<textarea name="contact_message" placeholder="Décrivez votre demande..." required></textarea></label>
        <input type="submit" value="Envoyer le message →">
      </form>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php get_footer(); ?>
