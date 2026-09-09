#!/usr/bin/env php
<?php

/**
 * Seed plugin content (reviews, questions, answers) for com_resources.
 * Must be run AFTER seed.php which creates the resources themselves.
 *
 * Usage:  php core/components/com_resources/seed-plugins.php
 *         php core/components/com_resources/seed-plugins.php --down
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

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
$PLUGIN_MARKER = '[SEED2-PLG]';

if (in_array('--down', $argv)) {
    seedDown($db, $prefix, $SEED_MARKER, $PLUGIN_MARKER);
    echo "Plugin seed data removed.\n";
    exit(0);
}

seedUp($db, $prefix, $SEED_MARKER, $PLUGIN_MARKER);
echo "Plugin seed data inserted.\n";
exit(0);

// ═══════════════════════════════════════════════════════════════════════════

function seedUp(PDO $db, string $prefix, string $marker, string $plgMarker): void
{
    // Check if already seeded
    $stmt = $db->query(
        "SELECT COUNT(*) FROM `{$prefix}resource_ratings`
         WHERE `comment` LIKE '{$plgMarker} %'"
    );
    if ((int) $stmt->fetchColumn() > 0) {
        echo "Already seeded — skipping. Use --down first to re-seed.\n";
        return;
    }

    // Get seeded resource IDs
    $stmt = $db->query(
        "SELECT `id`, `alias`, `type` FROM `{$prefix}resources`
         WHERE `title` LIKE '{$marker} %' AND `published` = 1 AND `standalone` = 1
         ORDER BY `id`"
    );
    $resources = $stmt->fetchAll();
    if (empty($resources)) {
        fwrite(STDERR, "No seeded resources found — run seed.php first.\n");
        exit(1);
    }

    $resourceMap = [];
    foreach ($resources as $r) {
        $resourceMap[$r->alias] = $r;
    }

    // Get users
    $stmt = $db->query(
        "SELECT `id`, `name` FROM `{$prefix}users` ORDER BY `id` ASC LIMIT 20"
    );
    $users = $stmt->fetchAll();
    $userIds = array_column($users, 'id');

    $now = date('Y-m-d H:i:s');

    // ── Reviews ───────────────────────────────────────────────────────────
    $reviewCount = addReviews($db, $prefix, $resourceMap, $userIds, $plgMarker);
    echo "  + {$reviewCount} reviews\n";

    // ── Questions & Answers ───────────────────────────────────────────────
    $questionCount = addQuestions($db, $prefix, $resourceMap, $userIds, $plgMarker);
    echo "  + {$questionCount} questions with answers\n";

    // ── Citations ────────────────────────────────────────────────────────
    $citationCount = addCitations($db, $prefix, $resourceMap, $userIds, $plgMarker);
    echo "  + {$citationCount} citations\n";

    // Update resource rating averages
    updateRatingAverages($db, $prefix, $resourceMap);
    echo "  + Updated rating averages\n";
}

function seedDown(PDO $db, string $prefix, string $marker, string $plgMarker): void
{
    // Remove reviews
    $db->exec(
        "DELETE FROM `{$prefix}resource_ratings`
         WHERE `comment` LIKE '{$plgMarker} %'"
    );
    echo "  - Removed seed reviews\n";

    // Remove review comments (item_comments for reviews)
    // Get resource IDs first
    $stmt = $db->query(
        "SELECT `id` FROM `{$prefix}resources` WHERE `title` LIKE '{$marker} %'"
    );
    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($ids)) {
        $idList = implode(',', array_map('intval', $ids));
        $db->exec(
            "DELETE FROM `{$prefix}item_comments`
             WHERE `item_type` = 'review'
               AND `content` LIKE '{$plgMarker} %'"
        );
    }
    echo "  - Removed seed review replies\n";

    // Remove questions (find by tag association)
    $db->exec(
        "DELETE FROM `{$prefix}answers_questions`
         WHERE `question` LIKE '{$plgMarker} %'"
    );
    echo "  - Removed seed questions\n";

    // Remove answers
    $db->exec(
        "DELETE FROM `{$prefix}answers_responses`
         WHERE `answer` LIKE '{$plgMarker} %'"
    );
    echo "  - Removed seed answers\n";

    // Remove citations
    $stmt = $db->query(
        "SELECT `id` FROM `{$prefix}citations`
         WHERE `note` LIKE '{$plgMarker}%'"
    );
    $citIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (!empty($citIds)) {
        $citIdList = implode(',', array_map('intval', $citIds));
        $db->exec("DELETE FROM `{$prefix}citations_assoc` WHERE `cid` IN ({$citIdList})");
        $db->exec("DELETE FROM `{$prefix}citations_authors` WHERE `cid` IN ({$citIdList})");
        $db->exec("DELETE FROM `{$prefix}citations` WHERE `id` IN ({$citIdList})");
    }
    echo "  - Removed seed citations\n";

    // Remove tags we created for question associations (normalized: no colons/hyphens)
    $stmt = $db->query(
        "SELECT `id` FROM `{$prefix}tags`
         WHERE (`tag` LIKE 'resource%' OR `tag` LIKE 'tool%')
           AND `description` LIKE '{$plgMarker}%'"
    );
    $tagIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (!empty($tagIds)) {
        $tagIdList = implode(',', array_map('intval', $tagIds));
        $db->exec("DELETE FROM `{$prefix}tags_object` WHERE `tagid` IN ({$tagIdList})");
        $db->exec("DELETE FROM `{$prefix}tags` WHERE `id` IN ({$tagIdList})");
    }
    echo "  - Removed seed tags\n";

    // Reset rating averages
    if (!empty($ids)) {
        $db->exec(
            "UPDATE `{$prefix}resources`
             SET `rating` = 0, `times_rated` = 0
             WHERE `id` IN ({$idList})"
        );
    }
    echo "  - Reset rating averages\n";
}

// ═══════════════════════════════════════════════════════════════════════════

function addReviews(PDO $db, string $prefix, array $resources, array $userIds, string $plg): int
{
    $pastDate = fn(int $daysAgo) => date('Y-m-d H:i:s', strtotime("-{$daysAgo} days"));

    $reviews = [
        // Neural Network Fundamentals — 3 reviews
        [
            'alias' => 'neural-network-fundamentals',
            'items' => [
                [
                    'user_idx' => 5, 'rating' => 5.0, 'days_ago' => 120,
                    'comment' => "{$plg} Excellent introduction to neural networks. The backpropagation section was particularly clear. I finally understood how gradient descent actually works in multi-layer networks. Highly recommend for anyone starting in ML.",
                    'anonymous' => 0,
                    'replies' => [
                        ['user_idx' => 2, 'days_ago' => 118, 'content' => "{$plg} Agreed! The visualization of gradient flow was very helpful."],
                    ],
                ],
                [
                    'user_idx' => 8, 'rating' => 4.0, 'days_ago' => 90,
                    'comment' => "{$plg} Good overview but could use more practical examples. The theory is solid but I would have liked to see a hands-on coding exercise with PyTorch or TensorFlow to cement the concepts.",
                    'anonymous' => 0,
                    'replies' => [],
                ],
                [
                    'user_idx' => 12, 'rating' => 5.0, 'days_ago' => 45,
                    'comment' => "{$plg} Used this seminar to prepare my students for the advanced ML course. The pacing is perfect for graduate-level audiences. The section on universal approximation theorem was the best explanation I have seen.",
                    'anonymous' => 0,
                    'replies' => [
                        ['user_idx' => 6, 'days_ago' => 43, 'content' => "{$plg} Which course are you teaching? I am looking for prerequisites to recommend for my Deep Learning seminar."],
                        ['user_idx' => 12, 'days_ago' => 42, 'content' => "{$plg} CS 578 at Purdue. This resource and the MPI guide both work well as prep materials."],
                    ],
                ],
            ],
        ],
        // HPC Cluster Workshop — 2 reviews
        [
            'alias' => 'hpc-cluster-workshop',
            'items' => [
                [
                    'user_idx' => 7, 'rating' => 4.0, 'days_ago' => 60,
                    'comment' => "{$plg} Very practical workshop. The SLURM job submission exercises were exactly what I needed. One minor issue: the MPI examples assume OpenMPI but our cluster runs MPICH, so some commands differ.",
                    'anonymous' => 0,
                    'replies' => [
                        ['user_idx' => 3, 'days_ago' => 58, 'content' => "{$plg} We are working on adding MPICH-specific notes to the next version. Thanks for the feedback!"],
                    ],
                ],
                [
                    'user_idx' => 10, 'rating' => 5.0, 'days_ago' => 30,
                    'comment' => "{$plg} This is the best HPC onboarding material I have found. Went from zero cluster experience to running 256-core parallel jobs in three days. The profiling section saved me weeks of optimization work.",
                    'anonymous' => 0,
                    'replies' => [],
                ],
            ],
        ],
        // MPI Programming Guide — 4 reviews (heavily used document)
        [
            'alias' => 'mpi-programming-guide',
            'items' => [
                [
                    'user_idx' => 4, 'rating' => 5.0, 'days_ago' => 300,
                    'comment' => "{$plg} The definitive MPI reference on this platform. I keep coming back to the chapter on derived datatypes whenever I need to send complex structures between processes. Clear examples in both C and Fortran.",
                    'anonymous' => 0,
                    'replies' => [],
                ],
                [
                    'user_idx' => 9, 'rating' => 4.0, 'days_ago' => 200,
                    'comment' => "{$plg} Solid guide. Would appreciate more coverage of MPI-3 features like shared memory windows and neighborhood collectives. The one-sided communication chapter is good but could use a performance comparison with two-sided.",
                    'anonymous' => 0,
                    'replies' => [
                        ['user_idx' => 5, 'days_ago' => 198, 'content' => "{$plg} +1 for MPI-3 coverage. The RMA chapter especially needs updating for the latest implementations."],
                    ],
                ],
                [
                    'user_idx' => 11, 'rating' => 5.0, 'days_ago' => 100,
                    'comment' => "{$plg} I assign this as required reading for my parallel computing course. Students consistently rate it as the most helpful resource. The progression from point-to-point to collectives to advanced topics is well structured.",
                    'anonymous' => 0,
                    'replies' => [],
                ],
                [
                    'user_idx' => 14, 'rating' => 5.0, 'days_ago' => 15,
                    'comment' => "{$plg} Still the gold standard for MPI documentation. Used it to optimize our molecular dynamics code and achieved 95% parallel efficiency on 1024 cores. The I/O chapter was crucial for our large dataset handling.",
                    'anonymous' => 0,
                    'replies' => [],
                ],
            ],
        ],
        // NanoSim — 3 reviews
        [
            'alias' => 'nanosim-molecular-dynamics',
            'items' => [
                [
                    'user_idx' => 6, 'rating' => 4.0, 'days_ago' => 250,
                    'comment' => "{$plg} NanoSim has been our group's go-to tool for quick MD feasibility studies. The web interface is intuitive and the real-time visualization is a game changer for presentations. Deducting one star because the Tersoff potential implementation has some edge cases with mixed systems.",
                    'anonymous' => 0,
                    'replies' => [
                        ['user_idx' => 4, 'days_ago' => 248, 'content' => "{$plg} Can you file a bug report about the Tersoff edge cases? We would like to fix them in the next release."],
                        ['user_idx' => 6, 'days_ago' => 247, 'content' => "{$plg} Done! Ticket #4521. It happens with Si-Ge mixed systems above 500 atoms."],
                    ],
                ],
                [
                    'user_idx' => 13, 'rating' => 5.0, 'days_ago' => 150,
                    'comment' => "{$plg} I used NanoSim for my thesis research on carbon nanotube mechanical properties. The trajectory analysis tools and RDF computation saved me from writing custom post-processing scripts. Published two papers using data generated with this tool.",
                    'anonymous' => 0,
                    'replies' => [],
                ],
                [
                    'user_idx' => 2, 'rating' => 3.0, 'days_ago' => 50,
                    'comment' => "{$plg} Decent tool for educational purposes but limited for production research. Maximum system size is too small for the biomolecular simulations I need. Would love to see GPU acceleration and larger system support in a future version.",
                    'anonymous' => 1,
                    'replies' => [],
                ],
            ],
        ],
        // Deep Learning Applications — 2 reviews
        [
            'alias' => 'deep-learning-applications',
            'items' => [
                [
                    'user_idx' => 15, 'rating' => 5.0, 'days_ago' => 80,
                    'comment' => "{$plg} Fascinating seminar that bridges ML and materials science beautifully. The section on graph neural networks for molecular property prediction opened up a new research direction for our lab. Presenter was very knowledgeable.",
                    'anonymous' => 0,
                    'replies' => [],
                ],
                [
                    'user_idx' => 7, 'rating' => 4.5, 'days_ago' => 40,
                    'comment' => "{$plg} Great content but the recording quality could be better. Some of the slides with equations were hard to read. The Q&A session at the end was particularly valuable for understanding practical deployment challenges.",
                    'anonymous' => 0,
                    'replies' => [],
                ],
            ],
        ],
        // Climate Model Dataset — 1 review
        [
            'alias' => 'climate-model-dataset-v3',
            'items' => [
                [
                    'user_idx' => 3, 'rating' => 5.0, 'days_ago' => 20,
                    'comment' => "{$plg} This dataset has been invaluable for our regional climate downscaling project. The 10-ensemble member design provides excellent uncertainty quantification. NetCDF4 format with CF conventions makes integration with our analysis pipeline seamless. Data documentation is thorough.",
                    'anonymous' => 0,
                    'replies' => [],
                ],
            ],
        ],
        // Protein Folding Toolkit — 2 reviews
        [
            'alias' => 'protein-folding-toolkit',
            'items' => [
                [
                    'user_idx' => 8, 'rating' => 4.0, 'days_ago' => 180,
                    'comment' => "{$plg} The AlphaFold integration is brilliant. Being able to generate initial structures and then refine with explicit solvent MD in one workflow is a huge time saver. The AMBER force field support is solid. Would like to see OPLS-AA added in the future.",
                    'anonymous' => 0,
                    'replies' => [],
                ],
                [
                    'user_idx' => 11, 'rating' => 4.0, 'days_ago' => 60,
                    'comment' => "{$plg} Excellent tool for protein structure prediction workflows. The Monte Carlo sampling for loop modeling is particularly well implemented. My only complaint is that the documentation for the GROMACS interface could be more detailed.",
                    'anonymous' => 0,
                    'replies' => [
                        ['user_idx' => 6, 'days_ago' => 58, 'content' => "{$plg} We just published updated GROMACS docs on the wiki page. Let us know if that helps!"],
                    ],
                ],
            ],
        ],
        // Quantum Computing 101 — 1 review
        [
            'alias' => 'quantum-computing-101',
            'items' => [
                [
                    'user_idx' => 15, 'rating' => 4.5, 'days_ago' => 10,
                    'comment' => "{$plg} Well-structured introduction to quantum computing. The lab exercises are the highlight — actually implementing quantum gates and seeing the results on IBM's simulator makes the abstract concepts concrete. The auto-grading is fair and provides good feedback.",
                    'anonymous' => 0,
                    'replies' => [],
                ],
            ],
        ],
    ];

    $insertReview = $db->prepare(
        "INSERT INTO `{$prefix}resource_ratings`
            (`resource_id`, `user_id`, `rating`, `comment`, `created`, `anonymous`, `state`)
         VALUES (?, ?, ?, ?, ?, ?, 1)"
    );

    $insertComment = $db->prepare(
        "INSERT INTO `{$prefix}item_comments`
            (`item_id`, `item_type`, `content`, `created`, `created_by`,
             `modified`, `modified_by`, `anonymous`, `parent`, `state`, `positive`, `negative`, `rating`)
         VALUES (?, 'review', ?, ?, ?, ?, ?, 0, 0, 1, 0, 0, 0)"
    );

    $count = 0;

    foreach ($reviews as $group) {
        $alias = $group['alias'];
        if (!isset($resources[$alias])) {
            continue;
        }
        $resourceId = (int) $resources[$alias]->id;

        foreach ($group['items'] as $item) {
            $userId = $userIds[$item['user_idx'] % count($userIds)];
            $created = $pastDate($item['days_ago']);

            $insertReview->execute([
                $resourceId,
                $userId,
                $item['rating'],
                $item['comment'],
                $created,
                $item['anonymous'] ?? 0,
            ]);
            $reviewId = (int) $db->lastInsertId();
            $count++;

            // Add replies as item_comments
            foreach ($item['replies'] as $reply) {
                $replyUserId = $userIds[$reply['user_idx'] % count($userIds)];
                $replyCreated = $pastDate($reply['days_ago']);
                $insertComment->execute([
                    $reviewId,
                    $reply['content'],
                    $replyCreated,
                    $replyUserId,
                    $replyCreated,
                    $replyUserId,
                ]);
            }
        }
    }

    return $count;
}

function addQuestions(PDO $db, string $prefix, array $resources, array $userIds, string $plg): int
{
    $pastDate = fn(int $daysAgo) => date('Y-m-d H:i:s', strtotime("-{$daysAgo} days"));
    $now = date('Y-m-d H:i:s');

    $questions = [
        // Neural Network Fundamentals
        [
            'alias' => 'neural-network-fundamentals',
            'items' => [
                [
                    'user_idx' => 9, 'days_ago' => 100,
                    'subject' => 'Recommended learning rate for the backpropagation example?',
                    'question' => "{$plg} In the backpropagation section, the slides mention using a learning rate of 0.01 but don't explain how to choose this value. Is there a general heuristic for selecting the initial learning rate for a simple feed-forward network? I tried 0.1 and my loss diverged.",
                    'state' => 0, // open
                    'answers' => [
                        [
                            'user_idx' => 2, 'days_ago' => 98,
                            'answer' => "{$plg} A good starting point is 0.001 for Adam optimizer or 0.01 for SGD with momentum. If your loss diverges, reduce by a factor of 10. You can also use learning rate schedulers — start high and decay. For the specific example in this seminar, 0.01 with SGD works because the problem is convex.",
                            'state' => 1, // accepted
                            'helpful' => 5,
                        ],
                        [
                            'user_idx' => 7, 'days_ago' => 97,
                            'answer' => "{$plg} I had the same issue. Try using the learning rate finder technique (Smith 2017) — sweep from 1e-7 to 1 and plot loss vs. learning rate. Pick the rate where loss is decreasing fastest. Works well for most architectures.",
                            'state' => 0,
                            'helpful' => 3,
                        ],
                    ],
                ],
                [
                    'user_idx' => 14, 'days_ago' => 60,
                    'subject' => 'Prerequisites for this seminar?',
                    'question' => "{$plg} I am an experimental physicist with limited programming experience. The prerequisites say linear algebra and basic calculus. How strong does my Python need to be? Would a basic understanding of NumPy arrays be sufficient or do I need to know classes and OOP?",
                    'state' => 0,
                    'answers' => [
                        [
                            'user_idx' => 5, 'days_ago' => 59,
                            'answer' => "{$plg} Basic NumPy is enough for following along. The seminar doesn't require OOP knowledge. You should be comfortable with array operations, basic plotting with matplotlib, and writing simple functions. If you can work through the first 4 chapters of the \"Python for Scientists\" tutorial on this hub, you will be well prepared.",
                            'state' => 1,
                            'helpful' => 8,
                        ],
                    ],
                ],
            ],
        ],
        // HPC Cluster Workshop
        [
            'alias' => 'hpc-cluster-workshop',
            'items' => [
                [
                    'user_idx' => 11, 'days_ago' => 45,
                    'subject' => 'How to request GPU nodes in SLURM?',
                    'question' => "{$plg} The workshop covers CPU job submission but I need to run CUDA code on GPU nodes. What SLURM directives do I need to add to my submission script to request a specific GPU type (e.g., A100 vs V100)?",
                    'state' => 0,
                    'answers' => [
                        [
                            'user_idx' => 3, 'days_ago' => 44,
                            'answer' => "{$plg} Add these to your SLURM script:\n\n#SBATCH --gres=gpu:1\n#SBATCH --partition=gpu\n\nFor a specific GPU type:\n#SBATCH --gres=gpu:a100:1\n\nYou can check available GPU types with: sinfo -o \"%G %P\" | grep gpu\n\nAlso make sure to load the CUDA module: module load cuda/12.3",
                            'state' => 1,
                            'helpful' => 12,
                        ],
                    ],
                ],
                [
                    'user_idx' => 6, 'days_ago' => 20,
                    'subject' => 'Job array vs MPI for parameter sweeps?',
                    'question' => "{$plg} I need to run the same simulation code with 500 different parameter combinations. Should I use SLURM job arrays or MPI? Each individual run takes about 2 hours on a single core.",
                    'state' => 0,
                    'answers' => [
                        [
                            'user_idx' => 5, 'days_ago' => 19,
                            'answer' => "{$plg} Job arrays are the way to go for embarrassingly parallel parameter sweeps. Use:\n\n#SBATCH --array=1-500\n\nThen read SLURM_ARRAY_TASK_ID in your script to pick the parameter set. MPI would add unnecessary complexity for independent runs. Job arrays also let the scheduler fill in gaps on partially-used nodes more efficiently.",
                            'state' => 1,
                            'helpful' => 7,
                        ],
                        [
                            'user_idx' => 13, 'days_ago' => 18,
                            'answer' => "{$plg} One tip: if you hit the MaxArraySize limit, split into multiple arrays or use a wrapper script. Also consider --array=1-500%50 to limit concurrent jobs to 50 — this is courteous to other users and prevents overloading the file system.",
                            'state' => 0,
                            'helpful' => 4,
                        ],
                    ],
                ],
            ],
        ],
        // NanoSim
        [
            'alias' => 'nanosim-molecular-dynamics',
            'items' => [
                [
                    'user_idx' => 8, 'days_ago' => 130,
                    'subject' => 'Maximum system size for Lennard-Jones simulations?',
                    'question' => "{$plg} What is the maximum number of atoms NanoSim can handle for Lennard-Jones simulations? I am trying to model a 100nm nanoparticle which requires approximately 500,000 atoms. The tool seems to slow down significantly above 50,000 atoms.",
                    'state' => 0,
                    'answers' => [
                        [
                            'user_idx' => 4, 'days_ago' => 128,
                            'answer' => "{$plg} The current web-based version is limited to about 100,000 atoms for LJ simulations due to the browser-based visualization. For larger systems, you can use the command-line backend directly via the hub's terminal tool — it supports up to 10M atoms without visualization. We are working on a server-side rendering mode for the next release that should handle 500K+ in the web interface.",
                            'state' => 1,
                            'helpful' => 6,
                        ],
                    ],
                ],
                [
                    'user_idx' => 15, 'days_ago' => 70,
                    'subject' => 'Exporting trajectory data to LAMMPS format?',
                    'question' => "{$plg} Is there a way to export NanoSim trajectories to LAMMPS dump format? I want to do post-processing with OVITO and it reads LAMMPS format natively.",
                    'state' => 0,
                    'answers' => [
                        [
                            'user_idx' => 4, 'days_ago' => 69,
                            'answer' => "{$plg} Yes! Go to Analysis → Export and select \"LAMMPS dump\" from the format dropdown. You can also export to XYZ and DCD formats. If you need custom columns in the dump file, use the Advanced Export tab where you can select which per-atom properties to include.",
                            'state' => 0,
                            'helpful' => 3,
                        ],
                    ],
                ],
            ],
        ],
        // MPI Programming Guide
        [
            'alias' => 'mpi-programming-guide',
            'items' => [
                [
                    'user_idx' => 10, 'days_ago' => 150,
                    'subject' => 'MPI_Allreduce vs MPI_Reduce + MPI_Bcast?',
                    'question' => "{$plg} Chapter 5 says MPI_Allreduce is preferred over MPI_Reduce followed by MPI_Bcast. Is there actually a performance difference, or is it just convenience? In my benchmarks they seem about the same for small messages.",
                    'state' => 0,
                    'answers' => [
                        [
                            'user_idx' => 5, 'days_ago' => 149,
                            'answer' => "{$plg} For small messages you may not see a difference, but for large messages MPI_Allreduce can use optimized algorithms (like recursive doubling or ring allreduce) that are significantly faster than reduce+bcast. On our 1024-node cluster, MPI_Allreduce is 30-40% faster for messages > 1MB. Always prefer the single collective call — it gives the MPI implementation more freedom to optimize.",
                            'state' => 1,
                            'helpful' => 15,
                        ],
                    ],
                ],
            ],
        ],
        // Protein Folding Toolkit
        [
            'alias' => 'protein-folding-toolkit',
            'items' => [
                [
                    'user_idx' => 13, 'days_ago' => 90,
                    'subject' => 'CHARMM36m force field parameters missing for non-standard residues',
                    'question' => "{$plg} I am trying to simulate a protein with a phosphorylated tyrosine residue but the CHARMM36m force field in the toolkit does not seem to include parameters for PTR. Is there a way to add custom residue parameters, or is this planned for a future update?",
                    'state' => 0,
                    'answers' => [
                        [
                            'user_idx' => 6, 'days_ago' => 88,
                            'answer' => "{$plg} You can add custom parameters by uploading a supplementary .str topology file in the Force Field Settings panel. For phosphorylated residues specifically, download the CHARMM36 PTM stream file from the MacKerell lab website and upload it. We plan to bundle common PTM parameters in the next release (v4.2, expected next month).",
                            'state' => 1,
                            'helpful' => 4,
                        ],
                    ],
                ],
            ],
        ],
    ];

    $insertQuestion = $db->prepare(
        "INSERT INTO `{$prefix}answers_questions`
            (`subject`, `question`, `created`, `created_by`, `state`, `anonymous`, `email`, `helpful`, `reward`, `nothelpful`)
         VALUES (?, ?, ?, ?, ?, 0, 0, 0, 0, 0)"
    );

    $insertAnswer = $db->prepare(
        "INSERT INTO `{$prefix}answers_responses`
            (`question_id`, `answer`, `created_by`, `created`, `helpful`, `nothelpful`, `state`, `anonymous`)
         VALUES (?, ?, ?, ?, ?, 0, ?, 0)"
    );

    $count = 0;

    foreach ($questions as $group) {
        $alias = $group['alias'];
        if (!isset($resources[$alias])) {
            continue;
        }
        $resource = $resources[$alias];
        $resourceId = (int) $resource->id;

        // Determine the tag for this resource
        $tag = ($resource->type == 7)
            ? 'tool:' . $resource->alias
            : 'resource:' . $resourceId;

        // Ensure the tag exists
        $tagId = ensureTag($db, $prefix, $tag, $plg);

        foreach ($group['items'] as $item) {
            $userId = $userIds[$item['user_idx'] % count($userIds)];
            $created = $pastDate($item['days_ago']);

            $insertQuestion->execute([
                $item['subject'],
                $item['question'],
                $created,
                $userId,
                $item['state'],
            ]);
            $questionId = (int) $db->lastInsertId();
            $count++;

            // Tag the question to associate with the resource
            $db->prepare(
                "INSERT INTO `{$prefix}tags_object`
                    (`objectid`, `tagid`, `strength`, `taggerid`, `taggedon`, `tbl`, `label`)
                 VALUES (?, ?, 1, ?, ?, 'answers', '')"
            )->execute([$questionId, $tagId, $userId, $created]);

            // Add answers
            foreach ($item['answers'] as $ans) {
                $ansUserId = $userIds[$ans['user_idx'] % count($userIds)];
                $ansCreated = $pastDate($ans['days_ago']);

                $insertAnswer->execute([
                    $questionId,
                    $ans['answer'],
                    $ansUserId,
                    $ansCreated,
                    $ans['helpful'] ?? 0,
                    $ans['state'] ?? 0,
                ]);
            }
        }
    }

    return $count;
}

function normalizeTag(string $tag): string
{
    return strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $tag));
}

function ensureTag(PDO $db, string $prefix, string $tag, string $plg): int
{
    $normalized = normalizeTag($tag);

    $stmt = $db->prepare(
        "SELECT `id` FROM `{$prefix}tags` WHERE `tag` = ? LIMIT 1"
    );
    $stmt->execute([$normalized]);
    $existing = $stmt->fetchColumn();

    if ($existing) {
        return (int) $existing;
    }

    $now = date('Y-m-d H:i:s');
    $db->prepare(
        "INSERT INTO `{$prefix}tags`
            (`tag`, `raw_tag`, `description`, `admin`, `created`, `created_by`, `objects`, `substitutes`)
         VALUES (?, ?, ?, 0, ?, 0, 0, 0)"
    )->execute([$normalized, $tag, "{$plg} Auto-created for seed data", $now]);

    return (int) $db->lastInsertId();
}

function addCitations(PDO $db, string $prefix, array $resources, array $userIds, string $plg): int
{
    $now = date('Y-m-d H:i:s');

    $citations = [
        // Neural Network Fundamentals — 4 citations
        [
            'alias' => 'neural-network-fundamentals',
            'items' => [
                [
                    'type' => 'journal',
                    'title' => 'Deep Learning',
                    'author' => 'LeCun, Yann and Bengio, Yoshua and Hinton, Geoffrey',
                    'journal' => 'Nature',
                    'year' => '2015',
                    'volume' => '521',
                    'number' => '7553',
                    'pages' => '436-444',
                    'doi' => '10.1038/nature14539',
                    'affiliated' => 1,
                ],
                [
                    'type' => 'inproceedings',
                    'title' => 'ImageNet Classification with Deep Convolutional Neural Networks',
                    'author' => 'Krizhevsky, Alex and Sutskever, Ilya and Hinton, Geoffrey E.',
                    'booktitle' => 'Advances in Neural Information Processing Systems',
                    'year' => '2012',
                    'volume' => '25',
                    'pages' => '1097-1105',
                    'doi' => '10.1145/3065386',
                    'affiliated' => 0,
                ],
                [
                    'type' => 'journal',
                    'title' => 'Gradient-Based Learning Applied to Document Recognition',
                    'author' => 'LeCun, Yann and Bottou, Leon and Bengio, Yoshua and Haffner, Patrick',
                    'journal' => 'Proceedings of the IEEE',
                    'year' => '1998',
                    'volume' => '86',
                    'number' => '11',
                    'pages' => '2278-2324',
                    'doi' => '10.1109/5.726791',
                    'affiliated' => 0,
                ],
                [
                    'type' => 'book',
                    'title' => 'Neural Networks and Deep Learning: A Textbook',
                    'author' => 'Aggarwal, Charu C.',
                    'publisher' => 'Springer',
                    'year' => '2018',
                    'isbn' => '978-3-319-94462-3',
                    'doi' => '10.1007/978-3-319-94463-0',
                    'affiliated' => 0,
                ],
            ],
        ],
        // MPI Programming Guide — 3 citations
        [
            'alias' => 'mpi-programming-guide',
            'items' => [
                [
                    'type' => 'book',
                    'title' => 'Using MPI: Portable Parallel Programming with the Message-Passing Interface',
                    'author' => 'Gropp, William and Lusk, Ewing and Skjellum, Anthony',
                    'publisher' => 'MIT Press',
                    'year' => '2014',
                    'edition' => '3rd',
                    'isbn' => '978-0-262-52739-2',
                    'affiliated' => 1,
                ],
                [
                    'type' => 'techreport',
                    'title' => 'MPI: A Message-Passing Interface Standard Version 4.0',
                    'author' => 'Message Passing Interface Forum',
                    'institution' => 'University of Tennessee',
                    'year' => '2021',
                    'url' => 'https://www.mpi-forum.org/docs/mpi-4.0/mpi40-report.pdf',
                    'affiliated' => 0,
                ],
                [
                    'type' => 'journal',
                    'title' => 'Scaling Parallel Scientific Computation on Multi-core Clusters with MPI',
                    'author' => 'Rabenseifner, Rolf and Hager, Georg and Jost, Gabriele',
                    'journal' => 'Journal of Parallel and Distributed Computing',
                    'year' => '2019',
                    'volume' => '131',
                    'pages' => '42-58',
                    'doi' => '10.1016/j.jpdc.2019.05.004',
                    'affiliated' => 1,
                ],
            ],
        ],
        // NanoSim — 2 citations
        [
            'alias' => 'nanosim-molecular-dynamics',
            'items' => [
                [
                    'type' => 'journal',
                    'title' => 'NanoSim: A Web-Based Molecular Dynamics Simulation Platform for Nanoscale Research',
                    'author' => 'Martinez, Ana and Chen, Wei and Hopper, Grace',
                    'journal' => 'Journal of Computational Physics',
                    'year' => '2023',
                    'volume' => '487',
                    'pages' => '112156',
                    'doi' => '10.1016/j.jcp.2023.112156',
                    'affiliated' => 1,
                ],
                [
                    'type' => 'inproceedings',
                    'title' => 'Carbon Nanotube Mechanical Properties via Interactive Molecular Dynamics',
                    'author' => 'Feynman, Richard P. and Davis, Carol and Martinez, Ana',
                    'booktitle' => 'Proceedings of the International Conference on Computational Nanoscience',
                    'year' => '2024',
                    'pages' => '315-322',
                    'publisher' => 'IEEE',
                    'doi' => '10.1109/ICCN.2024.9987654',
                    'affiliated' => 1,
                ],
            ],
        ],
        // Deep Learning Applications — 2 citations
        [
            'alias' => 'deep-learning-applications',
            'items' => [
                [
                    'type' => 'journal',
                    'title' => 'Graph Neural Networks for Molecular Property Prediction: A Survey',
                    'author' => 'Wu, Zhenqin and Ramsundar, Bharath and Feinberg, Evan N.',
                    'journal' => 'Chemical Science',
                    'year' => '2023',
                    'volume' => '14',
                    'number' => '9',
                    'pages' => '2325-2341',
                    'doi' => '10.1039/D2SC06535A',
                    'affiliated' => 0,
                ],
                [
                    'type' => 'inproceedings',
                    'title' => 'Attention Is All You Need',
                    'author' => 'Vaswani, Ashish and Shazeer, Noam and Parmar, Niki and Uszkoreit, Jakob',
                    'booktitle' => 'Advances in Neural Information Processing Systems',
                    'year' => '2017',
                    'volume' => '30',
                    'pages' => '5998-6008',
                    'affiliated' => 0,
                ],
            ],
        ],
        // Protein Folding Toolkit — 2 citations
        [
            'alias' => 'protein-folding-toolkit',
            'items' => [
                [
                    'type' => 'journal',
                    'title' => 'Highly Accurate Protein Structure Prediction with AlphaFold',
                    'author' => 'Jumper, John and Evans, Richard and Pritzel, Alexander',
                    'journal' => 'Nature',
                    'year' => '2021',
                    'volume' => '596',
                    'pages' => '583-589',
                    'doi' => '10.1038/s41586-021-03819-2',
                    'affiliated' => 0,
                ],
                [
                    'type' => 'journal',
                    'title' => 'Integrated Protein Folding and Refinement Workflows Using Cloud-Based HPC',
                    'author' => 'Chen, Wei and Hopper, Grace and Davis, Carol',
                    'journal' => 'Bioinformatics',
                    'year' => '2024',
                    'volume' => '40',
                    'number' => '3',
                    'pages' => 'btae089',
                    'doi' => '10.1093/bioinformatics/btae089',
                    'affiliated' => 1,
                ],
            ],
        ],
        // Climate Model Dataset — 1 citation
        [
            'alias' => 'climate-model-dataset-v3',
            'items' => [
                [
                    'type' => 'journal',
                    'title' => 'A Multi-Ensemble Climate Dataset for Regional Downscaling Studies',
                    'author' => 'Thompson, Sarah and Turing, Alan and Kisseberth, Nicholas',
                    'journal' => 'Earth System Science Data',
                    'year' => '2025',
                    'volume' => '17',
                    'number' => '1',
                    'pages' => '145-162',
                    'doi' => '10.5194/essd-17-145-2025',
                    'affiliated' => 1,
                ],
            ],
        ],
    ];

    $citFields = [
        'type', 'title', 'author', 'journal', 'booktitle', 'publisher',
        'year', 'volume', 'number', 'pages', 'edition', 'isbn', 'doi',
        'url', 'institution', 'affiliated', 'note',
    ];

    $count = 0;

    foreach ($citations as $group) {
        $alias = $group['alias'];
        if (!isset($resources[$alias])) {
            continue;
        }
        $resourceId = (int) $resources[$alias]->id;

        foreach ($group['items'] as $item) {
            $item['note'] = $plg;

            // Build INSERT dynamically from available fields
            $cols = ['`uid`', '`published`', '`created`'];
            $vals = [$userIds[0], 1, $now];
            $placeholders = ['?', '?', '?'];

            foreach ($citFields as $field) {
                if (isset($item[$field]) && $item[$field] !== '') {
                    $cols[] = "`{$field}`";
                    $vals[] = $item[$field];
                    $placeholders[] = '?';
                }
            }

            $sql = "INSERT INTO `{$prefix}citations` ("
                . implode(', ', $cols) . ") VALUES ("
                . implode(', ', $placeholders) . ")";
            $db->prepare($sql)->execute($vals);
            $citId = (int) $db->lastInsertId();

            // Link citation to resource
            $db->prepare(
                "INSERT INTO `{$prefix}citations_assoc`
                    (`cid`, `oid`, `tbl`, `type`)
                 VALUES (?, ?, 'resource', '')"
            )->execute([$citId, $resourceId]);

            $count++;
        }
    }

    return $count;
}

function updateRatingAverages(PDO $db, string $prefix, array $resources): void
{
    foreach ($resources as $r) {
        $stmt = $db->prepare(
            "SELECT AVG(`rating`) as avg_rating, COUNT(*) as cnt
             FROM `{$prefix}resource_ratings`
             WHERE `resource_id` = ? AND `state` = 1"
        );
        $stmt->execute([$r->id]);
        $row = $stmt->fetch();

        if ($row && $row->cnt > 0) {
            $db->prepare(
                "UPDATE `{$prefix}resources`
                 SET `rating` = ?, `times_rated` = ?
                 WHERE `id` = ?"
            )->execute([
                round($row->avg_rating, 1),
                $row->cnt,
                $r->id,
            ]);
        }
    }
}
