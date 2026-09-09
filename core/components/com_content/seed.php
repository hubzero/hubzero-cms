#!/usr/bin/env php
<?php

/**
 * Seed com_content with diverse articles covering all states, access levels,
 * categories, featured status, scheduling, and multiple authors.
 *
 * Usage:  php core/components/com_content/seed.php
 *         php core/components/com_content/seed.php --down   (remove seed data)
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

$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
    $config['host'], $config['port'] ?? '3306', $config['db']);

try {
    $db = new PDO($dsn, $config['user'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    ]);
} catch (PDOException $e) {
    fwrite(STDERR, "Database connection failed: " . $e->getMessage() . "\n");
    exit(1);
}

$SEED_MARKER = '[SEED]';

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
        "SELECT COUNT(*) FROM `{$prefix}content` WHERE `title` LIKE '$marker %'"
    );
    if ((int) $stmt->fetchColumn() > 0) {
        echo "Already seeded — skipping. Use --down first to re-seed.\n";
        return;
    }

    // ── Ensure categories ────────────────────────────────────────────────
    ensureCategories($db, $prefix, $marker);
    $cats = getCategoryMap($db, $prefix);

    // ── Look up users ────────────────────────────────────────────────────
    $stmt = $db->query("SELECT `id` FROM `{$prefix}users` ORDER BY `id` ASC LIMIT 5");
    $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (empty($userIds)) {
        $userIds = [0];
    }

    // ── Article definitions ──────────────────────────────────────────────
    $articles = getArticleDefinitions();

    // ── Insert ────────────────────────────────────────────────────────────
    $ordering = [];
    $featuredOrder = 1;

    $sql = "INSERT INTO `{$prefix}content`
               (`title`, `alias`, `introtext`, `fulltext`, `state`, `catid`,
                `created`, `created_by`, `created_by_alias`,
                `modified`, `modified_by`,
                `publish_up`, `publish_down`,
                `access`, `hits`, `featured`, `ordering`,
                `checked_out`, `checked_out_time`,
                `metakey`, `metadesc`, `metadata`, `attribs`,
                `images`, `urls`, `language`, `xreference`)
            VALUES (
                :title, :alias, :intro, :full, :state, :catid,
                :created, :created_by, :created_by_alias,
                :modified, :modified_by,
                :publish_up, :publish_down,
                :access, :hits, :featured, :ordering,
                :checked_out, :checked_out_time,
                '', '', '', '',
                '', '', '*', ''
            )";
    $insert = $db->prepare($sql);

    $fpSql = "INSERT INTO `{$prefix}content_frontpage` (`content_id`, `ordering`) VALUES (:id, :ord)";
    $fpInsert = $db->prepare($fpSql);

    foreach ($articles as $art) {
        $catId = $cats[$art['cat']] ?? $cats['uncategorised'] ?? 2;
        $authorId = $userIds[$art['author'] % count($userIds)];

        $pubUp = date('Y-m-d H:i:s', strtotime($art['pub_up'] . ' days'));
        $pubDown = $art['pub_down'] !== null
            ? date('Y-m-d H:i:s', strtotime($art['pub_down'] . ' days'))
            : null;

        $createdOffset = $art['pub_up'] - rand(0, 5);
        $created = date('Y-m-d H:i:s', strtotime($createdOffset . ' days'));
        $modified = date('Y-m-d H:i:s', strtotime(($art['pub_up'] + rand(0, 10)) . ' days'));

        if (!isset($ordering[$catId])) {
            $ordering[$catId] = 1;
        }
        $ord = $ordering[$catId]++;

        $alias = makeAlias($art['title']);

        $checkoutId = 0;
        $checkoutTime = null;
        if ($art['checkout']) {
            $checkoutId = $authorId;
            $checkoutTime = date('Y-m-d H:i:s', strtotime('-30 minutes'));
        }

        $title = "$marker " . $art['title'];

        $insert->execute([
            ':title'            => $title,
            ':alias'            => $alias,
            ':intro'            => $art['intro'],
            ':full'             => $art['full'],
            ':state'            => $art['state'],
            ':catid'            => $catId,
            ':created'          => $created,
            ':created_by'       => $authorId,
            ':created_by_alias' => $art['alias_by'],
            ':modified'         => $modified,
            ':modified_by'      => $authorId,
            ':publish_up'       => $pubUp,
            ':publish_down'     => $pubDown,
            ':access'           => $art['access'],
            ':hits'             => $art['hits'],
            ':featured'         => $art['featured'],
            ':ordering'         => $ord,
            ':checked_out'      => $checkoutId,
            ':checked_out_time' => $checkoutTime,
        ]);

        $articleId = (int) $db->lastInsertId();

        if ($art['featured']) {
            $fpInsert->execute([':id' => $articleId, ':ord' => $featuredOrder]);
            $featuredOrder++;
        }
    }

    $count = count($articles);
    echo "  Inserted $count articles ($featuredOrder featured)\n";
    echo "  States: published, unpublished, archived, trashed, scheduled\n";
    echo "  Access: public, registered, special\n";
    echo "  Categories: " . count(array_unique(array_column($articles, 'cat'))) . " used\n";
}

function seedDown(PDO $db, string $prefix, string $marker): void
{
    // Find seed article IDs
    $stmt = $db->query(
        "SELECT `id` FROM `{$prefix}content` WHERE `title` LIKE '$marker %'"
    );
    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($ids)) {
        $idList = implode(',', array_map('intval', $ids));

        $db->exec("DELETE FROM `{$prefix}content_frontpage` WHERE `content_id` IN ($idList)");
        $db->exec("DELETE FROM `{$prefix}content` WHERE `id` IN ($idList)");
        echo "  Removed " . count($ids) . " seed articles\n";
    }

    // Remove seed categories
    $stmt = $db->exec(
        "DELETE FROM `{$prefix}categories`
         WHERE `extension` = 'com_content'
           AND `description` LIKE '$marker%'"
    );
    echo "  Removed seed categories\n";
}

// ═════════════════════════════════════════════════════════════════════════════
// Helper functions
// ═════════════════════════════════════════════════════════════════════════════

function ensureCategories(PDO $db, string $prefix, string $marker): void
{
    $needed = [
        'tutorials'          => 'Tutorials',
        'news'               => 'News & Announcements',
        'documentation'      => 'Documentation',
        'research'           => 'Research Highlights',
        'maintenance'        => 'Maintenance Notices',
        'getting-started'    => 'Getting Started',
        'api-reference'      => 'API Reference',
        'developer-guide'    => 'Developer Guide',
        'admin-guide'        => 'Administrator Guide',
        'parallel-computing' => 'Parallel Computing',
        'machine-learning'   => 'Machine Learning',
        'data-analysis'      => 'Data Analysis',
    ];

    // Get root category for com_content
    $stmt = $db->query(
        "SELECT `id`, `lft`, `rgt` FROM `{$prefix}categories`
         WHERE `extension` = 'com_content' AND `level` = 0
         LIMIT 1"
    );
    $root = $stmt->fetch();

    if (!$root) {
        echo "  Warning: No root category for com_content — cannot create categories\n";
        return;
    }

    $created = 0;

    foreach ($needed as $alias => $title) {
        $stmt = $db->prepare(
            "SELECT COUNT(*) FROM `{$prefix}categories`
             WHERE `extension` = 'com_content' AND `alias` = ?"
        );
        $stmt->execute([$alias]);
        if ((int) $stmt->fetchColumn() > 0) {
            continue;
        }

        // Make room at the end of the tree
        $rgt = (int) $root->rgt;
        $db->exec(
            "UPDATE `{$prefix}categories` SET `rgt` = `rgt` + 2
             WHERE `extension` = 'com_content' AND `rgt` >= $rgt"
        );
        $db->exec(
            "UPDATE `{$prefix}categories` SET `lft` = `lft` + 2
             WHERE `extension` = 'com_content' AND `lft` > $rgt"
        );

        $now = date('Y-m-d H:i:s');
        $ins = $db->prepare(
            "INSERT INTO `{$prefix}categories`
               (`asset_id`, `parent_id`, `lft`, `rgt`, `level`,
                `path`, `extension`, `title`, `alias`,
                `description`, `published`, `access`,
                `params`, `metadesc`, `metakey`, `metadata`,
                `created_time`, `modified_time`, `language`)
             VALUES (
                0, :parent, :lft, :rgt, 1,
                :path, 'com_content', :title, :alias,
                :desc, 1, 1,
                '', '', '', '',
                :created, :modified, '*'
             )"
        );
        $ins->execute([
            ':parent'   => $root->id,
            ':lft'      => $rgt,
            ':rgt'      => $rgt + 1,
            ':path'     => $alias,
            ':title'    => $title,
            ':alias'    => $alias,
            ':desc'     => "$marker Auto-created for sample data",
            ':created'  => $now,
            ':modified' => $now,
        ]);

        $root->rgt = $rgt + 2;
        $created++;
    }

    if ($created > 0) {
        echo "  Created $created new categories\n";
    }
}

function getCategoryMap(PDO $db, string $prefix): array
{
    $stmt = $db->query(
        "SELECT `alias`, `id` FROM `{$prefix}categories`
         WHERE `extension` = 'com_content'"
    );
    $rows = $stmt->fetchAll();

    $map = [];
    foreach ($rows as $row) {
        $map[$row->alias] = (int) $row->id;
    }
    return $map;
}

function makeAlias(string $title): string
{
    $alias = strtolower($title);
    $alias = preg_replace('/[^a-z0-9\s-]/', '', $alias);
    $alias = preg_replace('/[\s-]+/', '-', $alias);
    return trim($alias, '-');
}

function getArticleDefinitions(): array
{
    return [
        // ── Published, Public, various categories ────────────────────────
        [
            'title'    => 'Getting Started with Hubzero',
            'cat'      => 'uncategorised',
            'state'    => 1,
            'access'   => 1,
            'featured' => 1,
            'author'   => 0,
            'intro'    => '<p>Welcome to Hubzero! This guide walks you through setting up your account, joining groups, and launching your first simulation tool. Whether you are a new researcher or a returning user, this article covers everything you need to get productive quickly.</p>',
            'full'     => '<h3>Step 1: Create Your Account</h3><p>Visit the registration page and fill in your institutional email address. You will receive a confirmation email within minutes.</p><h3>Step 2: Complete Your Profile</h3><p>Add your research interests, department, and a profile photo. This helps collaborators find you and makes group invitations easier.</p><h3>Step 3: Explore Available Tools</h3><p>Browse the tool catalog to find simulation and analysis tools relevant to your research. Each tool listing includes documentation, example inputs, and citation information.</p><h3>Step 4: Join a Research Group</h3><p>Groups provide shared workspaces, discussion forums, and collaborative projects. Search for groups in your research area or create your own.</p>',
            'pub_up'   => -90,
            'pub_down' => null,
            'hits'     => 1847,
            'checkout' => 0,
            'alias_by' => '',
        ],
        [
            'title'    => 'Simulation Best Practices for Materials Science',
            'cat'      => 'tutorials',
            'state'    => 1,
            'access'   => 1,
            'featured' => 1,
            'author'   => 1,
            'intro'    => '<p>Running accurate materials science simulations requires careful attention to convergence parameters, basis set selection, and validation against experimental data. This article summarizes best practices developed by the Hubzero computational materials community over a decade of collaborative research.</p>',
            'full'     => '<h3>Convergence Testing</h3><p>Always perform systematic convergence tests for your key parameters before production runs. For DFT calculations, test k-point grids, energy cutoffs, and supercell sizes independently.</p><h3>Validation Protocol</h3><p>Compare at least three calculated properties against published experimental values. Document any discrepancies and their likely causes in your project notes.</p><h3>Resource Management</h3><p>Estimate computational cost before submitting large job arrays. Use the job estimator tool to predict wall time and memory requirements.</p>',
            'pub_up'   => -60,
            'pub_down' => null,
            'hits'     => 923,
            'checkout' => 0,
            'alias_by' => '',
        ],
        [
            'title'    => 'January 2026 Platform Release Notes',
            'cat'      => 'news',
            'state'    => 1,
            'access'   => 1,
            'featured' => 1,
            'author'   => 0,
            'intro'    => '<p>The January 2026 platform release includes upgraded tool containers, a redesigned admin dashboard, improved accessibility across all components, and performance optimizations that reduce page load times by 40%.</p>',
            'full'     => '<h3>Tool Container Upgrades</h3><p>All tool containers have been upgraded to Ubuntu 22.04 LTS with updated compilers (GCC 12, Intel oneAPI 2024). Existing tools have been tested for compatibility.</p><h3>Admin Dashboard</h3><p>The admin interface now uses a modern daisyUI-based design with improved table layouts, responsive forms, and dark mode support.</p><h3>Accessibility Improvements</h3><p>All form components now meet WCAG 2.1 AA standards. Screen reader support has been improved across navigation, data tables, and interactive widgets.</p><h3>Performance</h3><p>Database query optimization and aggressive caching reduce average page load time from 1.2s to 0.7s. Asset bundling reduces HTTP requests by 60%.</p>',
            'pub_up'   => -45,
            'pub_down' => null,
            'hits'     => 2156,
            'checkout' => 0,
            'alias_by' => '',
        ],
        [
            'title'    => 'Understanding Molecular Dynamics Simulations',
            'cat'      => 'tutorials',
            'state'    => 1,
            'access'   => 1,
            'featured' => 0,
            'author'   => 2,
            'intro'    => '<p>Molecular dynamics (MD) simulations model the physical movements of atoms and molecules over time. This tutorial covers the fundamental concepts, force field selection, time step considerations, and ensemble choices for common MD workflows on Hubzero.</p>',
            'full'     => '<h3>Force Fields</h3><p>Choose your force field based on the system being studied. For proteins, AMBER and CHARMM are well-validated. For materials, use ReaxFF or machine-learned potentials.</p><h3>Time Steps</h3><p>The integration time step must be smaller than the fastest vibrational period in your system. For atomistic simulations with hydrogen, use 1-2 fs. With SHAKE/RATTLE constraints, you can safely use 2 fs.</p><h3>Equilibration</h3><p>Always equilibrate your system before production runs. Monitor temperature, pressure, and potential energy convergence.</p>',
            'pub_up'   => -30,
            'pub_down' => null,
            'hits'     => 456,
            'checkout' => 0,
            'alias_by' => '',
        ],
        [
            'title'    => 'Data Management and Sharing Guidelines',
            'cat'      => 'documentation',
            'state'    => 1,
            'access'   => 1,
            'featured' => 0,
            'author'   => 0,
            'intro'    => '<p>Effective data management is critical for reproducible research. This guide covers Hubzero\'s data storage options, sharing mechanisms, DOI assignment for datasets, and compliance with NSF data management plan requirements.</p>',
            'full'     => '<h3>Storage Tiers</h3><p>Hubzero provides three storage tiers: personal workspace (50 GB), project storage (500 GB shared), and archival storage (unlimited, read-only after publication).</p><h3>Sharing</h3><p>Share datasets within groups, with specific collaborators, or publicly. Each sharing level supports granular permissions (read, write, admin).</p><h3>DOI Assignment</h3><p>Published datasets automatically receive a DOI through our DataCite partnership. Include the DOI in your publications for proper attribution.</p>',
            'pub_up'   => -120,
            'pub_down' => null,
            'hits'     => 738,
            'checkout' => 0,
            'alias_by' => '',
        ],
        [
            'title'    => 'Community Spotlight: Nano-Bio Interface Research',
            'cat'      => 'research',
            'state'    => 1,
            'access'   => 1,
            'featured' => 0,
            'author'   => 3,
            'intro'    => '<p>Professor Sarah Chen\'s research group at the Nano-Bio Interface Lab has been using Hubzero tools to study nanoparticle interactions with biological membranes. Their work, published in Nature Nanotechnology, relied heavily on the NAMD and VMD tools available on the platform.</p>',
            'full'     => '<h3>The Research</h3><p>The team performed over 10,000 MD simulations studying how gold nanoparticles of varying sizes and surface coatings interact with lipid bilayers. The Hubzero computing infrastructure enabled screening of 200+ parameter combinations.</p><h3>Impact</h3><p>Their findings revealed a previously unknown size-dependent mechanism that could improve targeted drug delivery. The work has been cited over 50 times since publication.</p>',
            'pub_up'   => -15,
            'pub_down' => null,
            'hits'     => 312,
            'checkout' => 0,
            'alias_by' => 'Dr. Sarah Chen',
        ],
        [
            'title'    => 'February Maintenance Window Summary',
            'cat'      => 'maintenance',
            'state'    => 1,
            'access'   => 1,
            'featured' => 0,
            'author'   => 0,
            'intro'    => '<p>The scheduled maintenance on February 15, 2026 included database optimization, storage system firmware updates, and security patches. Total downtime was 2 hours 15 minutes, within the planned 4-hour window.</p>',
            'full'     => '<h3>Changes Applied</h3><ul><li>MariaDB upgraded to 10.11.7</li><li>Storage firmware updated on all NFS servers</li><li>OpenSSL patched (CVE-2026-0198)</li><li>Tool container images rebuilt with security updates</li></ul><h3>Known Issues</h3><p>Some users may need to clear their browser cache to see updated CSS styles in the admin interface.</p>',
            'pub_up'   => -20,
            'pub_down' => null,
            'hits'     => 189,
            'checkout' => 0,
            'alias_by' => '',
        ],
        [
            'title'    => 'Introduction to Python for Scientific Computing',
            'cat'      => 'getting-started',
            'state'    => 1,
            'access'   => 1,
            'featured' => 0,
            'author'   => 1,
            'intro'    => '<p>Python has become the lingua franca of scientific computing. This beginner-friendly tutorial covers setting up a Python environment on Hubzero, essential libraries (NumPy, SciPy, Matplotlib), and running your first data analysis notebook.</p>',
            'full'     => '<h3>Setting Up</h3><p>Launch the Jupyter tool from the tool catalog. It comes pre-installed with Python 3.11, NumPy 1.26, SciPy 1.12, and Matplotlib 3.8.</p><h3>Your First Notebook</h3><p>Create a new notebook and try importing numpy: <code>import numpy as np</code>. If it loads without error, your environment is ready.</p><h3>Example: Curve Fitting</h3><p>We\'ll fit a Gaussian function to noisy experimental data using scipy.optimize.curve_fit. This example demonstrates loading data, defining a model function, performing the fit, and visualizing results.</p>',
            'pub_up'   => -75,
            'pub_down' => null,
            'hits'     => 1203,
            'checkout' => 0,
            'alias_by' => '',
        ],

        // ── Published, Registered access (access=2) ─────────────────────
        [
            'title'    => 'API Developer Guide: Authentication and Endpoints',
            'cat'      => 'api-reference',
            'state'    => 1,
            'access'   => 2,
            'featured' => 0,
            'author'   => 0,
            'intro'    => '<p>This guide covers the Hubzero REST API authentication flow, available endpoints, rate limiting policies, and example requests using curl and Python. API access requires a registered account with an active API token.</p>',
            'full'     => '<h3>Authentication</h3><p>Generate an API token from Account Settings → Developer → API Tokens. Include the token in your request headers: <code>Authorization: Bearer YOUR_TOKEN</code>.</p><h3>Rate Limits</h3><p>Standard accounts: 100 requests/minute. Research accounts: 1000 requests/minute. Contact support for higher limits.</p><h3>Endpoints</h3><p>Base URL: <code>https://hub.example.edu/api/v2/</code>. Available resources: /users, /groups, /resources, /publications, /tools, /projects.</p>',
            'pub_up'   => -100,
            'pub_down' => null,
            'hits'     => 567,
            'checkout' => 0,
            'alias_by' => '',
        ],
        [
            'title'    => 'Advanced Tool Container Configuration',
            'cat'      => 'developer-guide',
            'state'    => 1,
            'access'   => 2,
            'featured' => 0,
            'author'   => 4,
            'intro'    => '<p>Tool developers can customize their container environments beyond the default configuration. This guide covers installing additional packages, configuring GPU access, setting up custom build pipelines, and optimizing container startup time.</p>',
            'full'     => '<h3>Custom Packages</h3><p>Add packages to your tool\'s Dockerfile. The build system supports apt-get, pip, conda, and spack package managers.</p><h3>GPU Configuration</h3><p>Request GPU access in your tool\'s invoke script with <code>--gpus=1</code>. CUDA 12.3 and cuDNN 8.9 are available in GPU-enabled containers.</p>',
            'pub_up'   => -50,
            'pub_down' => null,
            'hits'     => 234,
            'checkout' => 0,
            'alias_by' => '',
        ],

        // ── Published, Special access (access=3) ────────────────────────
        [
            'title'    => 'Infrastructure Runbook: Emergency Procedures',
            'cat'      => 'admin-guide',
            'state'    => 1,
            'access'   => 3,
            'featured' => 0,
            'author'   => 0,
            'intro'    => '<p>This runbook documents emergency procedures for platform administrators including service restart sequences, database failover steps, and incident communication protocols. Access restricted to platform administrators.</p>',
            'full'     => '<h3>Service Restart Order</h3><ol><li>Database (primary, then replicas)</li><li>Cache layer (Redis, Memcached)</li><li>Web servers (rolling restart)</li><li>Tool execution nodes</li><li>Background job workers</li></ol><h3>Database Failover</h3><p>If the primary database becomes unresponsive, promote replica-02 using the automated failover script at /opt/hubzero/scripts/db-failover.sh.</p>',
            'pub_up'   => -200,
            'pub_down' => null,
            'hits'     => 42,
            'checkout' => 0,
            'alias_by' => '',
        ],

        // ── Unpublished (state=0) ───────────────────────────────────────
        [
            'title'    => 'Draft: Summer Workshop 2026 Announcement',
            'cat'      => 'news',
            'state'    => 0,
            'access'   => 1,
            'featured' => 0,
            'author'   => 1,
            'intro'    => '<p>Join us for the 12th Annual Hubzero Summer Workshop, July 14-18, 2026. This year\'s theme focuses on AI-assisted scientific workflows and next-generation tool development. Registration opens April 1.</p>',
            'full'     => '<h3>Workshop Tracks</h3><ul><li>Track A: Machine Learning for Simulation</li><li>Track B: Tool Development with Containers</li><li>Track C: Data Science and Visualization</li><li>Track D: Platform Administration</li></ul><h3>Keynote Speakers</h3><p>[TBD - confirming speakers]</p>',
            'pub_up'   => -5,
            'pub_down' => null,
            'hits'     => 3,
            'checkout' => 0,
            'alias_by' => '',
        ],
        [
            'title'    => 'Draft: Updated Privacy Policy',
            'cat'      => 'legal',
            'state'    => 0,
            'access'   => 1,
            'featured' => 0,
            'author'   => 0,
            'intro'    => '<p>This draft updates our privacy policy to comply with the 2026 amendments to the FERPA regulations regarding cloud-based educational platforms. Review by legal counsel is pending.</p>',
            'full'     => '<h3>Key Changes</h3><ul><li>Section 4.2: Updated data retention timelines</li><li>Section 5.1: Added cloud storage provider disclosure</li><li>Section 7.3: New opt-out mechanism for analytics</li></ul><p><strong>Note: This is a draft. Do not publish until legal review is complete.</strong></p>',
            'pub_up'   => -10,
            'pub_down' => null,
            'hits'     => 0,
            'checkout' => 0,
            'alias_by' => '',
        ],
        [
            'title'    => 'Draft: New Contributor Guidelines',
            'cat'      => 'documentation',
            'state'    => 0,
            'access'   => 1,
            'featured' => 0,
            'author'   => 2,
            'intro'    => '<p>Guidelines for contributing tools, datasets, and educational content to the Hubzero platform. Covers submission requirements, review process, and licensing considerations.</p>',
            'full'     => '<p>Content still being drafted. Target completion: March 2026.</p>',
            'pub_up'   => -3,
            'pub_down' => null,
            'hits'     => 1,
            'checkout' => 0,
            'alias_by' => '',
        ],

        // ── Archived (state=2) ──────────────────────────────────────────
        [
            'title'    => 'Summer 2024 Workshop Recap',
            'cat'      => 'news',
            'state'    => 2,
            'access'   => 1,
            'featured' => 0,
            'author'   => 1,
            'intro'    => '<p>The 10th Annual Hubzero Summer Workshop brought together 150 researchers, developers, and administrators from 45 institutions. This recap covers highlights, presentation recordings, and attendee feedback.</p>',
            'full'     => '<h3>Highlights</h3><p>Keynote by Dr. James Mitchell on "The Future of Scientific Cyberinfrastructure" was the highest-rated session. The tool development hackathon produced 8 new tools that are now in the review pipeline.</p><h3>Recordings</h3><p>All session recordings are available in the workshop group.</p>',
            'pub_up'   => -600,
            'pub_down' => null,
            'hits'     => 891,
            'checkout' => 0,
            'alias_by' => '',
        ],
        [
            'title'    => 'Deprecated: Legacy Tool Migration Guide',
            'cat'      => 'documentation',
            'state'    => 2,
            'access'   => 1,
            'featured' => 0,
            'author'   => 0,
            'intro'    => '<p>This guide covered migrating tools from the legacy Rappture framework to the new container-based system. The migration deadline has passed and this article is archived for reference only.</p>',
            'full'     => '<p>All legacy tools have been migrated as of December 2025. This guide is preserved for historical reference. For current tool development documentation, see the Developer Guide.</p>',
            'pub_up'   => -400,
            'pub_down' => null,
            'hits'     => 1456,
            'checkout' => 0,
            'alias_by' => '',
        ],
        [
            'title'    => 'Archive: 2023 Usage Statistics Report',
            'cat'      => 'research',
            'state'    => 2,
            'access'   => 1,
            'featured' => 0,
            'author'   => 3,
            'intro'    => '<p>Annual usage statistics for the Hubzero platform during calendar year 2023. Covers user growth, tool launches, storage consumption, and publication metrics.</p>',
            'full'     => '<h3>Key Metrics</h3><ul><li>Total registered users: 12,847 (+18% YoY)</li><li>Active monthly users: 3,421</li><li>Tool launches: 287,000</li><li>Published datasets: 456</li><li>Total storage used: 45 TB</li></ul>',
            'pub_up'   => -450,
            'pub_down' => null,
            'hits'     => 567,
            'checkout' => 0,
            'alias_by' => '',
        ],

        // ── Trashed (state=-2) ──────────────────────────────────────────
        [
            'title'    => 'Test Article - Please Ignore',
            'cat'      => 'uncategorised',
            'state'    => -2,
            'access'   => 1,
            'featured' => 0,
            'author'   => 2,
            'intro'    => '<p>This is a test article created during development. It should be deleted.</p>',
            'full'     => '',
            'pub_up'   => -30,
            'pub_down' => null,
            'hits'     => 0,
            'checkout' => 0,
            'alias_by' => '',
        ],
        [
            'title'    => 'Duplicate: Getting Started Guide (old version)',
            'cat'      => 'uncategorised',
            'state'    => -2,
            'access'   => 1,
            'featured' => 0,
            'author'   => 0,
            'intro'    => '<p>This was the old version of the getting started guide. Replaced by the updated version.</p>',
            'full'     => '<p>Outdated content removed.</p>',
            'pub_up'   => -365,
            'pub_down' => null,
            'hits'     => 234,
            'checkout' => 0,
            'alias_by' => '',
        ],
        [
            'title'    => 'Accidental Post - Wrong Hub',
            'cat'      => 'news',
            'state'    => -2,
            'access'   => 1,
            'featured' => 0,
            'author'   => 4,
            'intro'    => '<p>This article was posted to the wrong hub instance. Moved to trash.</p>',
            'full'     => '',
            'pub_up'   => -14,
            'pub_down' => null,
            'hits'     => 5,
            'checkout' => 0,
            'alias_by' => '',
        ],

        // ── Scheduled future publish ────────────────────────────────────
        [
            'title'    => 'March 2026 Platform Release Notes',
            'cat'      => 'news',
            'state'    => 1,
            'access'   => 1,
            'featured' => 0,
            'author'   => 0,
            'intro'    => '<p>The March 2026 release includes PHP 8.3 upgrade, new Blade-based admin views for all remaining components, and a redesigned media manager with drag-and-drop uploads.</p>',
            'full'     => '<h3>PHP 8.3</h3><p>The platform has been upgraded to PHP 8.3.4 with JIT compilation enabled. Performance benchmarks show 15% improvement in API response times.</p><h3>Admin Views</h3><p>All admin components now use the modern daisyUI Blade templates. The legacy Kameleon templates remain available as a fallback.</p>',
            'pub_up'   => 7,
            'pub_down' => null,
            'hits'     => 0,
            'checkout' => 0,
            'alias_by' => '',
        ],

        // ── Publish window (has publish_down) ───────────────────────────
        [
            'title'    => 'Spring Break Hours: March 10-14',
            'cat'      => 'maintenance',
            'state'    => 1,
            'access'   => 1,
            'featured' => 0,
            'author'   => 0,
            'intro'    => '<p>Support response times may be extended during spring break (March 10-14). Emergency issues will be handled within 4 hours. Non-critical tickets will be addressed when staff return on March 17.</p>',
            'full'     => '',
            'pub_up'   => -2,
            'pub_down' => 5,
            'hits'     => 67,
            'checkout' => 0,
            'alias_by' => '',
        ],
        [
            'title'    => 'Limited-Time: Free GPU Hours for New Users',
            'cat'      => 'news',
            'state'    => 1,
            'access'   => 1,
            'featured' => 1,
            'author'   => 0,
            'intro'    => '<p>New users who register before March 31, 2026 receive 100 free GPU compute hours. Use them for machine learning training, molecular dynamics, or any GPU-accelerated tool on the platform.</p>',
            'full'     => '<h3>How to Claim</h3><p>GPU hours are automatically added to your account upon registration. Check your quota in Account Settings → Computing Resources.</p><h3>Eligible Tools</h3><p>Any tool with GPU support enabled: TensorFlow, PyTorch, NAMD (GPU), LAMMPS (GPU), and more.</p>',
            'pub_up'   => -10,
            'pub_down' => 22,
            'hits'     => 445,
            'checkout' => 0,
            'alias_by' => '',
        ],

        // ── Checked-out article ─────────────────────────────────────────
        [
            'title'    => 'Tool Development Quickstart',
            'cat'      => 'developer-guide',
            'state'    => 1,
            'access'   => 2,
            'featured' => 0,
            'author'   => 1,
            'intro'    => '<p>Get your first tool running on Hubzero in under an hour. This quickstart covers creating a tool project, writing the invoke script, building the container, testing locally, and publishing to the tool catalog.</p>',
            'full'     => '<h3>Prerequisites</h3><p>You need a registered account with tool developer privileges. Request access from your group manager or submit a support ticket.</p><h3>Create a Tool Project</h3><p>Navigate to Developer → New Tool. Fill in the tool name, description, and select a base container image.</p>',
            'pub_up'   => -40,
            'pub_down' => null,
            'hits'     => 389,
            'checkout' => 1,
            'alias_by' => '',
        ],

        // ── More variety in categories ──────────────────────────────────
        [
            'title'    => 'Parallel Computing with MPI on Hubzero',
            'cat'      => 'parallel-computing',
            'state'    => 1,
            'access'   => 1,
            'featured' => 0,
            'author'   => 4,
            'intro'    => '<p>Learn how to run MPI-parallel simulations on Hubzero\'s computing cluster. This tutorial covers MPI basics, writing a parallel job script, submitting to the batch queue, and analyzing parallel performance.</p>',
            'full'     => '<h3>MPI Setup</h3><p>MPI is pre-installed in all tool containers. Use <code>mpirun -np 4 your_program</code> for testing, or submit a batch job for production runs.</p><h3>Batch Submission</h3><p>Create a PBS script requesting the desired number of nodes and cores.</p>',
            'pub_up'   => -55,
            'pub_down' => null,
            'hits'     => 278,
            'checkout' => 0,
            'alias_by' => '',
        ],
        [
            'title'    => 'Machine Learning Tutorial: Image Classification',
            'cat'      => 'machine-learning',
            'state'    => 1,
            'access'   => 1,
            'featured' => 0,
            'author'   => 3,
            'intro'    => '<p>Build and train a convolutional neural network for image classification using PyTorch on Hubzero. This hands-on tutorial uses the CIFAR-10 dataset and covers data loading, model architecture, training loops, and evaluation.</p>',
            'full'     => '<h3>Environment Setup</h3><p>Launch the PyTorch tool from the catalog. It includes PyTorch 2.2, torchvision, and CUDA support for GPU training.</p><h3>Model Architecture</h3><p>We\'ll build a simple CNN with 3 convolutional layers, batch normalization, and dropout. The model achieves ~85% accuracy on CIFAR-10 after 20 epochs of training.</p>',
            'pub_up'   => -25,
            'pub_down' => null,
            'hits'     => 534,
            'checkout' => 0,
            'alias_by' => '',
        ],
        [
            'title'    => 'Data Analysis with R Studio on Hubzero',
            'cat'      => 'data-analysis',
            'state'    => 1,
            'access'   => 1,
            'featured' => 0,
            'author'   => 2,
            'intro'    => '<p>R Studio is available as a tool on Hubzero with a comprehensive set of pre-installed packages for statistical analysis, visualization, and report generation. This guide covers launching R Studio, importing data, and creating publication-quality plots.</p>',
            'full'     => '<h3>Launching R Studio</h3><p>Find R Studio in the tool catalog and click Launch. The environment includes R 4.3 with tidyverse, ggplot2, shiny, and 200+ additional packages.</p><h3>Importing Data</h3><p>Upload your data files through the file manager or use <code>read.csv()</code> to load files from your workspace.</p>',
            'pub_up'   => -35,
            'pub_down' => null,
            'hits'     => 412,
            'checkout' => 0,
            'alias_by' => '',
        ],

        // ── Article with created_by_alias ───────────────────────────────
        [
            'title'    => 'Guest Post: Open Science and Reproducibility',
            'cat'      => 'research',
            'state'    => 1,
            'access'   => 1,
            'featured' => 0,
            'author'   => 0,
            'intro'    => '<p>Open science practices are transforming how research is conducted and shared. This guest post by the Center for Open Science discusses the role of platforms like Hubzero in enabling reproducible computational research.</p>',
            'full'     => '<h3>The Reproducibility Challenge</h3><p>A 2024 survey found that 68% of computational researchers could not reproduce results from published papers, even with access to the same code and data. The missing ingredients are usually the software environment and configuration.</p><h3>How Hubzero Helps</h3><p>Hubzero\'s containerized tools capture the complete software environment. When a tool is published with a DOI, anyone can launch the exact same environment and reproduce the results.</p>',
            'pub_up'   => -18,
            'pub_down' => null,
            'hits'     => 267,
            'checkout' => 0,
            'alias_by' => 'Center for Open Science',
        ],

        // ── Unpublished + Special access ────────────────────────────────
        [
            'title'    => 'Draft: Internal Security Audit Report Q1 2026',
            'cat'      => 'admin-guide',
            'state'    => 0,
            'access'   => 3,
            'featured' => 0,
            'author'   => 0,
            'intro'    => '<p>Quarterly security audit findings for January-March 2026. Covers vulnerability scanning results, penetration testing summary, and remediation status. CONFIDENTIAL - administrators only.</p>',
            'full'     => '<p>[Audit details redacted for seed data.]</p>',
            'pub_up'   => -1,
            'pub_down' => null,
            'hits'     => 0,
            'checkout' => 0,
            'alias_by' => '',
        ],
    ];
}
