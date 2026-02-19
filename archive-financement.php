<?php get_header(); ?>

<?php
$devise_symbols = [ 'CAD' => '$', 'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'XOF' => 'FCFA', 'autre' => '' ];
$modalite_labels = [
    'ponctuel' => '', 'par_an' => '/ year', 'par_mois' => '/ month',
    'par_session' => '/ session', 'par_semestre' => '/ semester',
    'total' => 'total', 'renouvelable' => 'renewable',
    'degressif' => 'decreasing', 'variable' => 'variable',
    'en_nature' => 'in-kind', 'exoneration' => 'waiver', 'autre' => '',
];
$type_labels   = [ 'bourse' => 'Scholarship', 'financement' => 'Funding', 'subvention' => 'Grant', 'contrat' => 'Contract', 'prix' => 'Award' ];
$statut_labels = [ 'actif' => 'Active', 'termine' => 'Completed', 'en_attente' => 'Pending', 'refuse' => 'Declined', 'suspendu' => 'Suspended' ];
$role_labels   = [
    'chercheur_principal' => 'Principal Investigator', 'co_chercheur' => 'Co-Investigator',
    'collaborateur' => 'Collaborator', 'stagiaire' => 'Intern',
    'postdoc' => 'Postdoctoral Researcher', 'assistant' => 'Research Assistant',
    'coordonnateur' => 'Coordinator', 'responsable' => 'Scientific Director',
    'membre' => 'Team Member', 'beneficiaire' => 'Beneficiary', 'autre' => 'Other',
];
?>

<section class="archive-hero">
    <div class="section-wrap">
        <a href="<?php echo esc_url( home_url( '/#financements' ) ); ?>" class="back-link">&larr; Back to home</a>
        <p class="section-label">Funding</p>
        <h1 class="section-title">All Research Funding &amp; Awards</h1>
        <p class="section-sub">Scholarships, grants and institutional support.</p>
    </div>
</section>

<section class="archive-content">
    <div class="section-wrap">
        <?php if ( have_posts() ) : ?>
        <div class="financement-grid">
            <?php while ( have_posts() ) : the_post();
                $fin_pid = get_the_ID();
                $f_partenaire_id = get_post_meta( $fin_pid, '_financement_partenaire',   true );
                $f_type          = get_post_meta( $fin_pid, '_financement_type',         true );
                $f_montant       = get_post_meta( $fin_pid, '_financement_montant',      true );
                $f_devise        = get_post_meta( $fin_pid, '_financement_devise',       true );
                $f_modalite      = get_post_meta( $fin_pid, '_financement_modalite',     true );
                $f_statut        = get_post_meta( $fin_pid, '_financement_statut',       true );
                $f_role          = get_post_meta( $fin_pid, '_financement_role',          true );
                $f_beneficiaire  = get_post_meta( $fin_pid, '_financement_beneficiaire', true );
                $f_periode       = get_post_meta( $fin_pid, '_financement_periode',      true );
                $f_description   = get_post_meta( $fin_pid, '_financement_description',  true );

                $partenaire_name = '';
                $partenaire_logo = '';
                $partenaire_initiales = '';
                if ( $f_partenaire_id ) {
                    $partenaire_name = get_the_title( $f_partenaire_id );
                    if ( has_post_thumbnail( $f_partenaire_id ) ) {
                        $partenaire_logo = get_the_post_thumbnail( $f_partenaire_id, 'partner-logo', [ 'class' => 'financement-partner-logo', 'alt' => esc_attr( $partenaire_name ), 'loading' => 'lazy' ] );
                    } else {
                        $partenaire_initiales = get_post_meta( $f_partenaire_id, '_partenaire_initiales', true ) ?: mb_substr( $partenaire_name, 0, 2 );
                    }
                }

                $montant_display = '';
                if ( $f_montant ) {
                    $sym = $devise_symbols[ $f_devise ] ?? '';
                    $mod = $modalite_labels[ $f_modalite ] ?? '';
                    $montant_display = $f_montant . ( $sym ? ' ' . $sym : '' ) . ( $mod ? ' ' . $mod : '' );
                }
            ?>
            <a class="financement-card fade-up" href="<?php the_permalink(); ?>">
                <div class="financement-header">
                    <span class="financement-type financement-type--<?php echo esc_attr( $f_type ); ?>"><?php echo esc_html( $type_labels[ $f_type ] ?? 'Funding' ); ?></span>
                    <div class="financement-header-right">
                        <?php if ( $f_statut && $f_statut !== 'actif' ) : ?>
                        <span class="financement-statut financement-statut--<?php echo esc_attr( $f_statut ); ?>"><?php echo esc_html( $statut_labels[ $f_statut ] ?? '' ); ?></span>
                        <?php endif; ?>
                        <?php if ( $f_periode ) : ?>
                        <span class="financement-periode"><?php echo esc_html( $f_periode ); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <h3 class="financement-title"><?php the_title(); ?></h3>

                <?php if ( $partenaire_name ) : ?>
                <div class="financement-partner">
                    <?php if ( $partenaire_logo ) : ?>
                        <?php echo $partenaire_logo; ?>
                    <?php elseif ( $partenaire_initiales ) : ?>
                        <span class="financement-partner-initials"><?php echo esc_html( $partenaire_initiales ); ?></span>
                    <?php endif; ?>
                    <span class="financement-partner-name"><?php echo esc_html( $partenaire_name ); ?></span>
                </div>
                <?php endif; ?>

                <div class="financement-details">
                    <?php if ( $montant_display ) : ?>
                    <div class="financement-detail">
                        <span class="financement-detail-label">Amount</span>
                        <span class="financement-detail-value"><?php echo esc_html( $montant_display ); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ( $f_role && isset( $role_labels[ $f_role ] ) ) : ?>
                    <div class="financement-detail">
                        <span class="financement-detail-label">Role</span>
                        <span class="financement-detail-value"><?php echo esc_html( $role_labels[ $f_role ] ); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ( $f_beneficiaire ) : ?>
                    <div class="financement-detail">
                        <span class="financement-detail-label">Beneficiary</span>
                        <span class="financement-detail-value"><?php echo esc_html( $f_beneficiaire ); ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if ( $f_description ) : ?>
                <p class="financement-desc"><?php echo esc_html( $f_description ); ?></p>
                <?php endif; ?>
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
            <p><strong>No funding entries yet.</strong></p>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
