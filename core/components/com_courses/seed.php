#!/usr/bin/env php
<?php

/**
 * Seed com_courses with comprehensive sample data covering all admin views:
 * courses, offerings, sections, units, asset groups, assets, members (students,
 * managers, instructors), roles, enrollment codes, grade policies, grade book
 * entries, pages, announcements, certificates, and forms.
 *
 * Usage:  php core/components/com_courses/seed.php
 *         php core/components/com_courses/seed.php --down   (remove seed data)
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

$SEED_MARKER = '[SEED-CRS]';

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
        "SELECT COUNT(*) FROM `{$prefix}courses` WHERE `title` LIKE '{$marker} %'"
    );
    if ((int) $stmt->fetchColumn() > 0) {
        echo "Already seeded — skipping. Use --down first to re-seed.\n";
        return;
    }

    // ── Look up users ────────────────────────────────────────────────────
    $stmt = $db->query(
        "SELECT `id`, `name`, `email` FROM `{$prefix}users` ORDER BY `id` ASC LIMIT 20"
    );
    $users = $stmt->fetchAll(PDO::FETCH_OBJ);
    if (empty($users)) {
        fwrite(STDERR, "No users found — run user seeding first.\n");
        exit(1);
    }
    $userIds = array_column($users, 'id');
    $userMap = [];
    foreach ($users as $u) {
        $userMap[$u->id] = $u;
    }

    $now = date('Y-m-d H:i:s');
    $pastDate = fn(int $daysAgo) => date('Y-m-d H:i:s', strtotime("-{$daysAgo} days"));
    $futureDate = fn(int $daysAhead) => date('Y-m-d H:i:s', strtotime("+{$daysAhead} days"));
    $pickUser = fn(int $idx) => $userIds[$idx % count($userIds)];

    // ── 1. Roles ─────────────────────────────────────────────────────────
    echo "Creating roles...\n";
    $roles = ensureRoles($db, $prefix);
    echo "  Roles: " . implode(', ', array_keys($roles)) . "\n";

    // ── 2. Grade Policies ────────────────────────────────────────────────
    echo "Creating grade policies...\n";
    $policies = createGradePolicies($db, $prefix);
    echo "  Policies: " . count($policies) . "\n";

    // ── 3. Asset Group Types ─────────────────────────────────────────────
    echo "Creating asset group types...\n";
    ensureAssetGroupTypes($db, $prefix);

    // ── 4. Courses ───────────────────────────────────────────────────────
    echo "Creating courses...\n";
    $courses = getCourseDefinitions($marker);
    $courseIds = [];

    $insertCourse = $db->prepare(
        "INSERT INTO `{$prefix}courses`
            (`alias`, `group_id`, `title`, `state`, `type`, `access`,
             `blurb`, `description`, `logo`, `created`, `created_by`,
             `params`, `length`, `effort`)
         VALUES
            (:alias, :group_id, :title, :state, :type, :access,
             :blurb, :description, '', :created, :created_by,
             '', :length, :effort)"
    );

    foreach ($courses as $key => $c) {
        $insertCourse->execute([
            ':alias'      => $c['alias'],
            ':group_id'   => $c['group_id'] ?? 0,
            ':title'      => $c['title'],
            ':state'      => $c['state'],
            ':type'       => $c['type'] ?? 0,
            ':access'     => $c['access'] ?? 0,
            ':blurb'      => $c['blurb'],
            ':description' => $c['description'],
            ':created'    => $c['created'],
            ':created_by' => $pickUser($c['creator_idx']),
            ':length'     => $c['length'] ?? '',
            ':effort'     => $c['effort'] ?? '',
        ]);
        $courseIds[$key] = (int) $db->lastInsertId();
        echo "  + Course #{$courseIds[$key]}: {$c['title']}\n";
    }

    // ── 5. Offerings ─────────────────────────────────────────────────────
    echo "Creating offerings...\n";
    $offerings = getOfferingDefinitions();
    $offeringIds = [];

    $insertOffering = $db->prepare(
        "INSERT INTO `{$prefix}courses_offerings`
            (`course_id`, `alias`, `title`, `term`, `state`,
             `publish_up`, `publish_down`, `created`, `created_by`, `params`)
         VALUES
            (:course_id, :alias, :title, :term, :state,
             :publish_up, :publish_down, :created, :created_by, '')"
    );

    foreach ($offerings as $key => $o) {
        $courseId = $courseIds[$o['course_key']] ?? null;
        if (!$courseId) {
            continue;
        }
        $insertOffering->execute([
            ':course_id'    => $courseId,
            ':alias'        => $o['alias'],
            ':title'        => $o['title'],
            ':term'         => $o['term'] ?? '',
            ':state'        => $o['state'],
            ':publish_up'   => $o['publish_up'] ?? null,
            ':publish_down' => $o['publish_down'] ?? null,
            ':created'      => $o['created'],
            ':created_by'   => $pickUser($o['creator_idx']),
        ]);
        $offeringIds[$key] = (int) $db->lastInsertId();
        echo "  + Offering #{$offeringIds[$key]}: {$o['title']}\n";
    }

    // ── 6. Sections ──────────────────────────────────────────────────────
    echo "Creating sections...\n";
    $sections = getSectionDefinitions();
    $sectionIds = [];

    $insertSection = $db->prepare(
        "INSERT INTO `{$prefix}courses_offering_sections`
            (`offering_id`, `is_default`, `alias`, `title`, `state`,
             `start_date`, `end_date`, `publish_up`, `publish_down`,
             `created`, `created_by`, `enrollment`, `grade_policy_id`, `params`)
         VALUES
            (:offering_id, :is_default, :alias, :title, :state,
             :start_date, :end_date, :publish_up, :publish_down,
             :created, :created_by, :enrollment, :grade_policy_id, '')"
    );

    foreach ($sections as $key => $s) {
        $offId = $offeringIds[$s['offering_key']] ?? null;
        if (!$offId) {
            continue;
        }
        $policyId = $policies[$s['policy_key'] ?? 'standard'] ?? 1;
        $insertSection->execute([
            ':offering_id'     => $offId,
            ':is_default'      => $s['is_default'] ?? 0,
            ':alias'           => $s['alias'],
            ':title'           => $s['title'],
            ':state'           => $s['state'],
            ':start_date'      => $s['start_date'] ?? null,
            ':end_date'        => $s['end_date'] ?? null,
            ':publish_up'      => $s['publish_up'] ?? null,
            ':publish_down'    => $s['publish_down'] ?? null,
            ':created'         => $s['created'],
            ':created_by'      => $pickUser($s['creator_idx']),
            ':enrollment'      => $s['enrollment'] ?? 0,
            ':grade_policy_id' => $policyId,
        ]);
        $sectionIds[$key] = (int) $db->lastInsertId();
        echo "  + Section #{$sectionIds[$key]}: {$s['title']}\n";
    }

    // ── 7. Units ─────────────────────────────────────────────────────────
    echo "Creating units...\n";
    $units = getUnitDefinitions();
    $unitIds = [];

    $insertUnit = $db->prepare(
        "INSERT INTO `{$prefix}courses_units`
            (`offering_id`, `alias`, `title`, `description`, `ordering`,
             `created`, `created_by`, `state`)
         VALUES
            (:offering_id, :alias, :title, :description, :ordering,
             :created, :created_by, :state)"
    );

    foreach ($units as $key => $u) {
        $offId = $offeringIds[$u['offering_key']] ?? null;
        if (!$offId) {
            continue;
        }
        $insertUnit->execute([
            ':offering_id' => $offId,
            ':alias'       => $u['alias'],
            ':title'       => $u['title'],
            ':description' => $u['description'] ?? '',
            ':ordering'    => $u['ordering'],
            ':created'     => $u['created'],
            ':created_by'  => $pickUser($u['creator_idx']),
            ':state'       => $u['state'],
        ]);
        $unitIds[$key] = (int) $db->lastInsertId();
        echo "  + Unit #{$unitIds[$key]}: {$u['title']}\n";
    }

    // ── 8. Asset Groups ──────────────────────────────────────────────────
    echo "Creating asset groups...\n";
    $assetGroups = getAssetGroupDefinitions();
    $agIds = [];

    $insertAG = $db->prepare(
        "INSERT INTO `{$prefix}courses_asset_groups`
            (`unit_id`, `alias`, `title`, `description`, `ordering`,
             `parent`, `created`, `created_by`, `state`, `params`)
         VALUES
            (:unit_id, :alias, :title, :description, :ordering,
             :parent, :created, :created_by, :state, '')"
    );

    foreach ($assetGroups as $key => $ag) {
        $uId = $unitIds[$ag['unit_key']] ?? null;
        if (!$uId) {
            continue;
        }
        $parentId = 0;
        if (isset($ag['parent_key']) && isset($agIds[$ag['parent_key']])) {
            $parentId = $agIds[$ag['parent_key']];
        }
        $insertAG->execute([
            ':unit_id'     => $uId,
            ':alias'       => $ag['alias'],
            ':title'       => $ag['title'],
            ':description' => $ag['description'] ?? '',
            ':ordering'    => $ag['ordering'],
            ':parent'      => $parentId,
            ':created'     => $ag['created'],
            ':created_by'  => $pickUser($ag['creator_idx'] ?? 0),
            ':state'       => $ag['state'] ?? 1,
        ]);
        $agIds[$key] = (int) $db->lastInsertId();
    }
    echo "  Created " . count($agIds) . " asset groups\n";

    // ── 9. Assets ────────────────────────────────────────────────────────
    echo "Creating assets...\n";
    $assets = getAssetDefinitions();
    $assetIds = [];

    $insertAsset = $db->prepare(
        "INSERT INTO `{$prefix}courses_assets`
            (`title`, `content`, `type`, `subtype`, `url`,
             `created`, `created_by`, `state`, `course_id`,
             `graded`, `grade_weight`, `path`)
         VALUES
            (:title, :content, :type, :subtype, :url,
             :created, :created_by, :state, :course_id,
             :graded, :grade_weight, '')"
    );

    // Also link assets to asset groups
    // The association table doesn't exist in the migration, assets are linked
    // via the asset_group_id concept — but looking at the code, assets connect
    // to asset groups via a separate mapping. For seeding, we set course_id.

    foreach ($assets as $key => $a) {
        $cKey = $a['course_key'];
        $cId = $courseIds[$cKey] ?? null;
        if (!$cId) {
            continue;
        }
        $insertAsset->execute([
            ':title'        => $a['title'],
            ':content'      => $a['content'] ?? '',
            ':type'         => $a['type'],
            ':subtype'      => $a['subtype'] ?? 'file',
            ':url'          => $a['url'] ?? '',
            ':created'      => $a['created'],
            ':created_by'   => $pickUser($a['creator_idx']),
            ':state'        => $a['state'],
            ':course_id'    => $cId,
            ':graded'       => $a['graded'] ?? 0,
            ':grade_weight' => $a['grade_weight'] ?? '',
        ]);
        $assetIds[$key] = (int) $db->lastInsertId();
    }
    echo "  Created " . count($assetIds) . " assets\n";

    // ── 10. Members (managers, instructors, students) ────────────────────
    echo "Creating members...\n";
    $members = getMemberDefinitions();
    $memberIds = [];

    $insertMember = $db->prepare(
        "INSERT INTO `{$prefix}courses_members`
            (`user_id`, `course_id`, `offering_id`, `section_id`, `role_id`,
             `permissions`, `enrolled`, `student`, `first_visit`, `token`)
         VALUES
            (:user_id, :course_id, :offering_id, :section_id, :role_id,
             '', :enrolled, :student, :first_visit, '')"
    );

    foreach ($members as $m) {
        $cId = $courseIds[$m['course_key']] ?? 0;
        $oId = $offeringIds[$m['offering_key'] ?? ''] ?? 0;
        $sId = $sectionIds[$m['section_key'] ?? ''] ?? 0;
        $rId = $roles[$m['role']] ?? 0;
        $uId = $pickUser($m['user_idx']);

        $insertMember->execute([
            ':user_id'     => $uId,
            ':course_id'   => $cId,
            ':offering_id' => $oId,
            ':section_id'  => $sId,
            ':role_id'     => $rId,
            ':enrolled'    => $m['enrolled'],
            ':student'     => $m['student'] ?? 0,
            ':first_visit' => $m['first_visit'] ?? null,
        ]);
        $memberIds[] = (int) $db->lastInsertId();
    }
    echo "  Created " . count($memberIds) . " members\n";

    // ── 11. Enrollment Codes ─────────────────────────────────────────────
    echo "Creating enrollment codes...\n";
    $codes = getCodeDefinitions();
    $codeCount = 0;

    $insertCode = $db->prepare(
        "INSERT INTO `{$prefix}courses_offering_section_codes`
            (`section_id`, `code`, `created`, `created_by`,
             `expires`, `redeemed`, `redeemed_by`)
         VALUES
            (:section_id, :code, :created, :created_by,
             :expires, :redeemed, :redeemed_by)"
    );

    foreach ($codes as $c) {
        $sId = $sectionIds[$c['section_key']] ?? null;
        if (!$sId) {
            continue;
        }
        $insertCode->execute([
            ':section_id'   => $sId,
            ':code'         => $c['code'],
            ':created'      => $c['created'],
            ':created_by'   => $pickUser($c['creator_idx']),
            ':expires'      => $c['expires'] ?? null,
            ':redeemed'     => $c['redeemed'] ?? null,
            ':redeemed_by'  => $c['redeemed_by'] ? $pickUser($c['redeemed_by']) : 0,
        ]);
        $codeCount++;
    }
    echo "  Created {$codeCount} enrollment codes\n";

    // ── 12. Pages ────────────────────────────────────────────────────────
    echo "Creating pages...\n";
    $pages = getPageDefinitions();
    $pageCount = 0;

    $insertPage = $db->prepare(
        "INSERT INTO `{$prefix}courses_pages`
            (`course_id`, `offering_id`, `section_id`, `url`, `title`,
             `content`, `ordering`, `active`, `privacy`)
         VALUES
            (:course_id, :offering_id, :section_id, :url, :title,
             :content, :ordering, :active, :privacy)"
    );

    foreach ($pages as $p) {
        $cId = $courseIds[$p['course_key']] ?? 0;
        $oId = isset($p['offering_key']) ? ($offeringIds[$p['offering_key']] ?? 0) : 0;
        $sId = isset($p['section_key']) ? ($sectionIds[$p['section_key']] ?? 0) : 0;
        $insertPage->execute([
            ':course_id'  => $cId,
            ':offering_id' => $oId,
            ':section_id' => $sId,
            ':url'        => $p['url'],
            ':title'      => $p['title'],
            ':content'    => $p['content'],
            ':ordering'   => $p['ordering'],
            ':active'     => $p['active'] ?? 1,
            ':privacy'    => $p['privacy'] ?? '',
        ]);
        $pageCount++;
    }
    echo "  Created {$pageCount} pages\n";

    // ── 13. Announcements ────────────────────────────────────────────────
    echo "Creating announcements...\n";
    $announcements = getAnnouncementDefinitions();
    $annCount = 0;

    $insertAnn = $db->prepare(
        "INSERT INTO `{$prefix}courses_announcements`
            (`offering_id`, `content`, `priority`, `created`, `created_by`,
             `section_id`, `state`, `publish_up`, `publish_down`, `sticky`)
         VALUES
            (:offering_id, :content, :priority, :created, :created_by,
             :section_id, :state, :publish_up, :publish_down, :sticky)"
    );

    foreach ($announcements as $a) {
        $oId = $offeringIds[$a['offering_key']] ?? null;
        if (!$oId) {
            continue;
        }
        $sId = isset($a['section_key']) ? ($sectionIds[$a['section_key']] ?? 0) : 0;
        $insertAnn->execute([
            ':offering_id'  => $oId,
            ':content'      => $a['content'],
            ':priority'     => $a['priority'] ?? 0,
            ':created'      => $a['created'],
            ':created_by'   => $pickUser($a['creator_idx']),
            ':section_id'   => $sId,
            ':state'        => $a['state'],
            ':publish_up'   => $a['publish_up'] ?? null,
            ':publish_down' => $a['publish_down'] ?? null,
            ':sticky'       => $a['sticky'] ?? 0,
        ]);
        $annCount++;
    }
    echo "  Created {$annCount} announcements\n";

    // ── 14. Certificates ─────────────────────────────────────────────────
    echo "Creating certificates...\n";
    $certCount = 0;
    $insertCert = $db->prepare(
        "INSERT INTO `{$prefix}courses_certificates`
            (`properties`, `course_id`)
         VALUES (:properties, :course_id)"
    );

    foreach (['intro-cs', 'data-science', 'materials-eng'] as $ck) {
        if (isset($courseIds[$ck])) {
            $props = json_encode([
                'elements' => [
                    ['text' => 'name', 'x' => 300, 'y' => 250, 'font' => 'serif', 'size' => 24],
                    ['text' => 'date', 'x' => 300, 'y' => 300, 'font' => 'sans-serif', 'size' => 14],
                    ['text' => 'course', 'x' => 300, 'y' => 200, 'font' => 'sans-serif', 'size' => 18],
                ],
            ]);
            $insertCert->execute([
                ':properties' => $props,
                ':course_id'  => $courseIds[$ck],
            ]);
            $certCount++;
        }
    }
    echo "  Created {$certCount} certificates\n";

    // ── 15. Grade Book Entries ────────────────────────────────────────────
    echo "Creating grade book entries...\n";
    $gbCount = 0;
    $insertGB = $db->prepare(
        "INSERT IGNORE INTO `{$prefix}courses_grade_book`
            (`member_id`, `score`, `scope`, `scope_id`,
             `override`, `score_recorded`, `override_recorded`)
         VALUES
            (:member_id, :score, :scope, :scope_id,
             :override, :score_recorded, :override_recorded)"
    );

    // Assign grades to some students
    foreach ($memberIds as $idx => $memberId) {
        // Only grade student members (every 3rd to simulate partial grading)
        if ($idx % 2 !== 0) {
            continue;
        }
        foreach (array_slice($assetIds, 0, 5) as $aKey => $aId) {
            $score = rand(55, 100) + (rand(0, 99) / 100);
            $override = (rand(0, 10) > 8) ? min(100, $score + 5) : null;
            $insertGB->execute([
                ':member_id'         => $memberId,
                ':score'             => round($score, 2),
                ':scope'             => 'asset',
                ':scope_id'          => $aId,
                ':override'          => $override ? round($override, 2) : null,
                ':score_recorded'    => $now,
                ':override_recorded' => $override ? $now : null,
            ]);
            $gbCount++;
        }
    }
    echo "  Created {$gbCount} grade book entries\n";

    // ── 16. Section Badges ───────────────────────────────────────────────
    echo "Creating section badges...\n";
    $badgeCount = 0;
    $insertBadge = $db->prepare(
        "INSERT INTO `{$prefix}courses_offering_section_badges`
            (`section_id`, `published`, `provider_name`,
             `provider_badge_id`, `img_url`, `criteria_id`)
         VALUES
            (:section_id, :published, 'passport',
             :provider_badge_id, :img_url, 0)"
    );
    $insertCriteria = $db->prepare(
        "INSERT INTO `{$prefix}courses_offering_section_badge_criteria`
            (`text`, `section_badge_id`)
         VALUES (:text, :badge_id)"
    );

    $badgeSections = ['cs-f2025-default', 'ds-s2026-a', 'me-f2025-default'];
    $badgeTexts = [
        'Complete all 4 units with a passing grade of 70% or higher.',
        'Achieve at least 80% on all quizzes and submit the final project.',
        'Pass the midterm and final exams with 75% or higher combined.',
    ];
    foreach ($badgeSections as $i => $sKey) {
        $sId = $sectionIds[$sKey] ?? null;
        if (!$sId) {
            continue;
        }
        $insertBadge->execute([
            ':section_id'        => $sId,
            ':published'         => 1,
            ':provider_badge_id' => 1000 + $i,
            ':img_url'           => '',
        ]);
        $badgeId = (int) $db->lastInsertId();
        $insertCriteria->execute([
            ':text'     => $badgeTexts[$i],
            ':badge_id' => $badgeId,
        ]);
        // Update badge to link criteria
        $criteriaId = (int) $db->lastInsertId();
        $db->exec(
            "UPDATE `{$prefix}courses_offering_section_badges`
             SET `criteria_id` = {$criteriaId} WHERE `id` = {$badgeId}"
        );
        $badgeCount++;
    }
    echo "  Created {$badgeCount} section badges\n";

    // ── 17. Log Entries ──────────────────────────────────────────────────
    echo "Creating log entries...\n";
    $logCount = 0;
    $insertLog = $db->prepare(
        "INSERT INTO `{$prefix}courses_log`
            (`scope_id`, `scope`, `timestamp`, `user_id`,
             `action`, `comments`, `actor_id`)
         VALUES
            (:scope_id, :scope, :timestamp, :user_id,
             :action, :comments, :actor_id)"
    );

    $logActions = ['enroll', 'publish', 'grade', 'unitcomplete', 'login', 'submit'];
    foreach ($courseIds as $cKey => $cId) {
        for ($i = 0; $i < 8; $i++) {
            $insertLog->execute([
                ':scope_id'  => $cId,
                ':scope'     => 'course',
                ':timestamp' => date('Y-m-d H:i:s', strtotime("-" . rand(1, 90) . " days")),
                ':user_id'   => $pickUser(rand(0, 15)),
                ':action'    => $logActions[array_rand($logActions)],
                ':comments'  => '',
                ':actor_id'  => $pickUser(0),
            ]);
            $logCount++;
        }
    }
    echo "  Created {$logCount} log entries\n";

    // ── Summary ──────────────────────────────────────────────────────────
    echo "\n=== Seed Summary ===\n";
    echo "  Courses:      " . count($courseIds) . "\n";
    echo "  Offerings:    " . count($offeringIds) . "\n";
    echo "  Sections:     " . count($sectionIds) . "\n";
    echo "  Units:        " . count($unitIds) . "\n";
    echo "  Asset Groups: " . count($agIds) . "\n";
    echo "  Assets:       " . count($assetIds) . "\n";
    echo "  Members:      " . count($memberIds) . "\n";
    echo "  Codes:        {$codeCount}\n";
    echo "  Pages:        {$pageCount}\n";
    echo "  Announcements:{$annCount}\n";
    echo "  Certificates: {$certCount}\n";
    echo "  Grades:       {$gbCount}\n";
    echo "  Badges:       {$badgeCount}\n";
    echo "  Log entries:  {$logCount}\n";
}

function seedDown(PDO $db, string $prefix, string $marker): void
{
    // Find seed course IDs
    $stmt = $db->query(
        "SELECT `id` FROM `{$prefix}courses` WHERE `title` LIKE '{$marker} %'"
    );
    $courseIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($courseIds)) {
        echo "No seed data found.\n";
        return;
    }

    $idList = implode(',', array_map('intval', $courseIds));

    // Find offering IDs
    $stmt = $db->query(
        "SELECT `id` FROM `{$prefix}courses_offerings` WHERE `course_id` IN ({$idList})"
    );
    $offeringIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $offList = !empty($offeringIds)
        ? implode(',', array_map('intval', $offeringIds))
        : '0';

    // Find section IDs
    $stmt = $db->query(
        "SELECT `id` FROM `{$prefix}courses_offering_sections`
         WHERE `offering_id` IN ({$offList})"
    );
    $sectionIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $secList = !empty($sectionIds)
        ? implode(',', array_map('intval', $sectionIds))
        : '0';

    // Find unit IDs
    $stmt = $db->query(
        "SELECT `id` FROM `{$prefix}courses_units` WHERE `offering_id` IN ({$offList})"
    );
    $unitIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $unitList = !empty($unitIds)
        ? implode(',', array_map('intval', $unitIds))
        : '0';

    // Find member IDs
    $stmt = $db->query(
        "SELECT `id` FROM `{$prefix}courses_members` WHERE `course_id` IN ({$idList})"
    );
    $memberIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $memList = !empty($memberIds)
        ? implode(',', array_map('intval', $memberIds))
        : '0';

    // Delete in reverse order of dependencies
    $db->exec("DELETE FROM `{$prefix}courses_grade_book` WHERE `member_id` IN ({$memList})");
    $db->exec("DELETE FROM `{$prefix}courses_member_badges` WHERE `member_id` IN ({$memList})");

    // Badge criteria and badges
    $stmt = $db->query(
        "SELECT `id` FROM `{$prefix}courses_offering_section_badges`
         WHERE `section_id` IN ({$secList})"
    );
    $badgeIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (!empty($badgeIds)) {
        $bList = implode(',', array_map('intval', $badgeIds));
        $db->exec(
            "DELETE FROM `{$prefix}courses_offering_section_badge_criteria`
             WHERE `section_badge_id` IN ({$bList})"
        );
        $db->exec(
            "DELETE FROM `{$prefix}courses_offering_section_badges`
             WHERE `id` IN ({$bList})"
        );
    }

    $db->exec("DELETE FROM `{$prefix}courses_offering_section_codes` WHERE `section_id` IN ({$secList})");
    $db->exec("DELETE FROM `{$prefix}courses_offering_section_dates` WHERE `section_id` IN ({$secList})");
    $db->exec("DELETE FROM `{$prefix}courses_announcements` WHERE `offering_id` IN ({$offList})");
    $db->exec("DELETE FROM `{$prefix}courses_pages` WHERE `course_id` IN ({$idList})");
    $db->exec("DELETE FROM `{$prefix}courses_log` WHERE `scope` = 'course' AND `scope_id` IN ({$idList})");
    $db->exec("DELETE FROM `{$prefix}courses_certificates` WHERE `course_id` IN ({$idList})");
    $db->exec("DELETE FROM `{$prefix}courses_assets` WHERE `course_id` IN ({$idList})");
    $db->exec("DELETE FROM `{$prefix}courses_asset_groups` WHERE `unit_id` IN ({$unitList})");
    $db->exec("DELETE FROM `{$prefix}courses_members` WHERE `course_id` IN ({$idList})");
    $db->exec("DELETE FROM `{$prefix}courses_prerequisites` WHERE `section_id` IN ({$secList})");
    $db->exec("DELETE FROM `{$prefix}courses_progress_factors` WHERE `section_id` IN ({$secList})");
    $db->exec("DELETE FROM `{$prefix}courses_units` WHERE `offering_id` IN ({$offList})");
    $db->exec("DELETE FROM `{$prefix}courses_offering_sections` WHERE `offering_id` IN ({$offList})");
    $db->exec("DELETE FROM `{$prefix}courses_offerings` WHERE `course_id` IN ({$idList})");
    $db->exec("DELETE FROM `{$prefix}courses` WHERE `id` IN ({$idList})");

    // Clean up roles and policies we created
    $db->exec(
        "DELETE FROM `{$prefix}courses_roles`
         WHERE `alias` IN ('student','manager','instructor','ta','auditor')"
    );
    $db->exec("DELETE FROM `{$prefix}courses_grade_policies` WHERE `description` LIKE '{$marker}%'");

    echo "Removed " . count($courseIds) . " courses and all related data.\n";
}

// ═════════════════════════════════════════════════════════════════════════════
// Data Definitions
// ═════════════════════════════════════════════════════════════════════════════

function ensureRoles(PDO $db, string $prefix): array
{
    $roleDefs = [
        ['title' => 'Student',    'alias' => 'student',    'perms' => ''],
        ['title' => 'Manager',    'alias' => 'manager',    'perms' => '{"admin":1}'],
        ['title' => 'Instructor', 'alias' => 'instructor', 'perms' => '{"manage":1}'],
        ['title' => 'TA',         'alias' => 'ta',         'perms' => '{"grade":1}'],
        ['title' => 'Auditor',    'alias' => 'auditor',    'perms' => ''],
    ];

    $roles = [];
    foreach ($roleDefs as $r) {
        $stmt = $db->prepare(
            "SELECT `id` FROM `{$prefix}courses_roles` WHERE `alias` = ?"
        );
        $stmt->execute([$r['alias']]);
        $id = $stmt->fetchColumn();
        if ($id) {
            $roles[$r['alias']] = (int) $id;
        } else {
            $db->prepare(
                "INSERT INTO `{$prefix}courses_roles`
                    (`offering_id`, `alias`, `title`, `permissions`)
                 VALUES (0, ?, ?, ?)"
            )->execute([$r['alias'], $r['title'], $r['perms']]);
            $roles[$r['alias']] = (int) $db->lastInsertId();
        }
    }
    return $roles;
}

function createGradePolicies(PDO $db, string $prefix): array
{
    $marker = '[SEED-CRS]';
    $defs = [
        'standard' => [
            'desc'      => "{$marker} Standard grading (60% pass)",
            'threshold' => 0.60,
            'exam'      => 0.40,
            'quiz'      => 0.30,
            'homework'  => 0.30,
        ],
        'strict' => [
            'desc'      => "{$marker} Strict grading (80% pass)",
            'threshold' => 0.80,
            'exam'      => 0.50,
            'quiz'      => 0.25,
            'homework'  => 0.25,
        ],
        'project' => [
            'desc'      => "{$marker} Project-based (70% pass, homework heavy)",
            'threshold' => 0.70,
            'exam'      => 0.20,
            'quiz'      => 0.10,
            'homework'  => 0.70,
        ],
    ];

    $policies = [];
    $insert = $db->prepare(
        "INSERT INTO `{$prefix}courses_grade_policies`
            (`description`, `threshold`, `exam_weight`, `quiz_weight`, `homework_weight`)
         VALUES (:desc, :threshold, :exam, :quiz, :homework)"
    );

    foreach ($defs as $key => $d) {
        $insert->execute([
            ':desc'      => $d['desc'],
            ':threshold' => $d['threshold'],
            ':exam'      => $d['exam'],
            ':quiz'      => $d['quiz'],
            ':homework'  => $d['homework'],
        ]);
        $policies[$key] = (int) $db->lastInsertId();
    }
    return $policies;
}

function ensureAssetGroupTypes(PDO $db, string $prefix): void
{
    $types = [
        ['alias' => 'lectures',    'type' => 'Lectures'],
        ['alias' => 'homework',    'type' => 'Homework'],
        ['alias' => 'exam',        'type' => 'Exam'],
        ['alias' => 'notes',       'type' => 'Notes'],
        ['alias' => 'references',  'type' => 'References'],
    ];

    foreach ($types as $t) {
        $stmt = $db->prepare(
            "SELECT COUNT(*) FROM `{$prefix}courses_asset_group_types` WHERE `alias` = ?"
        );
        $stmt->execute([$t['alias']]);
        if ((int) $stmt->fetchColumn() === 0) {
            $db->prepare(
                "INSERT INTO `{$prefix}courses_asset_group_types` (`alias`, `type`)
                 VALUES (?, ?)"
            )->execute([$t['alias'], $t['type']]);
        }
    }
}

function getCourseDefinitions(string $m): array
{
    $pastDate = fn(int $d) => date('Y-m-d H:i:s', strtotime("-{$d} days"));

    return [
        // ── Published courses ────────────────────────────────────────────
        'intro-cs' => [
            'alias'       => 'intro-cs',
            'title'       => "{$m} Introduction to Computer Science",
            'state'       => 1,
            'blurb'       => 'A comprehensive introduction to computer science covering programming fundamentals, data structures, algorithms, and computational thinking.',
            'description' => '<p>This course provides a solid foundation in computer science. Topics include variables, control flow, functions, object-oriented programming, basic data structures (arrays, linked lists, trees), sorting and searching algorithms, and an introduction to computational complexity.</p><p>No prior programming experience required.</p>',
            'created'     => $pastDate(365),
            'creator_idx' => 0,
            'length'      => '16 weeks',
            'effort'      => '8-10 hours/week',
        ],
        'data-science' => [
            'alias'       => 'data-science',
            'title'       => "{$m} Data Science and Machine Learning",
            'state'       => 1,
            'blurb'       => 'Hands-on course in data science with Python, covering statistics, visualization, machine learning models, and real-world data analysis.',
            'description' => '<p>Learn the complete data science pipeline from data collection and cleaning through modeling and deployment. Uses Python, pandas, scikit-learn, and TensorFlow. Includes 6 hands-on projects with real datasets.</p>',
            'created'     => $pastDate(200),
            'creator_idx' => 1,
            'length'      => '12 weeks',
            'effort'      => '10-12 hours/week',
        ],
        'materials-eng' => [
            'alias'       => 'materials-eng',
            'title'       => "{$m} Computational Materials Engineering",
            'state'       => 1,
            'blurb'       => 'Simulation-based materials engineering covering molecular dynamics, finite element analysis, and phase-field modeling for materials design.',
            'description' => '<p>Students learn to use computational tools for materials design and characterization. Topics include atomistic simulation (LAMMPS, VASP), continuum modeling (FEA), and multi-scale methods. Emphasis on hands-on tool usage within the Hubzero platform.</p>',
            'created'     => $pastDate(300),
            'creator_idx' => 2,
            'length'      => '14 weeks',
            'effort'      => '6-8 hours/week',
            'group_id'    => 0,
        ],
        'web-dev' => [
            'alias'       => 'web-dev',
            'title'       => "{$m} Full-Stack Web Development",
            'state'       => 1,
            'blurb'       => 'Build modern web applications from scratch using HTML, CSS, JavaScript, Node.js, and React. Deploy to the cloud.',
            'description' => '<p>A project-driven course where you build progressively complex web applications. Covers frontend (HTML5, CSS3, React), backend (Node.js, Express, REST APIs), databases (PostgreSQL, MongoDB), authentication, and cloud deployment.</p>',
            'created'     => $pastDate(150),
            'creator_idx' => 3,
            'length'      => '10 weeks',
            'effort'      => '12-15 hours/week',
        ],
        'quantum' => [
            'alias'       => 'quantum-computing-course',
            'title'       => "{$m} Introduction to Quantum Computing",
            'state'       => 1,
            'blurb'       => 'Explore quantum computing fundamentals including qubits, quantum gates, entanglement, and programming quantum computers with Qiskit.',
            'description' => '<p>This course introduces the principles of quantum computing for students with a background in linear algebra. Topics include quantum mechanics basics, qubit representations, quantum gates, quantum algorithms (Grover, Shor, VQE), quantum error correction, and hands-on programming with IBM Qiskit.</p>',
            'created'     => $pastDate(90),
            'creator_idx' => 4,
            'length'      => '8 weeks',
            'effort'      => '6-8 hours/week',
        ],

        // ── Unpublished (state=0) ────────────────────────────────────────
        'bioinfo-draft' => [
            'alias'       => 'bioinformatics-foundations',
            'title'       => "{$m} Bioinformatics Foundations",
            'state'       => 0,
            'blurb'       => 'Introduction to bioinformatics: sequence alignment, phylogenetics, protein structure prediction, and genomics data analysis.',
            'description' => '<p>Currently being developed. Expected launch: Fall 2026.</p>',
            'created'     => $pastDate(30),
            'creator_idx' => 5,
            'length'      => '12 weeks',
            'effort'      => '8 hours/week',
        ],

        // ── Draft (state=3) ──────────────────────────────────────────────
        'robotics-pending' => [
            'alias'       => 'intro-robotics',
            'title'       => "{$m} Introduction to Robotics",
            'state'       => 3,
            'blurb'       => 'Fundamentals of robotics including kinematics, dynamics, control, and sensor integration. Uses ROS2 simulation environment.',
            'description' => '<p>Pending review by the curriculum committee. This course covers robot manipulators, mobile robots, SLAM, and ROS2 programming.</p>',
            'created'     => $pastDate(14),
            'creator_idx' => 6,
            'length'      => '14 weeks',
            'effort'      => '10 hours/week',
        ],

        // ── Trashed (state=2) ────────────────────────────────────────────
        'old-fortran' => [
            'alias'       => 'legacy-fortran-course',
            'title'       => "{$m} Fortran Programming (Legacy)",
            'state'       => 2,
            'blurb'       => 'Archived Fortran programming course. Replaced by the modern Fortran 2018 edition.',
            'description' => '<p>This course has been retired and replaced with updated content.</p>',
            'created'     => $pastDate(800),
            'creator_idx' => 0,
        ],
    ];
}

function getOfferingDefinitions(): array
{
    $pastDate = fn(int $d) => date('Y-m-d H:i:s', strtotime("-{$d} days"));
    $futureDate = fn(int $d) => date('Y-m-d H:i:s', strtotime("+{$d} days"));

    return [
        // intro-cs offerings
        'cs-fall2025' => [
            'course_key'  => 'intro-cs',
            'alias'       => 'fall-2025',
            'title'       => 'Fall 2025',
            'term'        => 'Fall 2025',
            'state'       => 1,
            'publish_up'  => $pastDate(180),
            'publish_down' => $pastDate(30),
            'created'     => $pastDate(200),
            'creator_idx' => 0,
        ],
        'cs-spring2026' => [
            'course_key'  => 'intro-cs',
            'alias'       => 'spring-2026',
            'title'       => 'Spring 2026',
            'term'        => 'Spring 2026',
            'state'       => 1,
            'publish_up'  => $pastDate(45),
            'publish_down' => $futureDate(75),
            'created'     => $pastDate(60),
            'creator_idx' => 0,
        ],
        'cs-summer2026' => [
            'course_key'  => 'intro-cs',
            'alias'       => 'summer-2026',
            'title'       => 'Summer 2026 (Intensive)',
            'term'        => 'Summer 2026',
            'state'       => 0, // Not yet published
            'publish_up'  => $futureDate(30),
            'publish_down' => $futureDate(90),
            'created'     => $pastDate(10),
            'creator_idx' => 0,
        ],

        // data-science offerings
        'ds-spring2026' => [
            'course_key'  => 'data-science',
            'alias'       => 'spring-2026',
            'title'       => 'Spring 2026',
            'term'        => 'Spring 2026',
            'state'       => 1,
            'publish_up'  => $pastDate(60),
            'publish_down' => $futureDate(30),
            'created'     => $pastDate(90),
            'creator_idx' => 1,
        ],
        'ds-selfpaced' => [
            'course_key'  => 'data-science',
            'alias'       => 'self-paced',
            'title'       => 'Self-Paced',
            'term'        => '',
            'state'       => 1,
            'publish_up'  => $pastDate(120),
            'created'     => $pastDate(120),
            'creator_idx' => 1,
        ],

        // materials-eng offerings
        'me-fall2025' => [
            'course_key'  => 'materials-eng',
            'alias'       => 'fall-2025',
            'title'       => 'Fall 2025',
            'term'        => 'Fall 2025',
            'state'       => 1,
            'publish_up'  => $pastDate(200),
            'publish_down' => $pastDate(60),
            'created'     => $pastDate(220),
            'creator_idx' => 2,
        ],

        // web-dev offerings
        'wd-spring2026' => [
            'course_key'  => 'web-dev',
            'alias'       => 'spring-2026',
            'title'       => 'Spring 2026 Cohort',
            'term'        => 'Spring 2026',
            'state'       => 1,
            'publish_up'  => $pastDate(30),
            'publish_down' => $futureDate(60),
            'created'     => $pastDate(45),
            'creator_idx' => 3,
        ],

        // quantum offerings
        'qc-spring2026' => [
            'course_key'  => 'quantum',
            'alias'       => 'spring-2026',
            'title'       => 'Spring 2026',
            'term'        => 'Spring 2026',
            'state'       => 1,
            'publish_up'  => $pastDate(20),
            'publish_down' => $futureDate(40),
            'created'     => $pastDate(30),
            'creator_idx' => 4,
        ],
    ];
}

function getSectionDefinitions(): array
{
    $pastDate = fn(int $d) => date('Y-m-d H:i:s', strtotime("-{$d} days"));
    $futureDate = fn(int $d) => date('Y-m-d H:i:s', strtotime("+{$d} days"));

    return [
        // CS Fall 2025 sections
        'cs-f2025-default' => [
            'offering_key' => 'cs-fall2025',
            'alias'        => 'section-001',
            'title'        => 'Section 001',
            'is_default'   => 1,
            'state'        => 1,
            'start_date'   => $pastDate(180),
            'end_date'     => $pastDate(30),
            'enrollment'   => 0, // Open
            'policy_key'   => 'standard',
            'created'      => $pastDate(200),
            'creator_idx'  => 0,
        ],
        'cs-f2025-honors' => [
            'offering_key' => 'cs-fall2025',
            'alias'        => 'honors',
            'title'        => 'Honors Section',
            'state'        => 1,
            'start_date'   => $pastDate(180),
            'end_date'     => $pastDate(30),
            'enrollment'   => 1, // Restricted
            'policy_key'   => 'strict',
            'created'      => $pastDate(200),
            'creator_idx'  => 0,
        ],

        // CS Spring 2026 sections
        'cs-s2026-default' => [
            'offering_key' => 'cs-spring2026',
            'alias'        => 'section-001',
            'title'        => 'Section 001',
            'is_default'   => 1,
            'state'        => 1,
            'start_date'   => $pastDate(45),
            'end_date'     => $futureDate(75),
            'enrollment'   => 0,
            'policy_key'   => 'standard',
            'created'      => $pastDate(60),
            'creator_idx'  => 0,
        ],
        'cs-s2026-evening' => [
            'offering_key' => 'cs-spring2026',
            'alias'        => 'evening',
            'title'        => 'Evening Section',
            'state'        => 1,
            'start_date'   => $pastDate(45),
            'end_date'     => $futureDate(75),
            'enrollment'   => 0,
            'policy_key'   => 'standard',
            'created'      => $pastDate(55),
            'creator_idx'  => 0,
        ],

        // DS Spring 2026 sections
        'ds-s2026-a' => [
            'offering_key' => 'ds-spring2026',
            'alias'        => 'section-a',
            'title'        => 'Section A',
            'is_default'   => 1,
            'state'        => 1,
            'start_date'   => $pastDate(60),
            'end_date'     => $futureDate(30),
            'enrollment'   => 0,
            'policy_key'   => 'project',
            'created'      => $pastDate(90),
            'creator_idx'  => 1,
        ],
        'ds-s2026-b' => [
            'offering_key' => 'ds-spring2026',
            'alias'        => 'section-b',
            'title'        => 'Section B',
            'state'        => 1,
            'start_date'   => $pastDate(60),
            'end_date'     => $futureDate(30),
            'enrollment'   => 1,
            'policy_key'   => 'project',
            'created'      => $pastDate(90),
            'creator_idx'  => 1,
        ],

        // DS Self-paced
        'ds-sp-default' => [
            'offering_key' => 'ds-selfpaced',
            'alias'        => 'default',
            'title'        => 'Default',
            'is_default'   => 1,
            'state'        => 1,
            'enrollment'   => 0,
            'policy_key'   => 'standard',
            'created'      => $pastDate(120),
            'creator_idx'  => 1,
        ],

        // ME Fall 2025
        'me-f2025-default' => [
            'offering_key' => 'me-fall2025',
            'alias'        => 'section-001',
            'title'        => 'Section 001',
            'is_default'   => 1,
            'state'        => 1,
            'start_date'   => $pastDate(200),
            'end_date'     => $pastDate(60),
            'enrollment'   => 0,
            'policy_key'   => 'standard',
            'created'      => $pastDate(220),
            'creator_idx'  => 2,
        ],

        // Web Dev Spring 2026
        'wd-s2026-default' => [
            'offering_key' => 'wd-spring2026',
            'alias'        => 'cohort-1',
            'title'        => 'Cohort 1',
            'is_default'   => 1,
            'state'        => 1,
            'start_date'   => $pastDate(30),
            'end_date'     => $futureDate(60),
            'enrollment'   => 1,
            'policy_key'   => 'project',
            'created'      => $pastDate(45),
            'creator_idx'  => 3,
        ],

        // Quantum Spring 2026
        'qc-s2026-default' => [
            'offering_key' => 'qc-spring2026',
            'alias'        => 'section-001',
            'title'        => 'Section 001',
            'is_default'   => 1,
            'state'        => 1,
            'start_date'   => $pastDate(20),
            'end_date'     => $futureDate(40),
            'enrollment'   => 0,
            'policy_key'   => 'strict',
            'created'      => $pastDate(30),
            'creator_idx'  => 4,
        ],

        // Unpublished section
        'qc-s2026-online' => [
            'offering_key' => 'qc-spring2026',
            'alias'        => 'online',
            'title'        => 'Online Section (Not Ready)',
            'state'        => 0,
            'enrollment'   => 0,
            'policy_key'   => 'standard',
            'created'      => $pastDate(10),
            'creator_idx'  => 4,
        ],
    ];
}

function getUnitDefinitions(): array
{
    $pastDate = fn(int $d) => date('Y-m-d H:i:s', strtotime("-{$d} days"));

    return [
        // CS Spring 2026 units
        'cs-u1' => [
            'offering_key' => 'cs-spring2026',
            'alias'        => 'programming-basics',
            'title'        => 'Unit 1: Programming Basics',
            'description'  => 'Variables, data types, control flow, and functions in Python.',
            'ordering'     => 1,
            'state'        => 1,
            'created'      => $pastDate(60),
            'creator_idx'  => 0,
        ],
        'cs-u2' => [
            'offering_key' => 'cs-spring2026',
            'alias'        => 'data-structures',
            'title'        => 'Unit 2: Data Structures',
            'description'  => 'Arrays, linked lists, stacks, queues, and hash tables.',
            'ordering'     => 2,
            'state'        => 1,
            'created'      => $pastDate(55),
            'creator_idx'  => 0,
        ],
        'cs-u3' => [
            'offering_key' => 'cs-spring2026',
            'alias'        => 'algorithms',
            'title'        => 'Unit 3: Algorithms',
            'description'  => 'Sorting, searching, recursion, and algorithmic complexity.',
            'ordering'     => 3,
            'state'        => 1,
            'created'      => $pastDate(50),
            'creator_idx'  => 0,
        ],
        'cs-u4' => [
            'offering_key' => 'cs-spring2026',
            'alias'        => 'oop',
            'title'        => 'Unit 4: Object-Oriented Programming',
            'description'  => 'Classes, inheritance, polymorphism, and design patterns.',
            'ordering'     => 4,
            'state'        => 0, // Not yet published
            'created'      => $pastDate(40),
            'creator_idx'  => 0,
        ],

        // DS Spring 2026 units
        'ds-u1' => [
            'offering_key' => 'ds-spring2026',
            'alias'        => 'data-wrangling',
            'title'        => 'Unit 1: Data Wrangling',
            'description'  => 'Loading, cleaning, and transforming data with pandas.',
            'ordering'     => 1,
            'state'        => 1,
            'created'      => $pastDate(90),
            'creator_idx'  => 1,
        ],
        'ds-u2' => [
            'offering_key' => 'ds-spring2026',
            'alias'        => 'visualization',
            'title'        => 'Unit 2: Data Visualization',
            'description'  => 'Creating publication-quality plots with matplotlib and seaborn.',
            'ordering'     => 2,
            'state'        => 1,
            'created'      => $pastDate(85),
            'creator_idx'  => 1,
        ],
        'ds-u3' => [
            'offering_key' => 'ds-spring2026',
            'alias'        => 'ml-models',
            'title'        => 'Unit 3: Machine Learning Models',
            'description'  => 'Regression, classification, clustering, and model evaluation.',
            'ordering'     => 3,
            'state'        => 1,
            'created'      => $pastDate(80),
            'creator_idx'  => 1,
        ],

        // ME Fall 2025 units
        'me-u1' => [
            'offering_key' => 'me-fall2025',
            'alias'        => 'md-simulation',
            'title'        => 'Unit 1: Molecular Dynamics Simulation',
            'description'  => 'Fundamentals of atomistic simulation with LAMMPS.',
            'ordering'     => 1,
            'state'        => 1,
            'created'      => $pastDate(220),
            'creator_idx'  => 2,
        ],
        'me-u2' => [
            'offering_key' => 'me-fall2025',
            'alias'        => 'fea',
            'title'        => 'Unit 2: Finite Element Analysis',
            'description'  => 'FEA theory and practice with commercial and open-source codes.',
            'ordering'     => 2,
            'state'        => 1,
            'created'      => $pastDate(210),
            'creator_idx'  => 2,
        ],

        // Web Dev Spring 2026 units
        'wd-u1' => [
            'offering_key' => 'wd-spring2026',
            'alias'        => 'html-css',
            'title'        => 'Unit 1: HTML & CSS',
            'description'  => 'Building responsive web pages with modern HTML5 and CSS3.',
            'ordering'     => 1,
            'state'        => 1,
            'created'      => $pastDate(45),
            'creator_idx'  => 3,
        ],
        'wd-u2' => [
            'offering_key' => 'wd-spring2026',
            'alias'        => 'javascript',
            'title'        => 'Unit 2: JavaScript & React',
            'description'  => 'Interactive frontends with vanilla JS and React components.',
            'ordering'     => 2,
            'state'        => 1,
            'created'      => $pastDate(40),
            'creator_idx'  => 3,
        ],
        'wd-u3' => [
            'offering_key' => 'wd-spring2026',
            'alias'        => 'backend',
            'title'        => 'Unit 3: Backend with Node.js',
            'description'  => 'REST APIs, databases, authentication, and deployment.',
            'ordering'     => 3,
            'state'        => 1,
            'created'      => $pastDate(35),
            'creator_idx'  => 3,
        ],

        // Quantum Spring 2026 units
        'qc-u1' => [
            'offering_key' => 'qc-spring2026',
            'alias'        => 'qubit-basics',
            'title'        => 'Unit 1: Qubits and Quantum States',
            'description'  => 'Superposition, measurement, Bloch sphere representation.',
            'ordering'     => 1,
            'state'        => 1,
            'created'      => $pastDate(30),
            'creator_idx'  => 4,
        ],
        'qc-u2' => [
            'offering_key' => 'qc-spring2026',
            'alias'        => 'quantum-gates',
            'title'        => 'Unit 2: Quantum Gates and Circuits',
            'description'  => 'Hadamard, CNOT, Toffoli gates; circuit notation; Qiskit programming.',
            'ordering'     => 2,
            'state'        => 1,
            'created'      => $pastDate(25),
            'creator_idx'  => 4,
        ],
    ];
}

function getAssetGroupDefinitions(): array
{
    $pastDate = fn(int $d) => date('Y-m-d H:i:s', strtotime("-{$d} days"));

    $groups = [];
    $unitKeys = [
        'cs-u1', 'cs-u2', 'cs-u3',
        'ds-u1', 'ds-u2', 'ds-u3',
        'me-u1', 'me-u2',
        'wd-u1', 'wd-u2', 'wd-u3',
        'qc-u1', 'qc-u2',
    ];

    $agTypes = [
        ['alias' => 'lectures',   'title' => 'Lectures'],
        ['alias' => 'homework',   'title' => 'Homework'],
        ['alias' => 'notes',      'title' => 'Notes'],
    ];

    foreach ($unitKeys as $uKey) {
        foreach ($agTypes as $ord => $agt) {
            $key = "{$uKey}-{$agt['alias']}";
            $groups[$key] = [
                'unit_key'    => $uKey,
                'alias'       => $agt['alias'],
                'title'       => $agt['title'],
                'ordering'    => $ord + 1,
                'state'       => 1,
                'created'     => $pastDate(rand(30, 90)),
                'creator_idx' => 0,
            ];
        }
    }

    return $groups;
}

function getAssetDefinitions(): array
{
    $pastDate = fn(int $d) => date('Y-m-d H:i:s', strtotime("-{$d} days"));

    return [
        // CS assets
        'cs-lecture1' => [
            'course_key'  => 'intro-cs',
            'title'       => 'Lecture 1: Welcome and Course Overview',
            'type'        => 'video',
            'subtype'     => 'video',
            'url'         => '/courses/intro-cs/lectures/01-welcome.mp4',
            'state'       => 1,
            'created'     => $pastDate(55),
            'creator_idx' => 0,
        ],
        'cs-lecture2' => [
            'course_key'  => 'intro-cs',
            'title'       => 'Lecture 2: Variables and Data Types',
            'type'        => 'video',
            'subtype'     => 'video',
            'url'         => '/courses/intro-cs/lectures/02-variables.mp4',
            'state'       => 1,
            'created'     => $pastDate(50),
            'creator_idx' => 0,
        ],
        'cs-hw1' => [
            'course_key'   => 'intro-cs',
            'title'        => 'Homework 1: Hello World and Basics',
            'type'         => 'file',
            'content'      => 'Write a program that prints "Hello, World!" and performs basic arithmetic operations.',
            'state'        => 1,
            'graded'       => 1,
            'grade_weight' => 'homework',
            'created'      => $pastDate(50),
            'creator_idx'  => 0,
        ],
        'cs-quiz1' => [
            'course_key'   => 'intro-cs',
            'title'        => 'Quiz 1: Programming Fundamentals',
            'type'         => 'form',
            'subtype'      => 'quiz',
            'state'        => 1,
            'graded'       => 1,
            'grade_weight' => 'quiz',
            'created'      => $pastDate(45),
            'creator_idx'  => 0,
        ],
        'cs-midterm' => [
            'course_key'   => 'intro-cs',
            'title'        => 'Midterm Exam',
            'type'         => 'form',
            'subtype'      => 'exam',
            'state'        => 1,
            'graded'       => 1,
            'grade_weight' => 'exam',
            'created'      => $pastDate(30),
            'creator_idx'  => 0,
        ],
        'cs-notes-ds' => [
            'course_key'  => 'intro-cs',
            'title'       => 'Data Structures Cheat Sheet',
            'type'        => 'file',
            'subtype'     => 'file',
            'state'       => 1,
            'created'     => $pastDate(48),
            'creator_idx' => 0,
        ],
        'cs-link-python' => [
            'course_key'  => 'intro-cs',
            'title'       => 'Python Documentation (External)',
            'type'        => 'url',
            'subtype'     => 'link',
            'url'         => 'https://docs.python.org/3/',
            'state'       => 1,
            'created'     => $pastDate(55),
            'creator_idx' => 0,
        ],

        // DS assets
        'ds-lecture1' => [
            'course_key'  => 'data-science',
            'title'       => 'Lecture: Introduction to pandas',
            'type'        => 'video',
            'subtype'     => 'video',
            'state'       => 1,
            'created'     => $pastDate(85),
            'creator_idx' => 1,
        ],
        'ds-notebook1' => [
            'course_key'  => 'data-science',
            'title'       => 'Lab Notebook: Data Cleaning Exercise',
            'type'        => 'file',
            'subtype'     => 'file',
            'content'     => 'Jupyter notebook for hands-on data cleaning.',
            'state'       => 1,
            'graded'      => 1,
            'grade_weight' => 'homework',
            'created'     => $pastDate(80),
            'creator_idx' => 1,
        ],
        'ds-project' => [
            'course_key'   => 'data-science',
            'title'        => 'Final Project: Real-World Data Analysis',
            'type'         => 'file',
            'content'      => 'Choose a public dataset, perform EDA, build a predictive model, and present findings.',
            'state'        => 1,
            'graded'       => 1,
            'grade_weight' => 'homework',
            'created'      => $pastDate(60),
            'creator_idx'  => 1,
        ],

        // ME assets
        'me-lecture1' => [
            'course_key'  => 'materials-eng',
            'title'       => 'Lecture: Introduction to LAMMPS',
            'type'        => 'video',
            'subtype'     => 'video',
            'state'       => 1,
            'created'     => $pastDate(210),
            'creator_idx' => 2,
        ],
        'me-sim-tool' => [
            'course_key'  => 'materials-eng',
            'title'       => 'NanoSim Interactive Tool',
            'type'        => 'tool',
            'subtype'     => 'tool',
            'url'         => '/tools/nanosim',
            'state'       => 1,
            'created'     => $pastDate(200),
            'creator_idx' => 2,
        ],

        // WD assets
        'wd-lecture1' => [
            'course_key'  => 'web-dev',
            'title'       => 'Lecture: Semantic HTML and Accessibility',
            'type'        => 'video',
            'subtype'     => 'video',
            'state'       => 1,
            'created'     => $pastDate(40),
            'creator_idx' => 3,
        ],
        'wd-hw1' => [
            'course_key'   => 'web-dev',
            'title'        => 'Project 1: Personal Portfolio Site',
            'type'         => 'file',
            'content'      => 'Build a responsive personal portfolio using HTML and CSS.',
            'state'        => 1,
            'graded'       => 1,
            'grade_weight' => 'homework',
            'created'      => $pastDate(38),
            'creator_idx'  => 3,
        ],

        // QC assets
        'qc-lecture1' => [
            'course_key'  => 'quantum',
            'title'       => 'Lecture: What Is a Qubit?',
            'type'        => 'video',
            'subtype'     => 'video',
            'state'       => 1,
            'created'     => $pastDate(25),
            'creator_idx' => 4,
        ],
        'qc-lab1' => [
            'course_key'   => 'quantum',
            'title'        => 'Lab: Building Quantum Circuits in Qiskit',
            'type'         => 'file',
            'content'      => 'Implement Hadamard, CNOT, and Toffoli gates. Create and measure Bell states.',
            'state'        => 1,
            'graded'       => 1,
            'grade_weight' => 'homework',
            'created'      => $pastDate(22),
            'creator_idx'  => 4,
        ],

        // Draft/unpublished asset
        'cs-final-draft' => [
            'course_key'  => 'intro-cs',
            'title'       => 'Final Exam (Draft)',
            'type'        => 'form',
            'subtype'     => 'exam',
            'state'       => 0,
            'graded'      => 1,
            'grade_weight' => 'exam',
            'created'     => $pastDate(5),
            'creator_idx' => 0,
        ],
    ];
}

function getMemberDefinitions(): array
{
    $pastDate = fn(int $d) => date('Y-m-d H:i:s', strtotime("-{$d} days"));

    $members = [];

    // CS Spring 2026 — managers and instructors
    $members[] = [
        'course_key'   => 'intro-cs',
        'offering_key' => 'cs-spring2026',
        'section_key'  => 'cs-s2026-default',
        'role'         => 'manager',
        'user_idx'     => 0,
        'enrolled'     => $pastDate(60),
        'student'      => 0,
    ];
    $members[] = [
        'course_key'   => 'intro-cs',
        'offering_key' => 'cs-spring2026',
        'section_key'  => 'cs-s2026-default',
        'role'         => 'instructor',
        'user_idx'     => 1,
        'enrolled'     => $pastDate(60),
        'student'      => 0,
    ];
    $members[] = [
        'course_key'   => 'intro-cs',
        'offering_key' => 'cs-spring2026',
        'section_key'  => 'cs-s2026-default',
        'role'         => 'ta',
        'user_idx'     => 7,
        'enrolled'     => $pastDate(55),
        'student'      => 0,
    ];

    // CS Spring 2026 — students (Sec 001)
    for ($i = 2; $i <= 10; $i++) {
        $members[] = [
            'course_key'   => 'intro-cs',
            'offering_key' => 'cs-spring2026',
            'section_key'  => 'cs-s2026-default',
            'role'         => 'student',
            'user_idx'     => $i,
            'enrolled'     => $pastDate(55 - $i),
            'student'      => 1,
            'first_visit'  => $pastDate(50 - $i),
        ];
    }

    // CS Spring 2026 — students (Evening)
    for ($i = 11; $i <= 15; $i++) {
        $members[] = [
            'course_key'   => 'intro-cs',
            'offering_key' => 'cs-spring2026',
            'section_key'  => 'cs-s2026-evening',
            'role'         => 'student',
            'user_idx'     => $i,
            'enrolled'     => $pastDate(50 - ($i - 11)),
            'student'      => 1,
            'first_visit'  => $pastDate(45 - ($i - 11)),
        ];
    }

    // DS Spring 2026 — manager + instructor + students
    $members[] = [
        'course_key'   => 'data-science',
        'offering_key' => 'ds-spring2026',
        'section_key'  => 'ds-s2026-a',
        'role'         => 'manager',
        'user_idx'     => 1,
        'enrolled'     => $pastDate(90),
        'student'      => 0,
    ];
    $members[] = [
        'course_key'   => 'data-science',
        'offering_key' => 'ds-spring2026',
        'section_key'  => 'ds-s2026-a',
        'role'         => 'instructor',
        'user_idx'     => 3,
        'enrolled'     => $pastDate(90),
        'student'      => 0,
    ];
    for ($i = 4; $i <= 12; $i++) {
        $sec = ($i < 8) ? 'ds-s2026-a' : 'ds-s2026-b';
        $members[] = [
            'course_key'   => 'data-science',
            'offering_key' => 'ds-spring2026',
            'section_key'  => $sec,
            'role'         => 'student',
            'user_idx'     => $i,
            'enrolled'     => $pastDate(60 - $i),
            'student'      => 1,
            'first_visit'  => $pastDate(55 - $i),
        ];
    }

    // ME Fall 2025 — manager + students
    $members[] = [
        'course_key'   => 'materials-eng',
        'offering_key' => 'me-fall2025',
        'section_key'  => 'me-f2025-default',
        'role'         => 'manager',
        'user_idx'     => 2,
        'enrolled'     => $pastDate(220),
        'student'      => 0,
    ];
    for ($i = 5; $i <= 10; $i++) {
        $members[] = [
            'course_key'   => 'materials-eng',
            'offering_key' => 'me-fall2025',
            'section_key'  => 'me-f2025-default',
            'role'         => 'student',
            'user_idx'     => $i,
            'enrolled'     => $pastDate(200 - $i),
            'student'      => 1,
            'first_visit'  => $pastDate(195 - $i),
        ];
    }

    // WD Spring 2026
    $members[] = [
        'course_key'   => 'web-dev',
        'offering_key' => 'wd-spring2026',
        'section_key'  => 'wd-s2026-default',
        'role'         => 'manager',
        'user_idx'     => 3,
        'enrolled'     => $pastDate(45),
        'student'      => 0,
    ];
    for ($i = 8; $i <= 16; $i++) {
        $members[] = [
            'course_key'   => 'web-dev',
            'offering_key' => 'wd-spring2026',
            'section_key'  => 'wd-s2026-default',
            'role'         => 'student',
            'user_idx'     => $i,
            'enrolled'     => $pastDate(30 - ($i - 8)),
            'student'      => 1,
            'first_visit'  => $pastDate(28 - ($i - 8)),
        ];
    }

    // QC Spring 2026
    $members[] = [
        'course_key'   => 'quantum',
        'offering_key' => 'qc-spring2026',
        'section_key'  => 'qc-s2026-default',
        'role'         => 'manager',
        'user_idx'     => 4,
        'enrolled'     => $pastDate(30),
        'student'      => 0,
    ];
    for ($i = 10; $i <= 18; $i++) {
        $members[] = [
            'course_key'   => 'quantum',
            'offering_key' => 'qc-spring2026',
            'section_key'  => 'qc-s2026-default',
            'role'         => 'student',
            'user_idx'     => $i,
            'enrolled'     => $pastDate(20 - ($i - 10)),
            'student'      => 1,
            'first_visit'  => $pastDate(18 - ($i - 10)),
        ];
    }

    // Auditor
    $members[] = [
        'course_key'   => 'intro-cs',
        'offering_key' => 'cs-spring2026',
        'section_key'  => 'cs-s2026-default',
        'role'         => 'auditor',
        'user_idx'     => 19,
        'enrolled'     => $pastDate(40),
        'student'      => 0,
    ];

    return $members;
}

function getCodeDefinitions(): array
{
    $pastDate = fn(int $d) => date('Y-m-d H:i:s', strtotime("-{$d} days"));
    $futureDate = fn(int $d) => date('Y-m-d H:i:s', strtotime("+{$d} days"));

    $codes = [];

    // CS codes — mix of redeemed and unredeemed
    $csPrefixes = ['CS26', 'CSPR'];
    for ($i = 1; $i <= 8; $i++) {
        $code = $csPrefixes[array_rand($csPrefixes)] . str_pad($i, 3, '0', STR_PAD_LEFT);
        $redeemed = ($i <= 5);
        $codes[] = [
            'section_key' => 'cs-s2026-default',
            'code'        => $code,
            'created'     => $pastDate(60),
            'creator_idx' => 0,
            'expires'     => $futureDate(90),
            'redeemed'    => $redeemed ? $pastDate(55 - $i) : null,
            'redeemed_by' => $redeemed ? ($i + 1) : null,
        ];
    }

    // DS codes — some expired
    for ($i = 1; $i <= 5; $i++) {
        $code = 'DS26' . str_pad($i, 3, '0', STR_PAD_LEFT);
        $expired = ($i <= 2);
        $codes[] = [
            'section_key' => 'ds-s2026-a',
            'code'        => $code,
            'created'     => $pastDate(90),
            'creator_idx' => 1,
            'expires'     => $expired ? $pastDate(10) : $futureDate(60),
            'redeemed'    => ($i === 1) ? $pastDate(50) : null,
            'redeemed_by' => ($i === 1) ? 5 : null,
        ];
    }

    // WD codes — restricted enrollment
    for ($i = 1; $i <= 6; $i++) {
        $code = 'WD26' . str_pad($i, 3, '0', STR_PAD_LEFT);
        $redeemed = ($i <= 4);
        $codes[] = [
            'section_key' => 'wd-s2026-default',
            'code'        => $code,
            'created'     => $pastDate(45),
            'creator_idx' => 3,
            'expires'     => $futureDate(60),
            'redeemed'    => $redeemed ? $pastDate(30 - $i) : null,
            'redeemed_by' => $redeemed ? ($i + 7) : null,
        ];
    }

    // QC codes
    for ($i = 1; $i <= 4; $i++) {
        $code = 'QC26' . str_pad($i, 3, '0', STR_PAD_LEFT);
        $codes[] = [
            'section_key' => 'qc-s2026-default',
            'code'        => $code,
            'created'     => $pastDate(30),
            'creator_idx' => 4,
            'expires'     => $futureDate(40),
            'redeemed'    => ($i <= 2) ? $pastDate(15 - $i) : null,
            'redeemed_by' => ($i <= 2) ? ($i + 9) : null,
        ];
    }

    return $codes;
}

function getPageDefinitions(): array
{
    return [
        // CS course-level pages
        ['course_key' => 'intro-cs', 'url' => 'syllabus', 'title' => 'Syllabus',
         'content' => '<h2>Course Syllabus</h2><p>This course introduces the fundamental concepts of computer science including problem solving, programming, data structures, and algorithms. No prior programming experience is required.</p><h3>Learning Objectives</h3><ul><li>Write programs in Python to solve computational problems</li><li>Understand and implement basic data structures</li><li>Analyze algorithm complexity using Big-O notation</li><li>Apply object-oriented design principles</li></ul><h3>Grading</h3><table><tr><th>Component</th><th>Weight</th></tr><tr><td>Homework</td><td>30%</td></tr><tr><td>Quizzes</td><td>30%</td></tr><tr><td>Exams</td><td>40%</td></tr></table>', 'ordering' => 1],
        ['course_key' => 'intro-cs', 'url' => 'office-hours', 'title' => 'Office Hours',
         'content' => '<h2>Office Hours</h2><p>Prof. Smith: Tuesdays and Thursdays, 2:00-4:00 PM, Room 215.</p><p>TA hours: Monday and Wednesday, 10:00 AM-12:00 PM, Room 102.</p><p>Drop-in help desk: Friday afternoons in the CS Lab.</p>', 'ordering' => 2],
        ['course_key' => 'intro-cs', 'url' => 'resources', 'title' => 'Resources',
         'content' => '<h2>Course Resources</h2><ul><li><strong>Textbook:</strong> "Think Python" by Allen Downey (free online)</li><li><strong>IDE:</strong> VS Code with Python extension (available on Hubzero)</li><li><strong>Reference:</strong> Python 3.11 documentation</li></ul>', 'ordering' => 3],

        // DS pages
        ['course_key' => 'data-science', 'url' => 'syllabus', 'title' => 'Syllabus',
         'content' => '<h2>Data Science Syllabus</h2><p>A hands-on course covering the complete data science pipeline. Emphasis on practical skills with real-world datasets.</p><h3>Tools</h3><p>Python 3.11, pandas, NumPy, scikit-learn, TensorFlow, Jupyter notebooks.</p>', 'ordering' => 1],
        ['course_key' => 'data-science', 'url' => 'datasets', 'title' => 'Datasets',
         'content' => '<h2>Available Datasets</h2><ul><li>Iris (classification warmup)</li><li>Housing prices (regression)</li><li>MNIST digits (deep learning)</li><li>Netflix ratings (recommendation)</li><li>Twitter sentiment (NLP)</li></ul>', 'ordering' => 2],

        // ME pages
        ['course_key' => 'materials-eng', 'url' => 'syllabus', 'title' => 'Syllabus',
         'content' => '<h2>Computational Materials Engineering</h2><p>Simulation-based course covering atomistic to continuum scale modeling. Students will use LAMMPS, VASP, and commercial FEA packages through Hubzero.</p>', 'ordering' => 1],
        ['course_key' => 'materials-eng', 'url' => 'software', 'title' => 'Software Setup',
         'content' => '<h2>Required Software</h2><p>All tools are pre-installed on Hubzero. Launch from the Tools menu:</p><ul><li>LAMMPS (molecular dynamics)</li><li>OVITO (visualization)</li><li>Jupyter + ASE (Python scripting)</li></ul>', 'ordering' => 2],

        // WD pages
        ['course_key' => 'web-dev', 'url' => 'syllabus', 'title' => 'Syllabus',
         'content' => '<h2>Full-Stack Web Development</h2><p>Build 3 complete web applications during this 10-week intensive course. Deploy each project to the cloud.</p>', 'ordering' => 1],

        // QC pages
        ['course_key' => 'quantum', 'url' => 'syllabus', 'title' => 'Syllabus',
         'content' => '<h2>Quantum Computing</h2><p>8-week course introducing quantum computing principles and programming with IBM Qiskit. Prerequisites: linear algebra.</p>', 'ordering' => 1],
        ['course_key' => 'quantum', 'url' => 'prereqs', 'title' => 'Prerequisites',
         'content' => '<h2>Prerequisites</h2><p>Students should be comfortable with:</p><ul><li>Matrix multiplication and eigenvalues</li><li>Complex numbers</li><li>Basic Python programming</li><li>Probability theory basics</li></ul>', 'ordering' => 2],

        // Inactive page
        ['course_key' => 'intro-cs', 'url' => 'archive-2024', 'title' => 'Fall 2024 Archive',
         'content' => '<p>Archived content from the Fall 2024 semester.</p>',
         'ordering' => 10, 'active' => 0],
    ];
}

function getAnnouncementDefinitions(): array
{
    $pastDate = fn(int $d) => date('Y-m-d H:i:s', strtotime("-{$d} days"));
    $futureDate = fn(int $d) => date('Y-m-d H:i:s', strtotime("+{$d} days"));

    return [
        // CS announcements
        [
            'offering_key' => 'cs-spring2026',
            'content'      => 'Welcome to Introduction to Computer Science, Spring 2026! Please review the syllabus and complete the pre-course survey by end of week 1.',
            'priority'     => 1,
            'state'        => 1,
            'sticky'       => 1,
            'publish_up'   => $pastDate(45),
            'created'      => $pastDate(45),
            'creator_idx'  => 0,
        ],
        [
            'offering_key' => 'cs-spring2026',
            'content'      => 'Homework 1 is now available. Due date: two weeks from today. Start early!',
            'state'        => 1,
            'publish_up'   => $pastDate(40),
            'created'      => $pastDate(40),
            'creator_idx'  => 0,
        ],
        [
            'offering_key' => 'cs-spring2026',
            'content'      => 'Midterm exam will be held on March 15. Review sessions available this week.',
            'priority'     => 1,
            'state'        => 1,
            'publish_up'   => $pastDate(5),
            'created'      => $pastDate(5),
            'creator_idx'  => 1,
        ],
        [
            'offering_key' => 'cs-spring2026',
            'section_key'  => 'cs-s2026-evening',
            'content'      => 'Evening section: room changed to Hall 310 starting next week.',
            'state'        => 1,
            'publish_up'   => $pastDate(20),
            'created'      => $pastDate(20),
            'creator_idx'  => 0,
        ],

        // DS announcements
        [
            'offering_key' => 'ds-spring2026',
            'content'      => 'Welcome to Data Science! Install Python and Jupyter before the first lab session.',
            'priority'     => 1,
            'state'        => 1,
            'sticky'       => 1,
            'publish_up'   => $pastDate(60),
            'created'      => $pastDate(60),
            'creator_idx'  => 1,
        ],
        [
            'offering_key' => 'ds-spring2026',
            'content'      => 'Final project proposals due by end of this week. See the Datasets page for inspiration.',
            'state'        => 1,
            'publish_up'   => $pastDate(15),
            'created'      => $pastDate(15),
            'creator_idx'  => 3,
        ],

        // ME announcement
        [
            'offering_key' => 'me-fall2025',
            'content'      => 'LAMMPS has been updated to the 2025 stable release. Re-run your scripts if you encounter compatibility issues.',
            'state'        => 1,
            'publish_up'   => $pastDate(100),
            'created'      => $pastDate(100),
            'creator_idx'  => 2,
        ],

        // Scheduled future announcement
        [
            'offering_key' => 'cs-spring2026',
            'content'      => 'Final exam information will be posted here. Date: April 30, 2026.',
            'state'        => 0, // Draft
            'publish_up'   => $futureDate(30),
            'created'      => $pastDate(2),
            'creator_idx'  => 0,
        ],

        // WD announcement
        [
            'offering_key' => 'wd-spring2026',
            'content'      => 'Project 1 demo day is this Friday at 3 PM. Prepare a 5-minute presentation of your portfolio site.',
            'state'        => 1,
            'publish_up'   => $pastDate(10),
            'created'      => $pastDate(10),
            'creator_idx'  => 3,
        ],
    ];
}
