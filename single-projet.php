<?php get_header(); ?>

<?php while ( have_posts() ) : the_post();
    $tags  = get_post_meta( get_the_ID(), '_projet_tags', true );
    $projet_periode = get_post_meta( get_the_ID(), '_projet_periode', true );
?>

<section class="single-projet-hero">
    <div class="section-wrap">
        <a href="<?php echo esc_url( home_url( '/#research' ) ); ?>" class="back-link">&larr; Back to projects</a>
        <span class="projet-icon-large"><?php
            $p_icon = deladem_render_svg_icon( get_post_meta( get_the_ID(), '_projet_icon_id', true ), 48 );
            echo $p_icon ?: deladem_default_projet_icon( 48 );
        ?></span>
        <h1 class="section-title"><?php the_title(); ?></h1>
        <?php if ( $tags ) : ?>
        <div class="research-tags research-tags--hero">
            <?php foreach ( array_map( 'trim', explode( ',', $tags ) ) as $t ) :
                if ( $t ) : ?>
                <span class="tag"><?php echo esc_html( $t ); ?></span>
            <?php endif; endforeach; ?>
        </div>
        <?php endif; ?>
        <?php if ( $projet_periode ) : ?>
        <div class="projet-periode">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <?php echo esc_html( $projet_periode ); ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<article class="single-projet-content">
    <div class="section-wrap">
        <?php if ( has_post_thumbnail() ) : ?>
        <div class="projet-thumbnail">
            <?php the_post_thumbnail( 'large', [ 'class' => 'projet-featured-img', 'alt' => esc_attr( get_the_title() ) ] ); ?>
        </div>
        <?php endif; ?>

        <div class="projet-body">
            <?php the_content(); ?>

            <?php
            // Linked Financements
            $linked_fin = get_post_meta( get_the_ID(), '_projet_financements', true );
            if ( is_array( $linked_fin ) && ! empty( $linked_fin ) ) :
                $type_labels = [ 'bourse' => 'Scholarship', 'financement' => 'Funding', 'subvention' => 'Grant', 'contrat' => 'Contract', 'prix' => 'Award' ];
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
            ?>
            <div class="projet-section">
                <h2>Funding</h2>
                <div class="projet-financements-list">
                    <?php foreach ( $linked_fin as $fin_id ) :
                        $fin_id = absint( $fin_id );
                        if ( get_post_status( $fin_id ) !== 'publish' ) continue;
                        $f_type     = get_post_meta( $fin_id, '_financement_type', true );
                        $f_montant  = get_post_meta( $fin_id, '_financement_montant', true );
                        $f_devise   = get_post_meta( $fin_id, '_financement_devise', true );
                        $f_modalite = get_post_meta( $fin_id, '_financement_modalite', true );
                        $f_periode  = get_post_meta( $fin_id, '_financement_periode', true );
                        $f_role     = get_post_meta( $fin_id, '_financement_role', true );
                        $f_part_id  = get_post_meta( $fin_id, '_financement_partenaire', true );
                        $f_part_name = $f_part_id ? get_the_title( $f_part_id ) : '';

                        $montant_display = '';
                        if ( $f_montant ) {
                            $sym = $devise_symbols[ $f_devise ] ?? '';
                            $mod = $modalite_labels[ $f_modalite ] ?? '';
                            $montant_display = $f_montant . ( $sym ? ' ' . $sym : '' ) . ( $mod ? ' ' . $mod : '' );
                        }
                    ?>
                    <div class="projet-financement-item">
                        <div class="projet-financement-info">
                            <div class="projet-financement-title-row">
                                <strong><?php echo esc_html( get_the_title( $fin_id ) ); ?></strong>
                                <span class="financement-type financement-type--<?php echo esc_attr( $f_type ); ?>"><?php echo esc_html( $type_labels[ $f_type ] ?? 'Funding' ); ?></span>
                            </div>
                            <?php if ( $f_part_name ) : ?>
                                <span class="projet-financement-partner"><?php echo esc_html( $f_part_name ); ?></span>
                            <?php endif; ?>
                            <?php if ( $f_role && isset( $role_labels[ $f_role ] ) ) : ?>
                                <span class="projet-financement-role"><?php echo esc_html( $role_labels[ $f_role ] ); ?></span>
                            <?php endif; ?>
                            <?php if ( $montant_display ) : ?>
                                <span class="projet-financement-montant"><?php echo esc_html( $montant_display ); ?></span>
                            <?php endif; ?>
                            <?php if ( $f_periode ) : ?>
                                <span class="projet-financement-periode"><?php echo esc_html( $f_periode ); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php
            // Linked Partenaires/Collaborateurs
            $linked_part = get_post_meta( get_the_ID(), '_projet_partenaires', true );
            if ( is_array( $linked_part ) && ! empty( $linked_part ) ) :
            ?>
            <div class="projet-section">
                <h2>Partners &amp; Collaborators</h2>
                <div class="projet-partenaires-grid">
                    <?php foreach ( $linked_part as $part_id ) :
                        $part_id = absint( $part_id );
                        if ( get_post_status( $part_id ) !== 'publish' ) continue;
                        $part_url  = get_post_meta( $part_id, '_partenaire_url', true );
                        $part_init = get_post_meta( $part_id, '_partenaire_initiales', true );
                        $tag_open  = $part_url ? '<a href="' . esc_url( $part_url ) . '" target="_blank" rel="noopener noreferrer" class="projet-partenaire-item">' : '<div class="projet-partenaire-item">';
                        $tag_close = $part_url ? '</a>' : '</div>';
                    ?>
                    <?php echo $tag_open; ?>
                        <?php if ( has_post_thumbnail( $part_id ) ) : ?>
                            <?php echo get_the_post_thumbnail( $part_id, 'partner-logo', [ 'class' => 'projet-partenaire-logo', 'alt' => esc_attr( get_the_title( $part_id ) ), 'loading' => 'lazy' ] ); ?>
                        <?php else : ?>
                            <span class="projet-partenaire-initials"><?php echo esc_html( $part_init ?: mb_substr( get_the_title( $part_id ), 0, 2 ) ); ?></span>
                        <?php endif; ?>
                        <span class="projet-partenaire-name"><?php echo esc_html( get_the_title( $part_id ) ); ?></span>
                    <?php echo $tag_close; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</article>

<?php endwhile; ?>

<?php get_footer(); ?>
