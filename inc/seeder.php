<?php
/**
 * Deladem IHM — Demo Data Seeder
 *
 * Populates the theme with complete demo content
 * on first activation. Runs only once.
 *
 * @package Deladem_IHM
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Check if a post already exists by title and type (WP 6.2+ compatible).
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
 * Check if seeding has already been done and run it if not.
 */
function deladem_maybe_seed() {
    if ( get_option( 'dlm_seeded' ) ) {
        return;
    }

    deladem_seed_options();
    deladem_seed_partenaires();
    deladem_seed_financements();
    deladem_seed_projets();
    deladem_seed_publications();
    deladem_seed_cv();

    update_option( 'dlm_seeded', true );
}

/**
 * Seed: theme options (hero, about, contact, footer, badges, stats, interests).
 */
function deladem_seed_options() {

    $options = [
        // Hero
        'hero_etiquette'      => 'PhD Student in Computer Science',
        'hero_titre_ligne1'   => 'Researcher in',
        'hero_titre_em'       => 'Human–Computer Interaction',
        'hero_titre_ligne3'   => '',
        'hero_description'    => 'I design multi-sensor physiological data acquisition systems to understand how humans interact with digital technologies.',
        'hero_btn1_label'     => 'My Research →',
        'hero_btn1_url'       => '#research',
        'hero_btn2_label'     => 'Publications',
        'hero_btn2_url'       => '#publications',

        // Stats
        'hero_stat1_num'      => '4+',
        'hero_stat1_label'    => 'Integrated Sensors',
        'hero_stat2_num'      => 'LSL',
        'hero_stat2_label'    => 'Synchronization',
        'hero_stat3_num'      => 'VR',
        'hero_stat3_label'    => 'Unity / XR',
        'hero_stat4_num'      => 'PhD',
        'hero_stat4_label'    => 'UQAC · LRIT',

        // About
        'about_titre'         => 'Understanding Humans Through Data',
        'about_description'   => 'Background, research context and academic information.',
        'about_sous_titre'    => '',
        'about_texte'         => '<strong>Passionate about the intersection of technology and human cognition</strong>, I conduct research on the acquisition and analysis of multi-sensor physiological data.<br><br>My work aims to develop innovative systems that simultaneously capture EEG, ECG, eye-tracking and thermal imaging signals to better understand the user experience in virtual reality.<br><br>I am particularly interested in <strong>temporal synchronization</strong> of heterogeneous data streams via the <strong>Lab Streaming Layer (LSL)</strong> protocol, enabling precise multimodal analysis of human-computer interactions.',

        // Info sidebar
        'info_institution'    => 'Université du Québec à Chicoutimi (UQAC)',
        'info_localisation'   => 'Quebec, Canada',
        'info_langues'        => 'French · English',

        // Contact
        'contact_email'           => 'researcher@example.uqac.ca',
        'contact_github'          => 'https://github.com/deladem-ihm',
        'contact_linkedin'        => 'https://linkedin.com/in/deladem-ihm',
        'contact_orcid'           => 'https://orcid.org/0000-0001-2345-6789',
        'contact_institution_url' => 'https://uqac.ca',
        'seo_description'         => 'PhD researcher in Human-Computer Interaction at UQAC. Exploring innovative interfaces, accessibility, and user experience in interactive systems.',

        // Partners
        'partners_label'      => 'Collaborations',
        'partners_titre'      => 'Partner Companies & Institutions',
        'partners_description' => 'Research partners and institutional collaborators.',

        // Financements
        'financements_label'       => 'Funding',
        'financements_titre'       => 'Research Funding & Awards',
        'financements_description' => 'Scholarships, grants and institutional support.',

        // Footer
        'footer_texte'        => '© ' . date( 'Y' ) . ' Deladem — HCI Researcher',
        'footer_mention'      => 'Built with passion · Quebec, Canada',
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

    // Supervisors
    if ( ! get_option( 'dlm_info_directeurs' ) ) {
        update_option( 'dlm_info_directeurs', [
            'Prof. Bob-Antoine Jerry Ménélas',
            'Prof. Hamid Mcheick',
        ] );
    }

    // Labs
    if ( ! get_option( 'dlm_info_labos' ) ) {
        update_option( 'dlm_info_labos', [
            'Interfaces & Technologies Research Lab (LRIT)',
            'UQAC Computer Science Research Group (GRI)',
        ] );
    }

    // Research interests
    if ( ! get_option( 'dlm_interets' ) ) {
        update_option( 'dlm_interets', [
            [
                'titre' => 'Multi-Sensor Acquisition',
                'desc'  => 'Development of synchronized acquisition pipelines for EEG, ECG, eye-tracking and thermal imaging via Lab Streaming Layer (LSL).',
                'url'   => 'https://labstreaminglayer.org/',
            ],
            [
                'titre' => 'Virtual Reality & Immersion',
                'desc'  => 'Design of VR environments with Unity/XR for studying physiological responses in immersive contexts.',
                'url'   => 'https://unity.com/solutions/xr',
            ],
            [
                'titre' => 'Physiological Data Analysis',
                'desc'  => 'Processing and visualization of multimodal signals: filtering, segmentation, feature extraction and machine learning.',
                'url'   => '',
            ],
            [
                'titre' => 'User Experience (UX)',
                'desc'  => 'Objective UX evaluation through physiological measures: cognitive load, stress, engagement and visual attention.',
                'url'   => '',
            ],
            [
                'titre' => 'Brain-Computer Interfaces (BCI)',
                'desc'  => 'Exploration of passive BCI paradigms for real-time mental state detection during digital interactions.',
                'url'   => 'https://openbci.com/',
            ],
        ] );
    }
}

/**
 * Seed: research projects.
 */
function deladem_seed_projets() {
    $projets = [
        [
            'title'   => 'LSL Multi-Sensor Platform',
            'excerpt' => 'Software architecture for synchronized real-time acquisition of EEG, ECG, eye-tracking and thermal data.',
            'content' => '<h2>Context</h2>
<p>This project aims to create a unified platform for simultaneous acquisition of multiple physiological data streams. Using the Lab Streaming Layer (LSL) protocol, we synchronize data with sub-millisecond temporal precision.</p>

<h2>Objectives</h2>
<ul>
<li>Integrate 4+ physiological sensors into a unified pipeline</li>
<li>Ensure temporal synchronization under 1ms</li>
<li>Develop a real-time monitoring interface</li>
<li>Provide export tools for post-hoc analysis</li>
</ul>

<h2>Technologies</h2>
<p>Python, Lab Streaming Layer (LSL), Qt/PySide6 for the interface, MNE-Python for EEG processing, and InfluxDB for time-series storage.</p>',
            'tags'    => 'Python, LSL, EEG, ECG, Eye-Tracking, Real-time',
            'ordre'   => 1,
            'periode' => '2022 — Present',
            'link_financements' => [ 'Bourse de doctorat FRQNT', 'Financement équipement de recherche' ],
            'link_partenaires'  => [ 'UQAC', 'Neurosity' ],
        ],
        [
            'title'   => 'Adaptive VR Environments',
            'excerpt' => 'Development of virtual reality environments that adapt in real-time to the user\'s physiological states.',
            'content' => '<h2>Context</h2>
<p>This project explores the creation of immersive environments capable of dynamically modifying their complexity, lighting and interactions based on the user\'s physiological measurements (stress, attention, fatigue).</p>

<h2>Approach</h2>
<p>We use Unity with the XR Interaction Toolkit framework, coupled with our LSL platform to receive physiological data in real-time. An inference module based on machine learning models classifies cognitive state and triggers environmental adaptations.</p>

<h2>Preliminary Results</h2>
<p>Initial tests show a significant reduction in cognitive overload (-23%) and improved engagement (+18%) when the environment adapts to detected states.</p>',
            'tags'    => 'Unity, XR, VR, Physiology, Machine Learning',
            'ordre'   => 2,
            'periode' => '2023 — Present',
            'link_financements' => [ 'Subvention MITACS Accelerate' ],
            'link_partenaires'  => [ 'UQAC', 'Tobii' ],
        ],
        [
            'title'   => 'Cognitive Load in Immersive Contexts',
            'excerpt' => 'Study of cognitive load during virtual reality interactions using EEG and eye-tracking measures.',
            'content' => '<h2>Description</h2>
<p>This research combines electroencephalography (EEG) and eye-tracking to quantify cognitive load during interaction tasks in virtual reality. We develop multimodal composite indices enabling continuous, non-invasive assessment of mental effort.</p>

<h2>Methodology</h2>
<p>Experimental protocol with 30 participants, tasks of increasing complexity in VR, simultaneous acquisition of EEG (32 channels) + eye-tracking (120 Hz). Analysis by mixed linear regression and SVM classification.</p>',
            'tags'    => 'EEG, Eye-Tracking, Cognitive Load, VR, Statistics',
            'ordre'   => 3,
            'periode' => '2024 — Present',
        ],
        [
            'title'   => 'Stress Detection via Thermography',
            'excerpt' => 'Using infrared thermal cameras for non-invasive stress detection during digital interactions.',
            'content' => '<h2>Objective</h2>
<p>Develop a stress detection system based on facial thermography. Temperature variations in the nose, forehead and periorbital region are reliable markers of sympathetic nervous system activation.</p>

<h2>Innovation</h2>
<p>Our approach combines thermography with ECG measurements (heart rate variability) to create a robust multimodal stress index, applicable in ecological conditions.</p>',
            'tags'    => 'Thermography, FLIR, ECG, HRV, Stress, OpenCV',
            'ordre'   => 4,
            'periode' => '2023 — 2024',
        ],
        [
            'title'   => 'Accessible VR for Motor Impairments',
            'excerpt' => 'Designing inclusive virtual reality interfaces for users with reduced motor abilities.',
            'content' => '<h2>Context</h2>
<p>Standard VR controllers require fine motor skills that exclude many users. This project proposes alternative interaction paradigms using gaze, head movement and voice commands to navigate virtual environments.</p>

<h2>Approach</h2>
<p>We combine eye-tracking with speech recognition and head-pose estimation to create a multi-channel input system. User studies with rehabilitation centers validate usability and engagement.</p>',
            'tags'    => 'Accessibility, VR, Eye-Tracking, Speech, Inclusion',
            'ordre'   => 5,
            'periode' => '2024 — Present',
        ],
        [
            'title'   => 'Real-Time EEG Artifact Rejection',
            'excerpt' => 'Machine learning pipeline for automatic removal of motion and ocular artifacts from EEG signals during VR use.',
            'content' => '<h2>Problem</h2>
<p>Head-mounted VR displays introduce significant motion artifacts into EEG recordings. Traditional offline cleaning methods are unsuitable for real-time BCI applications.</p>

<h2>Solution</h2>
<p>We develop a lightweight convolutional neural network trained on labeled artifact data that operates in real-time on streaming EEG, maintaining signal quality without introducing latency above 50ms.</p>',
            'tags'    => 'EEG, Artifact Rejection, Deep Learning, Real-time, BCI',
            'ordre'   => 6,
            'periode' => '2024 — Present',
        ],
        [
            'title'   => 'Haptic Feedback in Teleoperation',
            'excerpt' => 'Integrating force feedback into remote robotic control using physiological monitoring of operator workload.',
            'content' => '<h2>Description</h2>
<p>Teleoperation of robotic arms requires precise haptic feedback to prevent excessive force application. This project uses operator ECG and EEG to detect high cognitive load and automatically adjust haptic sensitivity.</p>

<h2>Setup</h2>
<p>A Franka Emika robot arm is controlled via VR with a haptic glove. Our LSL platform streams operator physiology to an adaptive controller that modulates force rendering in real-time.</p>',
            'tags'    => 'Haptics, Teleoperation, Robotics, ECG, VR',
            'ordre'   => 7,
            'periode' => '2023 — 2025',
            'link_partenaires' => [ 'UQAC', 'MITACS' ],
        ],
        [
            'title'   => 'Multimodal Learning Analytics',
            'excerpt' => 'Using physiological data to understand student engagement and learning outcomes in digital classrooms.',
            'content' => '<h2>Overview</h2>
<p>Online and hybrid learning environments lack the non-verbal cues teachers rely on in physical classrooms. This project uses webcam-based eye-tracking and wristband-based electrodermal activity to build engagement profiles.</p>

<h2>Goals</h2>
<ul>
<li>Correlate physiological engagement with quiz performance</li>
<li>Detect disengagement moments in real-time</li>
<li>Propose adaptive content delivery strategies</li>
</ul>',
            'tags'    => 'Learning Analytics, EDA, Eye-Tracking, Education, ML',
            'ordre'   => 8,
            'periode' => '2025 — Present',
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
            update_post_meta( $id, '_projet_tags',    $p['tags'] );
            update_post_meta( $id, '_projet_ordre',   $p['ordre'] );
            update_post_meta( $id, '_projet_periode', $p['periode'] ?? '' );

            // Link financements
            if ( ! empty( $p['link_financements'] ) ) {
                $fin_ids = [];
                foreach ( $p['link_financements'] as $ft ) {
                    $fq = new WP_Query([ 'post_type' => 'financement', 'title' => $ft, 'posts_per_page' => 1, 'fields' => 'ids' ]);
                    if ( $fq->have_posts() ) $fin_ids[] = $fq->posts[0];
                }
                if ( $fin_ids ) update_post_meta( $id, '_projet_financements', $fin_ids );
            }

            // Link partenaires
            if ( ! empty( $p['link_partenaires'] ) ) {
                $part_ids = [];
                foreach ( $p['link_partenaires'] as $pt ) {
                    $pq = new WP_Query([ 'post_type' => 'partenaire', 'title' => $pt, 'posts_per_page' => 1, 'fields' => 'ids' ]);
                    if ( $pq->have_posts() ) $part_ids[] = $pq->posts[0];
                }
                if ( $part_ids ) update_post_meta( $id, '_projet_partenaires', $part_ids );
            }
        }
    }
}

/**
 * Seed: academic publications.
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
            'title'   => 'State of the Art: Multi-Sensor Systems for User Experience Evaluation',
            'annee'   => 2023,
            'auteurs' => 'Deladem K.',
            'revue'   => 'Research Report, UQAC',
            'type'    => 'rapport',
            'doi'     => '',
        ],
        [
            'title'   => 'Design of a Multimodal Acquisition Platform for HCI Research in Virtual Reality',
            'annee'   => 2026,
            'auteurs' => 'Deladem K.',
            'revue'   => 'PhD Thesis, Université du Québec à Chicoutimi',
            'type'    => 'these',
            'doi'     => '',
        ],
        [
            'title'   => 'Accessible Interaction Paradigms for VR Users with Motor Impairments',
            'annee'   => 2025,
            'auteurs' => 'Deladem K., Ménélas B.-A. J.',
            'revue'   => 'ACM SIGACCESS Conference on Computers and Accessibility (ASSETS 2025)',
            'type'    => 'conference',
            'doi'     => 'https://doi.org/10.1145/example.assets2025',
        ],
        [
            'title'   => 'Real-Time Motion Artifact Rejection in EEG During VR Interactions',
            'annee'   => 2025,
            'auteurs' => 'Deladem K., Mcheick H., Ménélas B.-A. J.',
            'revue'   => 'IEEE International Conference on Systems, Man and Cybernetics (SMC 2025)',
            'type'    => 'conference',
            'doi'     => 'https://doi.org/10.1109/example.smc2025',
        ],
        [
            'title'   => 'Physiological Correlates of Engagement in Online Learning Environments',
            'annee'   => 2024,
            'auteurs' => 'Deladem K., Ménélas B.-A. J.',
            'revue'   => 'Poster at Graphics Interface 2024',
            'type'    => 'poster',
            'doi'     => '',
        ],
        [
            'title'   => 'Adaptive Haptic Rendering Based on Operator Cognitive Load in Teleoperation',
            'annee'   => 2024,
            'auteurs' => 'Deladem K., Ménélas B.-A. J., Mcheick H.',
            'revue'   => 'Journal of Multimodal User Interfaces',
            'type'    => 'journal',
            'doi'     => 'https://doi.org/10.1007/example.jmui2024',
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
 * Seed: partners (no logo, with initials).
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
 * Seed: CV entries (education, experience, skills).
 */
function deladem_seed_cv() {

    // Education
    $formations = [
        [
            'title'         => 'PhD in Computer Science',
            'periode'       => '2022 — Present',
            'etablissement' => 'UQAC, Chicoutimi QC',
            'ordre'         => 1,
        ],
        [
            'title'         => 'MSc in Computer Science',
            'periode'       => '2019 — 2022',
            'etablissement' => 'UQAC, Chicoutimi QC',
            'ordre'         => 2,
        ],
        [
            'title'         => 'BSc in Software Engineering',
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

    // Experience
    $experiences = [
        [
            'title'         => 'Research Assistant — LRIT',
            'periode'       => '2022 — Present',
            'etablissement' => 'UQAC, Chicoutimi QC',
            'ordre'         => 1,
        ],
        [
            'title'         => 'Lecturer — Introduction to Programming',
            'periode'       => '2023 — 2024',
            'etablissement' => 'UQAC, Computer Science Department',
            'ordre'         => 2,
        ],
        [
            'title'         => 'Full-Stack Developer (Internship)',
            'periode'       => '2021 — 2022',
            'etablissement' => 'Tech Solutions Inc., Montreal QC',
            'ordre'         => 3,
        ],
        [
            'title'         => 'Freelance IT Consultant',
            'periode'       => '2019 — 2021',
            'etablissement' => 'Various clients, Togo & Canada',
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

    // Skills
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

/**
 * Seed: financements (funding entries).
 */
function deladem_seed_financements() {
    $find_partenaire = function( $title ) {
        $q = new WP_Query([
            'post_type' => 'partenaire', 'title' => $title,
            'posts_per_page' => 1, 'post_status' => 'any', 'fields' => 'ids',
        ]);
        return $q->have_posts() ? $q->posts[0] : 0;
    };

    $frqnt_id    = $find_partenaire( 'FRQNT' );
    $mitacs_id   = $find_partenaire( 'MITACS' );
    $uqac_id     = $find_partenaire( 'UQAC' );
    $tobii_id    = $find_partenaire( 'Tobii' );
    $neurosity_id = $find_partenaire( 'Neurosity' );

    $financements = [
        [
            'title'        => 'Bourse de doctorat FRQNT',
            'partenaire'   => $frqnt_id,
            'type'         => 'bourse',
            'montant'      => '25 000',
            'devise'       => 'CAD',
            'modalite'     => 'par_an',
            'statut'       => 'actif',
            'role'         => 'beneficiaire',
            'beneficiaire' => 'Moi',
            'periode'      => '2022 — 2025',
            'description'  => 'Bourse de formation doctorale du Fonds de recherche du Québec — Nature et technologies.',
            'ordre'        => 1,
        ],
        [
            'title'        => 'Subvention MITACS Accelerate',
            'partenaire'   => $mitacs_id,
            'type'         => 'subvention',
            'montant'      => '15 000',
            'devise'       => 'CAD',
            'modalite'     => 'total',
            'statut'       => 'termine',
            'role'         => 'stagiaire',
            'beneficiaire' => 'Moi',
            'periode'      => '2023 — 2024',
            'description'  => 'Stage de recherche industrielle en partenariat avec un partenaire du secteur privé.',
            'ordre'        => 2,
        ],
        [
            'title'        => 'Financement équipement de recherche',
            'partenaire'   => $uqac_id,
            'type'         => 'financement',
            'montant'      => '8 000',
            'devise'       => 'CAD',
            'modalite'     => 'ponctuel',
            'statut'       => 'termine',
            'role'         => 'assistant',
            'beneficiaire' => 'Laboratoire LRIT',
            'periode'      => '2023',
            'description'  => 'Acquisition de capteurs physiologiques et équipement VR pour le laboratoire.',
            'ordre'        => 3,
        ],
        [
            'title'        => 'Prix excellence en recherche',
            'partenaire'   => $uqac_id,
            'type'         => 'prix',
            'montant'      => '2 000',
            'devise'       => 'CAD',
            'modalite'     => 'ponctuel',
            'statut'       => 'termine',
            'role'         => 'chercheur_principal',
            'beneficiaire' => 'Moi',
            'periode'      => '2024',
            'description'  => '',
            'ordre'        => 4,
        ],
        [
            'title'        => 'Bourse de mobilité internationale',
            'partenaire'   => $uqac_id,
            'type'         => 'bourse',
            'montant'      => '5 000',
            'devise'       => 'CAD',
            'modalite'     => 'ponctuel',
            'statut'       => 'termine',
            'role'         => 'beneficiaire',
            'beneficiaire' => 'Moi',
            'periode'      => '2023',
            'description'  => 'Funding for international research stay and conference participation.',
            'ordre'        => 5,
        ],
        [
            'title'        => 'Contrat de recherche Tobii',
            'partenaire'   => $tobii_id,
            'type'         => 'contrat',
            'montant'      => '12 000',
            'devise'       => 'USD',
            'modalite'     => 'total',
            'statut'       => 'actif',
            'role'         => 'co_chercheur',
            'beneficiaire' => 'Laboratoire LRIT',
            'periode'      => '2024 — 2025',
            'description'  => 'Industry partnership for eye-tracking integration in VR accessibility research.',
            'ordre'        => 6,
        ],
        [
            'title'        => 'Subvention NSERC Discovery',
            'partenaire'   => $frqnt_id,
            'type'         => 'subvention',
            'montant'      => '35 000',
            'devise'       => 'CAD',
            'modalite'     => 'par_an',
            'statut'       => 'actif',
            'role'         => 'assistant',
            'beneficiaire' => 'Prof. Ménélas B.-A. J.',
            'periode'      => '2022 — 2027',
            'description'  => 'Discovery grant supporting fundamental research in multimodal interaction systems.',
            'ordre'        => 7,
        ],
        [
            'title'        => 'Equipment grant Neurosity',
            'partenaire'   => $neurosity_id,
            'type'         => 'financement',
            'montant'      => '',
            'devise'       => 'CAD',
            'modalite'     => 'en_nature',
            'statut'       => 'termine',
            'role'         => 'beneficiaire',
            'beneficiaire' => 'Laboratoire LRIT',
            'periode'      => '2022',
            'description'  => 'In-kind donation of two Neurosity Crown EEG headsets for research purposes.',
            'ordre'        => 8,
        ],
    ];

    foreach ( $financements as $f ) {
        if ( deladem_post_exists( $f['title'], 'financement' ) ) {
            continue;
        }
        $id = wp_insert_post( [
            'post_type'   => 'financement',
            'post_title'  => $f['title'],
            'post_status' => 'publish',
        ] );
        if ( $id && ! is_wp_error( $id ) ) {
            update_post_meta( $id, '_financement_partenaire',   $f['partenaire'] );
            update_post_meta( $id, '_financement_type',         $f['type'] );
            update_post_meta( $id, '_financement_montant',      $f['montant'] );
            update_post_meta( $id, '_financement_devise',       $f['devise'] );
            update_post_meta( $id, '_financement_modalite',     $f['modalite'] );
            update_post_meta( $id, '_financement_statut',       $f['statut'] );
            update_post_meta( $id, '_financement_role',         $f['role'] );
            update_post_meta( $id, '_financement_beneficiaire', $f['beneficiaire'] );
            update_post_meta( $id, '_financement_periode',      $f['periode'] );
            update_post_meta( $id, '_financement_description',  $f['description'] );
            update_post_meta( $id, '_financement_ordre',        $f['ordre'] );
        }
    }
}
