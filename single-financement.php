<?php get_header(); ?>

<?php while ( have_posts() ) : the_post();
    $f_partenaire_id = get_post_meta( get_the_ID(), '_financement_partenaire',   true );
    $f_type          = get_post_meta( get_the_ID(), '_financement_type',         true );
    $f_montant       = get_post_meta( get_the_ID(), '_financement_montant',      true );
    $f_devise        = get_post_meta( get_the_ID(), '_financement_devise',       true );
    $f_modalite      = get_post_meta( get_the_ID(), '_financement_modalite',     true );
    $f_statut        = get_post_meta( get_the_ID(), '_financement_statut',       true );
    $f_role          = get_post_meta( get_the_ID(), '_financement_role',          true );
    $f_beneficiaire  = get_post_meta( get_the_ID(), '_financement_beneficiaire', true );
    $f_periode       = get_post_meta( get_the_ID(), '_financement_periode',      true );
    $f_description   = get_post_meta( get_the_ID(), '_financement_description',  true );
    $f_url           = get_post_meta( get_the_ID(), '_financement_url',          true );

    $type_labels = [ 'bourse' => 'Scholarship', 'financement' => 'Funding', 'subvention' => 'Grant', 'contrat' => 'Contract', 'prix' => 'Award' ];
    $statut_labels = [ 'actif' => 'Active', 'termine' => 'Completed', 'en_attente' => 'Pending', 'refuse' => 'Declined', 'suspendu' => 'Suspended' ];
    $role_labels = [
        'chercheur_principal' => 'Principal Investigator', 'co_chercheur' => 'Co-Investigator',
        'collaborateur' => 'Collaborator', 'stagiaire' => 'Intern',
        'postdoc' => 'Postdoctoral Researcher', 'assistant' => 'Research Assistant',
        'coordonnateur' => 'Coordinator', 'responsable' => 'Scientific Director',
        'membre' => 'Team Member', 'beneficiaire' => 'Beneficiary', 'autre' => 'Other',
    ];
    $devise_symbols = [ 'CAD' => '$', 'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'XOF' => 'FCFA', 'autre' => '' ];
    $modalite_labels = [
        'ponctuel' => '', 'par_an' => '/ year', 'par_mois' => '/ month',
        'par_session' => '/ session', 'par_semestre' => '/ semester',
        'total' => 'total', 'renouvelable' => 'renewable',
        'degressif' => 'decreasing', 'variable' => 'variable',
        'en_nature' => 'in-kind', 'exoneration' => 'waiver', 'autre' => '',
    ];

    // Partner info
    $partenaire_name = '';
    $partenaire_logo = '';
    $partenaire_initiales = '';
    $partenaire_url = '';
    if ( $f_partenaire_id ) {
        $partenaire_name = get_the_title( $f_partenaire_id );
        $partenaire_url  = get_post_meta( $f_partenaire_id, '_partenaire_url', true );
        if ( has_post_thumbnail( $f_partenaire_id ) ) {
            $partenaire_logo = get_the_post_thumbnail( $f_partenaire_id, 'partner-logo', [ 'class' => 'financement-single-partner-logo', 'alt' => esc_attr( $partenaire_name ), 'loading' => 'lazy' ] );
        } else {
            $partenaire_initiales = get_post_meta( $f_partenaire_id, '_partenaire_initiales', true ) ?: mb_substr( $partenaire_name, 0, 2 );
        }
    }

    // Format amount
    $montant_display = '';
    if ( $f_montant ) {
        $sym = $devise_symbols[ $f_devise ] ?? '';
        $mod = $modalite_labels[ $f_modalite ] ?? '';
        $montant_display = $f_montant . ( $sym ? ' ' . $sym : '' ) . ( $mod ? ' ' . $mod : '' );
    }

    // Linked projects
    $linked_projets = [];
    $projets_q = new WP_Query([ 'post_type' => 'projet', 'posts_per_page' => -1, 'post_status' => 'publish' ]);
    if ( $projets_q->have_posts() ) {
        while ( $projets_q->have_posts() ) {
            $projets_q->the_post();
            $proj_fins = get_post_meta( get_the_ID(), '_projet_financements', true );
            if ( is_array( $proj_fins ) && in_array( $post->ID, array_map( 'absint', $proj_fins ) ) ) {
                $linked_projets[] = get_the_ID();
            }
        }
        wp_reset_postdata();
    }
?>

<section class="single-financement-hero">
    <div class="section-wrap">
        <a href="<?php echo esc_url( home_url( '/#financements' ) ); ?>" class="back-link">&larr; Back to funding</a>

        <div class="financement-single-header">
            <span class="financement-type financement-type--<?php echo esc_attr( $f_type ); ?>">
                <?php echo esc_html( $type_labels[ $f_type ] ?? 'Funding' ); ?>
            </span>
            <?php if ( $f_statut ) : ?>
            <span class="financement-statut financement-statut--<?php echo esc_attr( $f_statut ); ?>">
                <?php echo esc_html( $statut_labels[ $f_statut ] ?? '' ); ?>
            </span>
            <?php endif; ?>
            <?php if ( $f_periode ) : ?>
            <span class="financement-single-periode">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <?php echo esc_html( $f_periode ); ?>
            </span>
            <?php endif; ?>
        </div>

        <h1 class="section-title"><?php the_title(); ?></h1>

        <?php if ( $partenaire_name ) : ?>
        <div class="financement-single-partner">
            <?php if ( $partenaire_logo ) : ?>
                <?php echo $partenaire_logo; ?>
            <?php elseif ( $partenaire_initiales ) : ?>
                <span class="financement-single-partner-initials"><?php echo esc_html( $partenaire_initiales ); ?></span>
            <?php endif; ?>
            <span class="financement-single-partner-name">
                <?php if ( $partenaire_url ) : ?>
                    <a href="<?php echo esc_url( $partenaire_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $partenaire_name ); ?></a>
                <?php else : ?>
                    <?php echo esc_html( $partenaire_name ); ?>
                <?php endif; ?>
            </span>
        </div>
        <?php endif; ?>

        <?php if ( $f_url ) : ?>
        <a href="<?php echo esc_url( $f_url ); ?>" class="btn-primary financement-single-cta" target="_blank" rel="noopener noreferrer">
            View funding program &rarr;
        </a>
        <?php endif; ?>
    </div>
</section>

<article class="single-financement-content">
    <div class="section-wrap">

        <?php if ( $montant_display || $f_role || $f_beneficiaire ) : ?>
        <div class="financement-single-details">
            <?php if ( $montant_display ) : ?>
            <div class="financement-single-detail-card">
                <div class="financement-single-detail-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <div>
                    <div class="financement-single-detail-label">Amount</div>
                    <div class="financement-single-detail-value"><?php echo esc_html( $montant_display ); ?></div>
                </div>
            </div>
            <?php endif; ?>
            <?php if ( $f_role && isset( $role_labels[ $f_role ] ) ) : ?>
            <div class="financement-single-detail-card">
                <div class="financement-single-detail-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div>
                    <div class="financement-single-detail-label">Role</div>
                    <div class="financement-single-detail-value"><?php echo esc_html( $role_labels[ $f_role ] ); ?></div>
                </div>
            </div>
            <?php endif; ?>
            <?php if ( $f_beneficiaire ) : ?>
            <div class="financement-single-detail-card">
                <div class="financement-single-detail-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                </div>
                <div>
                    <div class="financement-single-detail-label">Beneficiary</div>
                    <div class="financement-single-detail-value"><?php echo esc_html( $f_beneficiaire ); ?></div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ( $f_description ) : ?>
        <div class="financement-single-description">
            <h2>Description</h2>
            <p><?php echo nl2br( esc_html( $f_description ) ); ?></p>
        </div>
        <?php endif; ?>

        <?php if ( get_the_content() ) : ?>
        <div class="financement-single-body">
            <?php the_content(); ?>
        </div>
        <?php endif; ?>

        <?php if ( ! empty( $linked_projets ) ) : ?>
        <div class="projet-section">
            <h2>Related Projects</h2>
            <div class="research-grid">
                <?php $i = 1; foreach ( $linked_projets as $proj_id ) : ?>
                <a class="research-card" href="<?php echo get_permalink( $proj_id ); ?>">
                    <div class="research-num"><?php echo str_pad( $i, 2, '0', STR_PAD_LEFT ); ?></div>
                    <span class="research-icon"><?php
                        $p_icon = deladem_render_svg_icon( get_post_meta( $proj_id, '_projet_icon_id', true ), 24 );
                        echo $p_icon ?: deladem_default_projet_icon();
                    ?></span>
                    <h3><?php echo esc_html( get_the_title( $proj_id ) ); ?></h3>
                    <?php deladem_render_projet_tags( $proj_id ); ?>
                </a>
                <?php $i++; endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</article>

<?php endwhile; ?>

<?php get_footer(); ?>
