<?php
/**
 * Deladem IHM — Seeder de données fictives
 *
 * Remplit le thème avec un contenu de démonstration complet
 * lors de la première activation. Exécuté une seule fois.
 *
 * @package Deladem_IHM
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Vérifie si un post existe déjà par titre et type (compatible WP 6.2+).
 */
function deladem_post_exists( $title, $post_type ) {
    $q = new WP_Query( [
        'post_type'      => $post_type,
        'title'          => $title,
        'posts_per_page' => 1,
        'post_status'    => 'any',
        'fields'         => 'ids',
    ] );
    return $q->have_posts();
}

/**
 * Vérifie si le seeding a déjà été fait et l'exécute sinon.
 */
function deladem_maybe_seed() {
    if ( get_option( 'dlm_seeded' ) ) {
        return;
    }

    deladem_seed_options();
    deladem_seed_projets();
    deladem_seed_publications();
    deladem_seed_partenaires();
    deladem_seed_cv();

    update_option( 'dlm_seeded', true );
}

/**
 * Seed : options du thème (hero, about, contact, footer, badges, stats, intérêts).
 */
function deladem_seed_options() {

    $options = [
        // Hero
        'hero_etiquette'      => 'Doctorant en Informatique',
        'hero_titre_ligne1'   => 'Chercheur en',
        'hero_titre_em'       => 'Interaction Humain–Machine',
        'hero_titre_ligne3'   => '',
        'hero_description'    => 'Je conçois des systèmes d\'acquisition de données physiologiques multi-capteurs pour comprendre comment les humains interagissent avec les technologies numériques.',
        'hero_btn1_label'     => 'Mes recherches →',
        'hero_btn1_url'       => '#research',
        'hero_btn2_label'     => 'Publications',
        'hero_btn2_url'       => '#publications',

        // Stats
        'hero_stat1_num'      => '4+',
        'hero_stat1_label'    => 'Capteurs intégrés',
        'hero_stat2_num'      => 'LSL',
        'hero_stat2_label'    => 'Synchronisation',
        'hero_stat3_num'      => 'VR',
        'hero_stat3_label'    => 'Unity / XR',
        'hero_stat4_num'      => 'PhD',
        'hero_stat4_label'    => 'UQAC · LRIT',

        // About
        'about_titre'         => 'Comprendre l\'humain par les données',
        'about_sous_titre'    => '',
        'about_texte'         => '<strong>Passionné par l\'intersection entre technologie et cognition humaine</strong>, je mène des recherches sur l\'acquisition et l\'analyse de données physiologiques multi-capteurs.<br><br>Mon travail vise à développer des systèmes innovants qui capturent simultanément les signaux EEG, ECG, le suivi oculaire et l\'imagerie thermique pour mieux comprendre l\'expérience utilisateur en réalité virtuelle.<br><br>Je m\'intéresse particulièrement à la <strong>synchronisation temporelle</strong> des flux de données hétérogènes via le protocole <strong>Lab Streaming Layer (LSL)</strong>, permettant une analyse multimodale précise des interactions humain-machine.',

        // Info sidebar
        'info_institution'    => 'Université du Québec à Chicoutimi (UQAC)',
        'info_localisation'   => 'Québec, Canada',
        'info_langues'        => 'Français · Anglais',

        // Contact
        'contact_email'           => 'chercheur@example.uqac.ca',
        'contact_github'          => 'https://github.com/deladem-ihm',
        'contact_linkedin'        => 'https://linkedin.com/in/deladem-ihm',
        'contact_institution_url' => 'https://uqac.ca',

        // Partners
        'partners_label'      => 'Collaborations',
        'partners_titre'      => 'Entreprises & institutions partenaires',

        // Footer
        'footer_texte'        => '© ' . date( 'Y' ) . ' Deladem — Chercheur en IHM',
        'footer_mention'      => 'Construit avec passion · Québec, Canada',
    ];

    foreach ( $options as $key => $value ) {
        if ( ! get_option( 'dlm_' . $key ) ) {
            update_option( 'dlm_' . $key, $value );
        }
    }

    // Badges
    if ( ! get_option( 'dlm_hero_badges' ) ) {
        update_option( 'dlm_hero_badges', [
            'EEG · Neurosity Crown',
            'ECG · Polar H10',
            'Eye Tracking · Tobii Pro',
            'Thermal Camera · FLIR',
            'Motion Capture · Xsens',
            'VR · Meta Quest 3',
        ] );
    }

    // Directeurs
    if ( ! get_option( 'dlm_info_directeurs' ) ) {
        update_option( 'dlm_info_directeurs', [
            'Prof. Bob-Antoine Jerry Ménélas',
            'Prof. Hamid Mcheick',
        ] );
    }

    // Labos
    if ( ! get_option( 'dlm_info_labos' ) ) {
        update_option( 'dlm_info_labos', [
            'Laboratoire de Recherche en Interfaces & Technologies (LRIT)',
            'Groupe de Recherche en Informatique de l\'UQAC (GRI)',
        ] );
    }

    // Intérêts de recherche
    if ( ! get_option( 'dlm_interets' ) ) {
        update_option( 'dlm_interets', [
            [
                'titre' => 'Acquisition multi-capteurs',
                'desc'  => 'Développement de pipelines d\'acquisition synchronisée pour EEG, ECG, eye-tracking et imagerie thermique via Lab Streaming Layer (LSL).',
                'url'   => 'https://labstreaminglayer.org/',
            ],
            [
                'titre' => 'Réalité virtuelle & immersion',
                'desc'  => 'Conception d\'environnements VR avec Unity/XR pour l\'étude des réponses physiologiques en contexte immersif.',
                'url'   => 'https://unity.com/solutions/xr',
            ],
            [
                'titre' => 'Analyse de données physiologiques',
                'desc'  => 'Traitement et visualisation de signaux multimodaux : filtrage, segmentation, extraction de features et machine learning.',
                'url'   => '',
            ],
            [
                'titre' => 'Expérience utilisateur (UX)',
                'desc'  => 'Évaluation objective de l\'UX par mesures physiologiques : charge cognitive, stress, engagement et attention visuelle.',
                'url'   => '',
            ],
            [
                'titre' => 'Interfaces cerveau-ordinateur (BCI)',
                'desc'  => 'Exploration des paradigmes BCI passifs pour la détection d\'états mentaux en temps réel lors d\'interactions numériques.',
                'url'   => 'https://openbci.com/',
            ],
        ] );
    }
}

/**
 * Seed : projets de recherche.
 */
function deladem_seed_projets() {
    $projets = [
        [
            'title'   => 'Plateforme multi-capteurs LSL',
            'excerpt' => 'Architecture logicielle pour l\'acquisition synchronisée de données EEG, ECG, eye-tracking et thermiques en temps réel.',
            'content' => '<h2>Contexte</h2>
<p>Ce projet vise à créer une plateforme unifiée permettant l\'acquisition simultanée de multiples flux de données physiologiques. En utilisant le protocole Lab Streaming Layer (LSL), nous synchronisons les données avec une précision temporelle sub-milliseconde.</p>

<h2>Objectifs</h2>
<ul>
<li>Intégrer 4+ capteurs physiologiques dans un pipeline unifié</li>
<li>Garantir une synchronisation temporelle inférieure à 1ms</li>
<li>Développer une interface de monitoring en temps réel</li>
<li>Fournir des outils d\'export pour l\'analyse post-hoc</li>
</ul>

<h2>Technologies utilisées</h2>
<p>Python, Lab Streaming Layer (LSL), Qt/PySide6 pour l\'interface, MNE-Python pour le traitement EEG, et InfluxDB pour le stockage temporel.</p>',
            'tags'    => 'Python, LSL, EEG, ECG, Eye-Tracking, Temps réel',
            'ordre'   => 1,
        ],
        [
            'title'   => 'Environnements VR adaptatifs',
            'excerpt' => 'Développement d\'environnements de réalité virtuelle qui s\'adaptent en temps réel aux états physiologiques de l\'utilisateur.',
            'content' => '<h2>Contexte</h2>
<p>Ce projet explore la création d\'environnements immersifs capables de modifier dynamiquement leur complexité, éclairage et interactions en fonction des mesures physiologiques de l\'utilisateur (stress, attention, fatigue).</p>

<h2>Approche</h2>
<p>Nous utilisons Unity avec le framework XR Interaction Toolkit, couplé à notre plateforme LSL pour recevoir les données physiologiques en temps réel. Un module d\'inférence basé sur des modèles de machine learning classifie l\'état cognitif et déclenche des adaptations environnementales.</p>

<h2>Résultats préliminaires</h2>
<p>Les premiers tests montrent une réduction significative de la surcharge cognitive (-23%) et une amélioration de l\'engagement (+18%) lorsque l\'environnement s\'adapte aux états détectés.</p>',
            'tags'    => 'Unity, XR, VR, Physiologie, Machine Learning',
            'ordre'   => 2,
        ],
        [
            'title'   => 'Charge cognitive en contexte immersif',
            'excerpt' => 'Étude de la charge cognitive lors d\'interactions en réalité virtuelle à l\'aide de mesures EEG et oculométriques.',
            'content' => '<h2>Description</h2>
<p>Cette recherche combine l\'électroencéphalographie (EEG) et le suivi oculaire pour quantifier la charge cognitive lors de tâches d\'interaction en réalité virtuelle. Nous développons des indices composites multimodaux permettant une évaluation continue et non-invasive de l\'effort mental.</p>

<h2>Méthodologie</h2>
<p>Protocole expérimental avec 30 participants, tâches de complexité croissante en VR, acquisition simultanée EEG (32 canaux) + eye-tracking (120 Hz). Analyse par régression linéaire mixte et classification par SVM.</p>',
            'tags'    => 'EEG, Eye-Tracking, Charge cognitive, VR, Statistiques',
            'ordre'   => 3,
        ],
        [
            'title'   => 'Détection de stress par thermographie',
            'excerpt' => 'Utilisation de caméras thermiques infrarouges pour la détection non-invasive du stress lors d\'interactions numériques.',
            'content' => '<h2>Objectif</h2>
<p>Développer un système de détection du stress basé sur la thermographie faciale. Les variations de température au niveau du nez, du front et de la région péri-orbitale sont des marqueurs fiables de l\'activation du système nerveux sympathique.</p>

<h2>Innovation</h2>
<p>Notre approche combine la thermographie avec des mesures ECG (variabilité de fréquence cardiaque) pour créer un indice de stress multimodal robuste, applicable en conditions écologiques.</p>',
            'tags'    => 'Thermographie, FLIR, ECG, HRV, Stress, OpenCV',
            'ordre'   => 4,
        ],
    ];

    foreach ( $projets as $p ) {
        if ( deladem_post_exists( $p['title'], 'projet' ) ) {
            continue;
        }
        $id = wp_insert_post( [
            'post_type'    => 'projet',
            'post_title'   => $p['title'],
            'post_excerpt' => $p['excerpt'],
            'post_content' => $p['content'],
            'post_status'  => 'publish',
        ] );
        if ( $id && ! is_wp_error( $id ) ) {
            update_post_meta( $id, '_projet_tags',  $p['tags'] );
            update_post_meta( $id, '_projet_ordre', $p['ordre'] );
        }
    }
}

/**
 * Seed : publications académiques.
 */
function deladem_seed_publications() {
    $publications = [
        [
            'title'   => 'A Multi-Sensor Framework for Real-Time Physiological Data Acquisition in VR Environments',
            'annee'   => 2025,
            'auteurs' => 'Deladem K., Ménélas B.-A. J.',
            'revue'   => 'ACM Conference on Human Factors in Computing Systems (CHI 2025)',
            'type'    => 'conference',
            'doi'     => 'https://doi.org/10.1145/example.chi2025',
        ],
        [
            'title'   => 'Synchronization Challenges in Multi-Modal Physiological Data Streams Using LSL',
            'annee'   => 2024,
            'auteurs' => 'Deladem K., Ménélas B.-A. J., Mcheick H.',
            'revue'   => 'IEEE Transactions on Biomedical Engineering',
            'type'    => 'journal',
            'doi'     => 'https://doi.org/10.1109/example.tbme2024',
        ],
        [
            'title'   => 'Assessing Cognitive Load in Virtual Reality Through EEG and Eye-Tracking Fusion',
            'annee'   => 2024,
            'auteurs' => 'Deladem K., Ménélas B.-A. J.',
            'revue'   => 'International Conference on Human-Computer Interaction (INTERACT 2024)',
            'type'    => 'conference',
            'doi'     => 'https://doi.org/10.1007/example.interact2024',
        ],
        [
            'title'   => 'Thermal Imaging for Non-Invasive Stress Detection During Digital Interactions',
            'annee'   => 2023,
            'auteurs' => 'Deladem K., Ménélas B.-A. J.',
            'revue'   => 'Workshop on Physiological Computing, ACM UIST 2023',
            'type'    => 'workshop',
            'doi'     => '',
        ],
        [
            'title'   => 'État de l\'art : systèmes multi-capteurs pour l\'évaluation de l\'expérience utilisateur',
            'annee'   => 2023,
            'auteurs' => 'Deladem K.',
            'revue'   => 'Rapport de recherche, UQAC',
            'type'    => 'rapport',
            'doi'     => '',
        ],
        [
            'title'   => 'Conception d\'une plateforme d\'acquisition multimodale pour l\'étude de l\'IHM en réalité virtuelle',
            'annee'   => 2026,
            'auteurs' => 'Deladem K.',
            'revue'   => 'Thèse de doctorat, Université du Québec à Chicoutimi',
            'type'    => 'these',
            'doi'     => '',
        ],
    ];

    foreach ( $publications as $pub ) {
        if ( deladem_post_exists( $pub['title'], 'publication' ) ) {
            continue;
        }
        $id = wp_insert_post( [
            'post_type'   => 'publication',
            'post_title'  => $pub['title'],
            'post_status' => 'publish',
        ] );
        if ( $id && ! is_wp_error( $id ) ) {
            update_post_meta( $id, '_pub_annee',   $pub['annee'] );
            update_post_meta( $id, '_pub_auteurs', $pub['auteurs'] );
            update_post_meta( $id, '_pub_revue',   $pub['revue'] );
            update_post_meta( $id, '_pub_type',    $pub['type'] );
            update_post_meta( $id, '_pub_doi',     $pub['doi'] );
        }
    }
}

/**
 * Seed : partenaires (sans logo, avec initiales).
 */
function deladem_seed_partenaires() {
    $partenaires = [
        [
            'title'     => 'UQAC',
            'type'      => 'institution',
            'initiales' => 'UQ',
            'url'       => 'https://uqac.ca',
            'ordre'     => 1,
        ],
        [
            'title'     => 'Neurosity',
            'type'      => 'entreprise',
            'initiales' => 'NR',
            'url'       => 'https://neurosity.co',
            'ordre'     => 2,
        ],
        [
            'title'     => 'Tobii',
            'type'      => 'entreprise',
            'initiales' => 'TB',
            'url'       => 'https://tobii.com',
            'ordre'     => 3,
        ],
        [
            'title'     => 'MITACS',
            'type'      => 'institution',
            'initiales' => 'MT',
            'url'       => 'https://mitacs.ca',
            'ordre'     => 4,
        ],
        [
            'title'     => 'FRQNT',
            'type'      => 'institution',
            'initiales' => 'FQ',
            'url'       => 'https://frq.gouv.qc.ca',
            'ordre'     => 5,
        ],
    ];

    foreach ( $partenaires as $p ) {
        if ( deladem_post_exists( $p['title'], 'partenaire' ) ) {
            continue;
        }
        $id = wp_insert_post( [
            'post_type'   => 'partenaire',
            'post_title'  => $p['title'],
            'post_status' => 'publish',
        ] );
        if ( $id && ! is_wp_error( $id ) ) {
            update_post_meta( $id, '_partenaire_type',      $p['type'] );
            update_post_meta( $id, '_partenaire_initiales', $p['initiales'] );
            update_post_meta( $id, '_partenaire_url',       $p['url'] );
            update_post_meta( $id, '_partenaire_ordre',     $p['ordre'] );
        }
    }
}

/**
 * Seed : entrées CV (formations, expériences, compétences).
 */
function deladem_seed_cv() {

    // Formations
    $formations = [
        [
            'title'         => 'Doctorat en Informatique',
            'periode'       => '2022 — En cours',
            'etablissement' => 'UQAC, Chicoutimi QC',
            'ordre'         => 1,
        ],
        [
            'title'         => 'Maîtrise en Informatique',
            'periode'       => '2019 — 2022',
            'etablissement' => 'UQAC, Chicoutimi QC',
            'ordre'         => 2,
        ],
        [
            'title'         => 'Baccalauréat en Génie logiciel',
            'periode'       => '2015 — 2019',
            'etablissement' => 'Université de Lomé, Togo',
            'ordre'         => 3,
        ],
    ];

    foreach ( $formations as $f ) {
        if ( deladem_post_exists( $f['title'], 'cv_entree' ) ) {
            continue;
        }
        $id = wp_insert_post( [
            'post_type'   => 'cv_entree',
            'post_title'  => $f['title'],
            'post_status' => 'publish',
        ] );
        if ( $id && ! is_wp_error( $id ) ) {
            update_post_meta( $id, '_cv_categorie',     'formation' );
            update_post_meta( $id, '_cv_periode',       $f['periode'] );
            update_post_meta( $id, '_cv_etablissement', $f['etablissement'] );
            update_post_meta( $id, '_cv_ordre',         $f['ordre'] );
        }
    }

    // Expériences
    $experiences = [
        [
            'title'         => 'Assistant de recherche — LRIT',
            'periode'       => '2022 — En cours',
            'etablissement' => 'UQAC, Chicoutimi QC',
            'ordre'         => 1,
        ],
        [
            'title'         => 'Chargé de cours — Introduction à la programmation',
            'periode'       => '2023 — 2024',
            'etablissement' => 'UQAC, Département d\'informatique',
            'ordre'         => 2,
        ],
        [
            'title'         => 'Développeur Full-Stack (Stage)',
            'periode'       => '2021 — 2022',
            'etablissement' => 'Tech Solutions Inc., Montréal QC',
            'ordre'         => 3,
        ],
        [
            'title'         => 'Consultant IT Freelance',
            'periode'       => '2019 — 2021',
            'etablissement' => 'Clients divers, Togo & Canada',
            'ordre'         => 4,
        ],
    ];

    foreach ( $experiences as $e ) {
        if ( deladem_post_exists( $e['title'], 'cv_entree' ) ) {
            continue;
        }
        $id = wp_insert_post( [
            'post_type'   => 'cv_entree',
            'post_title'  => $e['title'],
            'post_status' => 'publish',
        ] );
        if ( $id && ! is_wp_error( $id ) ) {
            update_post_meta( $id, '_cv_categorie',     'experience' );
            update_post_meta( $id, '_cv_periode',       $e['periode'] );
            update_post_meta( $id, '_cv_etablissement', $e['etablissement'] );
            update_post_meta( $id, '_cv_ordre',         $e['ordre'] );
        }
    }

    // Compétences
    $competences = [
        'Python', 'JavaScript', 'C#', 'Unity / XR',
        'Lab Streaming Layer', 'MNE-Python', 'OpenCV',
        'EEG / BCI', 'Eye-Tracking', 'Thermographie',
        'Machine Learning', 'Statistiques', 'LaTeX',
        'Git', 'Docker', 'WordPress',
    ];

    foreach ( $competences as $idx => $comp ) {
        if ( deladem_post_exists( $comp, 'cv_entree' ) ) {
            continue;
        }
        $id = wp_insert_post( [
            'post_type'   => 'cv_entree',
            'post_title'  => $comp,
            'post_status' => 'publish',
        ] );
        if ( $id && ! is_wp_error( $id ) ) {
            update_post_meta( $id, '_cv_categorie', 'competence' );
            update_post_meta( $id, '_cv_ordre',     $idx + 1 );
        }
    }
}
