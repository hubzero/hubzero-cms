#!/usr/bin/env php
<?php

/**
 * Seed com_resources with diverse resources covering all states, access levels,
 * resource types, author roles, parent-child relationships, ratings, DOIs,
 * publish scheduling, and group ownership.
 *
 * Usage:  php core/components/com_resources/seed.php
 *         php core/components/com_resources/seed.php --down   (remove seed data)
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// ── Bootstrap database connection ────────────────────────────────────────────

$configFile = __DIR__ . '/../../../app/config/database.php';
if (!file_exists($configFile)) {
    fwrite(STDERR, "Cannot find database config at: $configFile\n");
    exit(1);
}

$config = require $configFile;
$prefix = $config['dbprefix'] ?? 'jos_';

$dsn = sprintf(
    'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
    $config['host'],
    $config['port'] ?? '3306',
    $config['db']
);

try {
    $db = new PDO($dsn, $config['user'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    ]);
} catch (PDOException $e) {
    fwrite(STDERR, "Database connection failed: " . $e->getMessage() . "\n");
    exit(1);
}

$SEED_MARKER = '[SEED2]';

// ── Handle --down flag ───────────────────────────────────────────────────────

if (in_array('--down', $argv)) {
    seedDown($db, $prefix, $SEED_MARKER);
    echo "Seed data removed.\n";
    exit(0);
}

// ── Main: seed up ────────────────────────────────────────────────────────────

seedUp($db, $prefix, $SEED_MARKER);
echo "Seed data inserted.\n";
exit(0);

// ═════════════════════════════════════════════════════════════════════════════

function seedUp(PDO $db, string $prefix, string $marker): void
{
    // Check if already seeded
    $stmt = $db->query(
        "SELECT COUNT(*) FROM `{$prefix}resources` WHERE `title` LIKE '{$marker} %'"
    );
    if ((int) $stmt->fetchColumn() > 0) {
        echo "Already seeded — skipping. Use --down first to re-seed.\n";
        return;
    }

    // ── Look up users ────────────────────────────────────────────────────
    $stmt = $db->query(
        "SELECT `id`, `name` FROM `{$prefix}users` ORDER BY `id` ASC LIMIT 20"
    );
    $users = $stmt->fetchAll(PDO::FETCH_OBJ);
    if (empty($users)) {
        fwrite(STDERR, "No users found — run user seeding first.\n");
        exit(1);
    }
    $userIds = array_column($users, 'id');
    $userNames = [];
    foreach ($users as $u) {
        $userNames[$u->id] = $u->name;
    }

    // ── Ensure author roles ──────────────────────────────────────────────
    ensureAuthorRoles($db, $prefix);

    // ── Resource definitions ─────────────────────────────────────────────
    // States:  1=published, 0=unpublished, -1=archived, 2=draft, 3=pending, 4=trashed, 5=draft_internal
    // Access:  0=public, 1=registered, 2=special, 3=protected, 4=private
    // Types:   1=Seminars, 2=Workshops, 3=Documents, 7=Tools, 9=datasets,
    //          31=Series, 39=Teaching Materials

    $now = date('Y-m-d H:i:s');
    $resources = getResourceDefinitions($marker);

    $insertedIds = [];
    $insertResource = $db->prepare(
        "INSERT INTO `{$prefix}resources`
            (`title`, `alias`, `type`, `introtext`, `fulltxt`, `footertext`,
             `created`, `created_by`, `modified`, `modified_by`,
             `published`, `publish_up`, `publish_down`,
             `access`, `hits`, `standalone`, `rating`, `times_rated`, `ranking`,
             `group_owner`, `group_access`, `master_doi`, `license`, `params`, `attribs`)
         VALUES
            (:title, :alias, :type, :introtext, :fulltxt, '',
             :created, :created_by, :modified, :modified_by,
             :published, :publish_up, :publish_down,
             :access, :hits, :standalone, :rating, :times_rated, :ranking,
             :group_owner, :group_access, :master_doi, :license, :params, :attribs)"
    );

    foreach ($resources as $r) {
        $creatorId = $userIds[$r['creator_idx'] % count($userIds)];
        $modifierId = $userIds[($r['creator_idx'] + 1) % count($userIds)];

        $insertResource->execute([
            ':title'        => $r['title'],
            ':alias'        => $r['alias'],
            ':type'         => $r['type'],
            ':introtext'    => $r['introtext'],
            ':fulltxt'      => $r['fulltxt'] ?? '',
            ':created'      => $r['created'],
            ':created_by'   => $creatorId,
            ':modified'     => $r['modified'] ?? $now,
            ':modified_by'  => $modifierId,
            ':published'    => $r['published'],
            ':publish_up'   => $r['publish_up'] ?? '0000-00-00 00:00:00',
            ':publish_down' => $r['publish_down'] ?? '0000-00-00 00:00:00',
            ':access'       => $r['access'] ?? 0,
            ':hits'         => $r['hits'] ?? rand(5, 2000),
            ':standalone'   => $r['standalone'] ?? 1,
            ':rating'       => $r['rating'] ?? 0.0,
            ':times_rated'  => $r['times_rated'] ?? 0,
            ':ranking'      => $r['ranking'] ?? 0.0,
            ':group_owner'  => $r['group_owner'] ?? '',
            ':group_access' => $r['group_access'] ?? '',
            ':master_doi'   => $r['master_doi'] ?? '',
            ':license'      => $r['license'] ?? '',
            ':params'       => $r['params'] ?? '',
            ':attribs'      => $r['attribs'] ?? '',
        ]);

        $id = (int) $db->lastInsertId();
        $insertedIds[$r['alias']] = $id;

        // Add authors
        if (!empty($r['authors'])) {
            addAuthors($db, $prefix, $id, $r['authors'], $userIds, $userNames);
        }

        echo "  + Resource #{$id}: {$r['title']}\n";
    }

    // ── Parent-child associations ────────────────────────────────────────
    addAssociations($db, $prefix, $insertedIds);

    $count = count($resources);
    echo "\nInserted {$count} resources with authors and associations.\n";
}

function seedDown(PDO $db, string $prefix, string $marker): void
{
    // Get IDs of seeded resources
    $stmt = $db->query(
        "SELECT `id` FROM `{$prefix}resources` WHERE `title` LIKE '{$marker} %'"
    );
    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($ids)) {
        echo "No seed data found.\n";
        return;
    }

    $idList = implode(',', array_map('intval', $ids));

    // Remove authors
    $db->exec(
        "DELETE FROM `{$prefix}author_assoc`
         WHERE `subtable` = 'resources' AND `subid` IN ({$idList})"
    );

    // Remove parent-child associations
    $db->exec(
        "DELETE FROM `{$prefix}resource_assoc`
         WHERE `parent_id` IN ({$idList}) OR `child_id` IN ({$idList})"
    );

    // Remove resources
    $db->exec(
        "DELETE FROM `{$prefix}resources` WHERE `id` IN ({$idList})"
    );

    // Remove author roles we created
    $db->exec(
        "DELETE FROM `{$prefix}author_roles` WHERE `alias` IN ('author','editor','contributor','advisor','submitter')"
    );

    echo "Removed " . count($ids) . " resources and related data.\n";
}

// ═════════════════════════════════════════════════════════════════════════════
// Helper Functions
// ═════════════════════════════════════════════════════════════════════════════

function ensureAuthorRoles(PDO $db, string $prefix): void
{
    $roles = [
        ['title' => 'Author',      'alias' => 'author'],
        ['title' => 'Editor',      'alias' => 'editor'],
        ['title' => 'Contributor', 'alias' => 'contributor'],
        ['title' => 'Advisor',     'alias' => 'advisor'],
        ['title' => 'Submitter',   'alias' => 'submitter'],
    ];

    $now = date('Y-m-d H:i:s');
    foreach ($roles as $role) {
        $stmt = $db->prepare(
            "SELECT COUNT(*) FROM `{$prefix}author_roles` WHERE `alias` = ?"
        );
        $stmt->execute([$role['alias']]);
        if ((int) $stmt->fetchColumn() === 0) {
            $db->prepare(
                "INSERT INTO `{$prefix}author_roles`
                    (`title`, `alias`, `state`, `created`, `created_by`)
                 VALUES (?, ?, 1, ?, 1000)"
            )->execute([$role['title'], $role['alias'], $now]);
        }
    }
}

function addAuthors(
    PDO $db,
    string $prefix,
    int $resourceId,
    array $authors,
    array $userIds,
    array $userNames
): void {
    $insert = $db->prepare(
        "INSERT INTO `{$prefix}author_assoc`
            (`subtable`, `subid`, `authorid`, `ordering`, `role`, `name`, `organization`)
         VALUES ('resources', ?, ?, ?, ?, ?, ?)"
    );

    foreach ($authors as $i => $author) {
        $uid = $userIds[$author['user_idx'] % count($userIds)];
        $name = $author['name'] ?? $userNames[$uid] ?? 'Unknown';
        $insert->execute([
            $resourceId,
            $uid,
            $i + 1,
            $author['role'] ?? '',
            $name,
            $author['org'] ?? 'Purdue University',
        ]);
    }
}

function addAssociations(PDO $db, string $prefix, array $ids): void
{
    // Define parent-child relationships using aliases
    $relationships = [
        // Series → children
        ['parent' => 'ml-seminar-series',     'child' => 'neural-network-fundamentals', 'order' => 1],
        ['parent' => 'ml-seminar-series',     'child' => 'deep-learning-applications',  'order' => 2],
        ['parent' => 'ml-seminar-series',     'child' => 'reinforcement-learning-intro', 'order' => 3],
        // Workshop → materials
        ['parent' => 'hpc-cluster-workshop',  'child' => 'mpi-programming-guide',       'order' => 1],
        ['parent' => 'hpc-cluster-workshop',  'child' => 'climate-model-dataset-v3',    'order' => 2],
        // Course → lectures
        ['parent' => 'quantum-computing-101', 'child' => 'quantum-gates-tutorial',      'order' => 1],
    ];

    $insert = $db->prepare(
        "INSERT INTO `{$prefix}resource_assoc`
            (`parent_id`, `child_id`, `ordering`, `grouping`)
         VALUES (?, ?, ?, 0)"
    );

    foreach ($relationships as $rel) {
        if (isset($ids[$rel['parent']]) && isset($ids[$rel['child']])) {
            $insert->execute([$ids[$rel['parent']], $ids[$rel['child']], $rel['order']]);
        }
    }
}

function getResourceDefinitions(string $m): array
{
    // Helper to generate past dates
    $pastDate = fn(int $daysAgo) => date('Y-m-d H:i:s', strtotime("-{$daysAgo} days"));
    $futureDate = fn(int $daysAhead) => date('Y-m-d H:i:s', strtotime("+{$daysAhead} days"));

    return [
        // ────────────────────────────────────────────────────────────────────
        // PUBLISHED resources (state=1) — various types, access, ratings
        // ────────────────────────────────────────────────────────────────────
        [
            'title'       => "{$m} Neural Network Fundamentals",
            'alias'       => 'neural-network-fundamentals',
            'type'        => 1, // Seminars
            'introtext'   => 'An introductory seminar on artificial neural networks, covering perceptrons, backpropagation, and activation functions. Designed for graduate students and researchers new to machine learning.',
            'fulltxt'     => '<p>This seminar covers the foundational concepts of neural networks including:</p><ul><li>Perceptron model and linear classifiers</li><li>Multi-layer networks and the universal approximation theorem</li><li>Backpropagation algorithm</li><li>Common activation functions (ReLU, sigmoid, tanh)</li><li>Gradient descent optimization</li></ul><p>Prerequisites: Linear algebra, basic calculus, Python programming.</p>',
            'created'     => $pastDate(180),
            'published'   => 1,
            'access'      => 0, // Public
            'hits'        => 1847,
            'rating'      => 4.5,
            'times_rated' => 24,
            'ranking'     => 8.7,
            'license'     => 'cc40-by-nc-sa',
            'creator_idx' => 2,
            'authors'     => [
                ['user_idx' => 10, 'role' => 'author', 'name' => 'Grace Hopper', 'org' => 'MIT'],
                ['user_idx' => 11, 'role' => 'author', 'name' => 'Alan Turing', 'org' => 'Stanford University'],
            ],
        ],
        [
            'title'       => "{$m} Deep Learning Applications in Materials Science",
            'alias'       => 'deep-learning-applications',
            'type'        => 1, // Seminars
            'introtext'   => 'A seminar exploring how deep learning techniques are being applied to materials discovery, property prediction, and molecular dynamics simulation.',
            'fulltxt'     => '<p>Topics include convolutional neural networks for crystal structure prediction, graph neural networks for molecular properties, and generative models for materials design.</p>',
            'created'     => $pastDate(120),
            'published'   => 1,
            'access'      => 1, // Registered
            'hits'        => 932,
            'rating'      => 4.8,
            'times_rated' => 15,
            'ranking'     => 9.2,
            'license'     => 'cc30-by',
            'master_doi'  => '10.1234/hub.2026.0042',
            'creator_idx' => 3,
            'authors'     => [
                ['user_idx' => 12, 'role' => 'author', 'name' => 'Ada Lovelace', 'org' => 'Purdue University'],
                ['user_idx' => 14, 'role' => 'contributor', 'name' => 'Ana Martinez', 'org' => 'Caltech'],
            ],
        ],
        [
            'title'       => "{$m} HPC Cluster Computing Workshop",
            'alias'       => 'hpc-cluster-workshop',
            'type'        => 2, // Workshops
            'introtext'   => 'Hands-on workshop covering high-performance computing cluster usage, job scheduling with SLURM, and parallel programming with MPI and OpenMP.',
            'fulltxt'     => '<p>This 3-day workshop provides practical experience with HPC systems. Participants will learn to submit jobs, manage resources, profile code performance, and scale applications to thousands of cores.</p>',
            'created'     => $pastDate(90),
            'published'   => 1,
            'access'      => 0,
            'hits'        => 2341,
            'rating'      => 4.2,
            'times_rated' => 37,
            'ranking'     => 7.5,
            'publish_up'  => $pastDate(90),
            'license'     => 'cc30-by-nc-sa',
            'creator_idx' => 0,
            'authors'     => [
                ['user_idx' => 0, 'role' => 'submitter', 'name' => 'Site Administrator', 'org' => 'Purdue University'],
                ['user_idx' => 13, 'role' => 'author', 'name' => 'John Smith', 'org' => 'Argonne National Lab'],
                ['user_idx' => 15, 'role' => 'author', 'name' => 'Brian Lee', 'org' => 'Oak Ridge National Lab'],
            ],
        ],
        [
            'title'       => "{$m} MPI Programming Guide",
            'alias'       => 'mpi-programming-guide',
            'type'        => 3, // Documents
            'introtext'   => 'Comprehensive guide to Message Passing Interface (MPI) programming, from basic point-to-point communication to advanced collective operations.',
            'fulltxt'     => '<p>This document covers MPI-1 and MPI-2 specifications with C and Fortran examples. Includes chapters on derived datatypes, one-sided communication, and I/O.</p>',
            'created'     => $pastDate(365),
            'published'   => 1,
            'access'      => 0,
            'hits'        => 5672,
            'rating'      => 4.7,
            'times_rated' => 89,
            'ranking'     => 9.5,
            'master_doi'  => '10.1234/hub.2025.0017',
            'license'     => 'cc30-by',
            'creator_idx' => 5,
            'authors'     => [
                ['user_idx' => 15, 'role' => 'author', 'name' => 'Brian Lee', 'org' => 'Oak Ridge National Lab'],
            ],
        ],
        [
            'title'       => "{$m} NanoSim: Molecular Dynamics Simulator",
            'alias'       => 'nanosim-molecular-dynamics',
            'type'        => 7, // Tools
            'introtext'   => 'An interactive molecular dynamics simulation tool for modeling nanoscale systems. Supports Lennard-Jones, Tersoff, and ReaxFF potentials.',
            'fulltxt'     => '<p>NanoSim provides a web-based interface for setting up, running, and visualizing molecular dynamics simulations. Features include real-time 3D visualization, trajectory analysis, and radial distribution function computation.</p>',
            'created'     => $pastDate(400),
            'modified'    => $pastDate(10),
            'published'   => 1,
            'access'      => 0,
            'hits'        => 12450,
            'rating'      => 4.3,
            'times_rated' => 156,
            'ranking'     => 9.8,
            'master_doi'  => '10.4231/hub.nanosim.v2',
            'license'     => 'cc30-by-nc-sa',
            'creator_idx' => 4,
            'authors'     => [
                ['user_idx' => 14, 'role' => 'author', 'name' => 'Ana Martinez', 'org' => 'Caltech'],
                ['user_idx' => 16, 'role' => 'author', 'name' => 'Carol Davis', 'org' => 'Purdue University'],
                ['user_idx' => 17, 'role' => 'contributor', 'name' => 'David Wilson', 'org' => 'MIT'],
            ],
        ],
        [
            'title'       => "{$m} Climate Model Dataset v3.2",
            'alias'       => 'climate-model-dataset-v3',
            'type'        => 9, // Datasets
            'introtext'   => 'Global climate simulation output from CESM2, covering 1850-2100 under SSP2-4.5 scenario. Includes temperature, precipitation, sea level, and ice extent fields.',
            'fulltxt'     => '<p>This dataset contains monthly-averaged output from 10 ensemble members of CESM2 at 1° resolution. Variables include surface temperature, sea surface temperature, precipitation, sea ice concentration, and ice sheet mass balance.</p><p>Total size: 2.3 TB. Available in NetCDF4 format.</p>',
            'created'     => $pastDate(60),
            'published'   => 1,
            'access'      => 1, // Registered
            'hits'        => 734,
            'rating'      => 4.9,
            'times_rated' => 12,
            'ranking'     => 8.1,
            'master_doi'  => '10.4231/hub.climate.v3.2',
            'license'     => 'cc40-by-nc-sa',
            'creator_idx' => 6,
            'authors'     => [
                ['user_idx' => 18, 'role' => 'author', 'name' => 'Elena Garcia', 'org' => 'NCAR'],
                ['user_idx' => 10, 'role' => 'author', 'name' => 'Grace Hopper', 'org' => 'NOAA'],
            ],
        ],
        [
            'title'       => "{$m} Machine Learning Seminar Series",
            'alias'       => 'ml-seminar-series',
            'type'        => 31, // Series
            'introtext'   => 'A comprehensive seminar series covering the foundations and frontiers of machine learning, held weekly during the fall semester.',
            'fulltxt'     => '<p>This series brings together leading researchers to discuss topics ranging from supervised learning basics to cutting-edge generative AI. Each talk includes hands-on exercises.</p>',
            'created'     => $pastDate(200),
            'published'   => 1,
            'access'      => 0,
            'hits'        => 3201,
            'rating'      => 4.6,
            'times_rated' => 45,
            'ranking'     => 8.9,
            'creator_idx' => 1,
            'authors'     => [
                ['user_idx' => 1, 'role' => 'editor', 'name' => 'Nicholas Kisseberth', 'org' => 'Purdue University'],
            ],
        ],
        [
            'title'       => "{$m} Introduction to Jupyter on Hubzero",
            'alias'       => 'jupyter-on-hubzero',
            'type'        => 39, // Teaching Materials
            'introtext'   => 'Step-by-step teaching materials for using Jupyter notebooks within the Hubzero platform. Covers setup, kernel management, and sharing notebooks with students.',
            'fulltxt'     => '<p>Topics: launching Jupyter, installing custom packages, creating assignments, grading with nbgrader, and collaborative editing.</p>',
            'created'     => $pastDate(45),
            'published'   => 1,
            'access'      => 0,
            'hits'        => 567,
            'rating'      => 3.8,
            'times_rated' => 8,
            'ranking'     => 5.2,
            'creator_idx' => 7,
            'authors'     => [
                ['user_idx' => 19, 'role' => 'author', 'name' => 'Frank Miller', 'org' => 'Purdue University'],
            ],
        ],
        [
            'title'       => "{$m} Quantum Computing 101: Concepts and Simulations",
            'alias'       => 'quantum-computing-101',
            'type'        => 39, // Teaching Materials
            'introtext'   => 'An introductory course module on quantum computing with hands-on simulation exercises using Qiskit and Cirq frameworks.',
            'fulltxt'     => '<p>This teaching material covers qubits, quantum gates, entanglement, quantum algorithms (Grover, Shor), and error correction. Includes 12 lab exercises with auto-grading.</p>',
            'created'     => $pastDate(30),
            'published'   => 1,
            'access'      => 0,
            'hits'        => 289,
            'rating'      => 0.0,
            'times_rated' => 0,
            'ranking'     => 3.1,
            'license'     => 'cc40-by-nc-sa',
            'creator_idx' => 8,
            'authors'     => [
                ['user_idx' => 11, 'role' => 'author', 'name' => 'Alan Turing', 'org' => 'Stanford University'],
                ['user_idx' => 12, 'role' => 'advisor', 'name' => 'Ada Lovelace', 'org' => 'Purdue University'],
            ],
        ],

        // Published with scheduled publish_down (will expire)
        [
            'title'       => "{$m} Conference Workshop: GPU Programming with CUDA",
            'alias'       => 'gpu-programming-cuda-workshop',
            'type'        => 2, // Workshops
            'introtext'   => 'Workshop materials from the SC26 conference covering CUDA kernel development, memory optimization, and multi-GPU programming patterns.',
            'fulltxt'     => '<p>Limited-time access to conference workshop materials. Includes video recordings, slides, and code examples.</p>',
            'created'     => $pastDate(14),
            'published'   => 1,
            'access'      => 2, // Special
            'hits'        => 145,
            'publish_up'  => $pastDate(14),
            'publish_down' => $futureDate(30), // Expires in 30 days
            'license'     => 'cc30-by-nc-nd',
            'creator_idx' => 3,
            'authors'     => [
                ['user_idx' => 13, 'role' => 'author', 'name' => 'John Smith', 'org' => 'NVIDIA'],
            ],
        ],

        // Published with group ownership
        [
            'title'       => "{$m} Protein Folding Simulation Toolkit",
            'alias'       => 'protein-folding-toolkit',
            'type'        => 7, // Tools
            'introtext'   => 'A group-owned simulation toolkit for protein structure prediction using molecular dynamics and Monte Carlo methods.',
            'fulltxt'     => '<p>Developed by the BioComputing Research Group. Supports AMBER, CHARMM, and GROMACS force fields. Integrates with AlphaFold for initial structure prediction.</p>',
            'created'     => $pastDate(250),
            'modified'    => $pastDate(5),
            'published'   => 1,
            'access'      => 1, // Registered
            'hits'        => 4500,
            'rating'      => 4.1,
            'times_rated' => 67,
            'ranking'     => 8.4,
            'group_owner' => 'biocomputing',
            'master_doi'  => '10.4231/hub.protfold.v4',
            'license'     => 'cc30-by-nc-sa',
            'creator_idx' => 6,
            'authors'     => [
                ['user_idx' => 16, 'role' => 'author', 'name' => 'Carol Davis', 'org' => 'Purdue University'],
                ['user_idx' => 18, 'role' => 'author', 'name' => 'Elena Garcia', 'org' => 'Purdue University'],
                ['user_idx' => 17, 'role' => 'contributor', 'name' => 'David Wilson', 'org' => 'MIT'],
            ],
        ],

        // Published, private access
        [
            'title'       => "{$m} Internal Benchmarking Results: HPC Cluster 2026",
            'alias'       => 'internal-benchmarking-hpc-2026',
            'type'        => 3, // Documents
            'introtext'   => 'Internal benchmark results for the 2026 cluster upgrade. LINPACK, HPCG, and application-specific benchmarks across all node types.',
            'fulltxt'     => '<p>Restricted document containing performance data for procurement evaluation. Includes comparisons with vendor proposals.</p>',
            'created'     => $pastDate(20),
            'published'   => 1,
            'access'      => 4, // Private
            'hits'        => 23,
            'group_owner' => 'sysadmin',
            'creator_idx' => 0,
            'authors'     => [
                ['user_idx' => 0, 'role' => 'author', 'name' => 'Site Administrator', 'org' => 'Purdue University'],
            ],
        ],

        // ────────────────────────────────────────────────────────────────────
        // UNPUBLISHED resources (state=0)
        // ────────────────────────────────────────────────────────────────────
        [
            'title'       => "{$m} Deprecated: Legacy Visualization Toolkit v1",
            'alias'       => 'legacy-viz-toolkit-v1',
            'type'        => 7, // Tools
            'introtext'   => 'This tool has been superseded by the new WebGL-based visualization platform. Kept for reference but no longer maintained.',
            'fulltxt'     => '<p>Original Java-based visualization toolkit. Does not support modern browsers.</p>',
            'created'     => $pastDate(1200),
            'published'   => 0,
            'access'      => 0,
            'hits'        => 8923,
            'rating'      => 2.1,
            'times_rated' => 34,
            'creator_idx' => 4,
            'authors'     => [
                ['user_idx' => 19, 'role' => 'author', 'name' => 'Frank Miller', 'org' => 'Purdue University'],
            ],
        ],
        [
            'title'       => "{$m} Seasonal Flu Prediction Model (Retired)",
            'alias'       => 'flu-prediction-model-retired',
            'type'        => 7, // Tools
            'introtext'   => 'Epidemiological model for seasonal influenza prediction. Retired after the updated COVID-era model replaced it.',
            'created'     => $pastDate(800),
            'published'   => 0,
            'access'      => 0,
            'hits'        => 3456,
            'creator_idx' => 8,
            'authors'     => [
                ['user_idx' => 14, 'role' => 'author', 'name' => 'Ana Martinez', 'org' => 'Johns Hopkins'],
            ],
        ],

        // ────────────────────────────────────────────────────────────────────
        // ARCHIVED resources (state=-1) — previously published, now archived
        // ────────────────────────────────────────────────────────────────────
        [
            'title'       => "{$m} Archive: 2024 Annual Research Symposium Proceedings",
            'alias'       => 'archive-2024-symposium',
            'type'        => 3, // Documents
            'introtext'   => 'Complete proceedings from the 2024 Annual Research Symposium including abstracts, presentations, and poster summaries.',
            'fulltxt'     => '<p>53 contributed talks and 120 poster presentations across computational science, data analytics, and cyberinfrastructure tracks.</p>',
            'created'     => $pastDate(450),
            'published'   => -1,
            'access'      => 0,
            'hits'        => 2100,
            'rating'      => 4.0,
            'times_rated' => 18,
            'master_doi'  => '10.1234/hub.symp.2024',
            'creator_idx' => 1,
            'authors'     => [
                ['user_idx' => 1, 'role' => 'editor', 'name' => 'Nicholas Kisseberth', 'org' => 'Purdue University'],
                ['user_idx' => 10, 'role' => 'editor', 'name' => 'Grace Hopper', 'org' => 'MIT'],
            ],
        ],
        [
            'title'       => "{$m} Archive: Introduction to Fortran 90 (Legacy Course)",
            'alias'       => 'archive-fortran90-course',
            'type'        => 39, // Teaching Materials
            'introtext'   => 'Archived course materials for Fortran 90 programming. Replaced by the modern Fortran 2018 course.',
            'created'     => $pastDate(900),
            'published'   => -1,
            'access'      => 0,
            'hits'        => 15600,
            'rating'      => 3.5,
            'times_rated' => 210,
            'creator_idx' => 5,
            'authors'     => [
                ['user_idx' => 10, 'role' => 'author', 'name' => 'Grace Hopper', 'org' => 'US Navy / MIT'],
            ],
        ],
        [
            'title'       => "{$m} Archive: RAPPTURE Toolkit v1.0",
            'alias'       => 'archive-rappture-v1',
            'type'        => 7, // Tools
            'introtext'   => 'Archived version 1.0 of the RAPPTURE simulation toolkit. Superseded by RAPPTURE v2.0.',
            'fulltxt'     => '<p>RAPPTURE (Rapid Application Infrastructure Toolkit) v1.0 was the original GUI builder for Hubzero simulation tools. This version is preserved for historical reference.</p>',
            'created'     => $pastDate(1500),
            'published'   => -1,
            'access'      => 0,
            'hits'        => 45000,
            'rating'      => 3.9,
            'times_rated' => 502,
            'master_doi'  => '10.4231/hub.rappture.v1',
            'creator_idx' => 0,
            'authors'     => [
                ['user_idx' => 0, 'role' => 'author', 'name' => 'Site Administrator', 'org' => 'Purdue University'],
                ['user_idx' => 15, 'role' => 'contributor', 'name' => 'Brian Lee', 'org' => 'Purdue University'],
            ],
        ],

        // ────────────────────────────────────────────────────────────────────
        // DRAFT resources (state=2)
        // ────────────────────────────────────────────────────────────────────
        [
            'title'       => "{$m} Draft: Federated Learning for Distributed Sensor Networks",
            'alias'       => 'draft-federated-learning',
            'type'        => 1, // Seminars
            'introtext'   => 'Upcoming seminar on privacy-preserving machine learning using federated learning across IoT sensor networks.',
            'fulltxt'     => '<p>DRAFT — content is being prepared by the presenter. Expected publication: next month.</p>',
            'created'     => $pastDate(5),
            'published'   => 2,
            'access'      => 0,
            'hits'        => 0,
            'creator_idx' => 9,
            'authors'     => [
                ['user_idx' => 18, 'role' => 'author', 'name' => 'Elena Garcia', 'org' => 'Purdue University'],
            ],
        ],
        [
            'title'       => "{$m} Draft: Multi-Scale Modeling Toolkit v3",
            'alias'       => 'draft-multiscale-toolkit-v3',
            'type'        => 7, // Tools
            'introtext'   => 'Major update to the multi-scale modeling toolkit. Adds support for DFT coupling and improved meshing algorithms.',
            'fulltxt'     => '<p>DRAFT — tool is in testing phase. New features include automated mesh refinement, DFT-to-MD coupling, and a Python API.</p>',
            'created'     => $pastDate(15),
            'published'   => 2,
            'access'      => 0,
            'hits'        => 0,
            'creator_idx' => 4,
            'authors'     => [
                ['user_idx' => 16, 'role' => 'author', 'name' => 'Carol Davis', 'org' => 'Purdue University'],
                ['user_idx' => 17, 'role' => 'author', 'name' => 'David Wilson', 'org' => 'MIT'],
            ],
        ],

        // ────────────────────────────────────────────────────────────────────
        // PENDING resources (state=3) — awaiting admin approval
        // ────────────────────────────────────────────────────────────────────
        [
            'title'       => "{$m} Pending: Earthquake Simulation Dataset - Pacific Ring",
            'alias'       => 'pending-earthquake-dataset',
            'type'        => 9, // Datasets
            'introtext'   => 'Seismic simulation output for major fault zones along the Pacific Ring of Fire. Submitted for review.',
            'fulltxt'     => '<p>Contains synthetic seismogram data for 500+ simulated earthquakes (M5.0-M9.0) using the SPECFEM3D code. Data in ASDF format, total 800 GB.</p>',
            'created'     => $pastDate(3),
            'published'   => 3,
            'access'      => 0,
            'hits'        => 0,
            'creator_idx' => 7,
            'authors'     => [
                ['user_idx' => 19, 'role' => 'submitter', 'name' => 'Frank Miller', 'org' => 'Caltech'],
                ['user_idx' => 13, 'role' => 'author', 'name' => 'John Smith', 'org' => 'USGS'],
            ],
        ],
        [
            'title'       => "{$m} Pending: CRISPR Gene Editor Visualization Tool",
            'alias'       => 'pending-crispr-viz-tool',
            'type'        => 7, // Tools
            'introtext'   => 'Interactive visualization tool for CRISPR-Cas9 gene editing targeting and off-target analysis. Submitted for review.',
            'fulltxt'     => '<p>Web-based tool for visualizing guide RNA design, target specificity, and predicted off-target sites. Uses the latest CRISPRscan and CFD scoring algorithms.</p>',
            'created'     => $pastDate(7),
            'published'   => 3,
            'access'      => 0,
            'hits'        => 0,
            'creator_idx' => 8,
            'authors'     => [
                ['user_idx' => 14, 'role' => 'author', 'name' => 'Ana Martinez', 'org' => 'Broad Institute'],
            ],
        ],
        [
            'title'       => "{$m} Pending: Renewable Energy Grid Optimization Model",
            'alias'       => 'pending-renewable-grid-model',
            'type'        => 7, // Tools
            'introtext'   => 'Optimization tool for integrating solar, wind, and battery storage into electrical grid models. Submitted for admin review.',
            'created'     => $pastDate(1),
            'published'   => 3,
            'access'      => 0,
            'hits'        => 0,
            'creator_idx' => 6,
            'authors'     => [
                ['user_idx' => 15, 'role' => 'author', 'name' => 'Brian Lee', 'org' => 'NREL'],
                ['user_idx' => 18, 'role' => 'contributor', 'name' => 'Elena Garcia', 'org' => 'Purdue University'],
            ],
        ],

        // ────────────────────────────────────────────────────────────────────
        // TRASHED resources (state=4) — soft-deleted
        // ────────────────────────────────────────────────────────────────────
        [
            'title'       => "{$m} Trashed: Duplicate Entry - NanoSim v1 (old)",
            'alias'       => 'trashed-nanosim-v1-dup',
            'type'        => 7, // Tools
            'introtext'   => 'Duplicate entry created by mistake during migration. Trashed.',
            'created'     => $pastDate(500),
            'published'   => 4,
            'access'      => 0,
            'hits'        => 0,
            'creator_idx' => 0,
            'authors'     => [],
        ],
        [
            'title'       => "{$m} Trashed: Test Upload - Please Delete",
            'alias'       => 'trashed-test-upload',
            'type'        => 3, // Documents
            'introtext'   => 'Test document uploaded during system testing.',
            'created'     => $pastDate(2),
            'published'   => 4,
            'access'      => 0,
            'hits'        => 1,
            'creator_idx' => 0,
            'authors'     => [],
        ],

        // ────────────────────────────────────────────────────────────────────
        // DRAFT INTERNAL resources (state=5)
        // ────────────────────────────────────────────────────────────────────
        [
            'title'       => "{$m} Internal: Platform Migration Runbook",
            'alias'       => 'internal-migration-runbook',
            'type'        => 3, // Documents
            'introtext'   => 'Internal operations document for migrating the Hubzero instance to new infrastructure. Not for public consumption.',
            'fulltxt'     => '<p>Step-by-step runbook covering database migration, file system sync, DNS cutover, and rollback procedures.</p>',
            'created'     => $pastDate(10),
            'published'   => 5,
            'access'      => 4, // Private
            'hits'        => 12,
            'group_owner' => 'sysadmin',
            'creator_idx' => 0,
            'authors'     => [
                ['user_idx' => 0, 'role' => 'author', 'name' => 'Site Administrator', 'org' => 'Purdue University'],
            ],
        ],

        // ────────────────────────────────────────────────────────────────────
        // Additional interesting resources
        // ────────────────────────────────────────────────────────────────────

        // Published but low-rated
        [
            'title'       => "{$m} Reinforcement Learning: An Introduction",
            'alias'       => 'reinforcement-learning-intro',
            'type'        => 1, // Seminars
            'introtext'   => 'Overview of reinforcement learning covering Markov decision processes, Q-learning, and policy gradient methods.',
            'fulltxt'     => '<p>Seminar recording with slides. Covers basic RL concepts with robotics and game-playing examples.</p>',
            'created'     => $pastDate(150),
            'published'   => 1,
            'access'      => 0,
            'hits'        => 412,
            'rating'      => 2.5,
            'times_rated' => 6,
            'ranking'     => 2.8,
            'creator_idx' => 9,
            'authors'     => [
                ['user_idx' => 17, 'role' => 'author', 'name' => 'David Wilson', 'org' => 'MIT'],
            ],
        ],

        // Published with future publish_up (scheduled)
        [
            'title'       => "{$m} Upcoming: Summer School Materials - Computational Biology 2026",
            'alias'       => 'upcoming-compbio-summer-2026',
            'type'        => 39, // Teaching Materials
            'introtext'   => 'Materials for the upcoming summer school on computational biology. Will become available on the first day of the program.',
            'created'     => $pastDate(30),
            'published'   => 1,
            'access'      => 1, // Registered
            'hits'        => 0,
            'publish_up'  => $futureDate(60), // Not yet available
            'publish_down' => $futureDate(120),
            'creator_idx' => 2,
            'authors'     => [
                ['user_idx' => 16, 'role' => 'editor', 'name' => 'Carol Davis', 'org' => 'Purdue University'],
                ['user_idx' => 14, 'role' => 'author', 'name' => 'Ana Martinez', 'org' => 'Stanford University'],
            ],
        ],

        // Published, protected access, group owned
        [
            'title'       => "{$m} Proprietary Dataset: Semiconductor Process Data",
            'alias'       => 'semiconductor-process-data',
            'type'        => 9, // Datasets
            'introtext'   => 'Experimental semiconductor manufacturing process data shared under NDA. Access restricted to the nanofab research group.',
            'fulltxt'     => '<p>Contains wafer-level measurement data from 45nm process characterization. 2000 wafers across 5 lots.</p>',
            'created'     => $pastDate(75),
            'published'   => 1,
            'access'      => 3, // Protected
            'hits'        => 89,
            'group_owner' => 'nanofab',
            'group_access' => '["nanofab"]',
            'creator_idx' => 5,
            'authors'     => [
                ['user_idx' => 12, 'role' => 'author', 'name' => 'Ada Lovelace', 'org' => 'Purdue Nanofab'],
            ],
        ],

        // Quantum gates tutorial (child of quantum-computing-101)
        [
            'title'       => "{$m} Lab: Quantum Gates and Circuits",
            'alias'       => 'quantum-gates-tutorial',
            'type'        => 39, // Teaching Materials
            'introtext'   => 'Hands-on lab exercise implementing quantum gates (Hadamard, CNOT, Toffoli) and building simple quantum circuits in Qiskit.',
            'fulltxt'     => '<p>Students will implement single-qubit and multi-qubit gates, create Bell states, and run circuits on IBM Quantum simulators.</p>',
            'created'     => $pastDate(28),
            'published'   => 1,
            'access'      => 0,
            'hits'        => 156,
            'standalone'  => 0, // Child resource
            'creator_idx' => 8,
            'authors'     => [
                ['user_idx' => 11, 'role' => 'author', 'name' => 'Alan Turing', 'org' => 'Stanford University'],
            ],
        ],
    ];
}
