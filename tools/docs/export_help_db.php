<?php
/**
 * Export every documentation article on help.hubzero.org to JSON, including the
 * unpublished trees (2.2 and older) that the public API does not return.
 *
 * Run on help.hubzero.org as root (hubconfiguration.php is root-only):
 *
 *   scp tools/docs/export_help_db.php help.hubzero.org:/tmp/
 *   ssh help.hubzero.org sudo php /tmp/export_help_db.php
 *   scp help.hubzero.org:/tmp/documentation-export.json docs/_import/help-export.json
 *
 * Writes /tmp/documentation-export.json (world-readable) and prints one summary
 * line per version root. Credentials are never printed.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$root = '/var/www/help';
$out  = '/tmp/documentation-export.json';

// Connection settings: app/config/database.php (a Hubzero 2.x array) if it
// exists, otherwise the legacy HubConfig class in hubconfiguration.php.
$settings = [];
if (file_exists($root . '/app/config/database.php')) {
    $settings = (array) require $root . '/app/config/database.php';
}
if (empty($settings['host'])) {
    require $root . '/hubconfiguration.php';
    $legacy = new HubConfig();
    $settings = [
        'host'     => $legacy->hubDBHost ?? $legacy->host ?? 'localhost',
        'user'     => $legacy->hubDBUsername ?? $legacy->user ?? '',
        'password' => $legacy->hubDBPassword ?? $legacy->password ?? '',
        'db'       => $legacy->hubDBName ?? $legacy->db ?? '',
        'dbprefix' => $legacy->hubDBPrefix ?? $legacy->prefix ?? 'jos_',
    ];
}
$prefix = $settings['dbprefix'] ?? $settings['prefix'] ?? 'jos_';

$db = new mysqli($settings['host'], $settings['user'], $settings['password'], $settings['db']);
if ($db->connect_error) {
    fwrite(STDERR, "Database connection failed\n");
    exit(1);
}
$db->set_charset('utf8mb4');

// The component's table is "<prefix>documentation" on some hubs and
// "<prefix>documentation_articles" on others; take whichever exists.
$table = null;
$found = $db->query("SHOW TABLES LIKE '%documentation%'");
$candidates = [];
while ($row = $found->fetch_row()) {
    $candidates[] = $row[0];
}
foreach ([$prefix . 'documentation', $prefix . 'documentation_articles'] as $preferred) {
    if (in_array($preferred, $candidates, true)) {
        $table = $preferred;
        break;
    }
}
if ($table === null) {
    foreach ($candidates as $candidate) {
        $columns = $db->query("SHOW COLUMNS FROM `{$candidate}` LIKE 'lft'");
        if ($columns && $columns->num_rows) {
            $table = $candidate;
            break;
        }
    }
}
if ($table === null) {
    fwrite(STDERR, "No documentation table found; candidates: " . implode(', ', $candidates) . "\n");
    exit(1);
}
echo "table: {$table}\n";

$result = $db->query("SELECT * FROM `{$table}` ORDER BY lft");
$fh = fopen($out, 'w');
fwrite($fh, "[\n");
$rows = [];
$i = 0;
while ($row = $result->fetch_assoc()) {
    fwrite($fh, ($i++ ? ",\n" : '') . json_encode($row, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    $rows[] = [
        'id' => (int) $row['id'], 'alias' => $row['alias'], 'title' => $row['title'],
        'state' => (int) $row['state'], 'level' => (int) $row['level'],
        'lft' => (int) $row['lft'], 'rgt' => (int) $row['rgt'], 'bytes' => strlen($row['content']),
    ];
}
fwrite($fh, "\n]\n");
fclose($fh);
chmod($out, 0644);

printf("%d rows written to %s\n\n%-6s %-16s %-5s %-6s %-9s %-10s %s\n", $i, $out, 'id', 'alias', 'state', 'pages', 'published', 'bytes', 'title');
foreach ($rows as $v) {
    if ($v['level'] !== 1) {
        continue;
    }
    $pages = $published = $bytes = 0;
    foreach ($rows as $a) {
        if ($a['lft'] > $v['lft'] && $a['rgt'] < $v['rgt']) {
            $pages++;
            $published += $a['state'] === 1 ? 1 : 0;
            $bytes += $a['bytes'];
        }
    }
    printf("%-6d %-16s %-5d %-6d %-9d %-10d %s\n", $v['id'], $v['alias'], $v['state'], $pages, $published, $bytes, $v['title']);
}
