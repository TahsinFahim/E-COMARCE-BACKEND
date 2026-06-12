<?php
$db = new PDO('sqlite:database/database.sqlite');
$tables = $db->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
echo "Tables:\n";
foreach ($tables as $t) {
    if ($t === 'sqlite_sequence') continue;
    echo "\n=== $t ===\n";
    $cols = $db->query("PRAGMA table_info($t)")->fetchAll(PDO::FETCH_ASSOC);
    echo "Columns:\n";
    foreach ($cols as $c) {
        echo "  {$c['name']} ({$c['type']})" . ($c['pk'] ? ' PK' : '') . ($c['notnull'] ? ' NOT NULL' : '') . ($c['dflt_value'] !== null ? ' DEFAULT=' . $c['dflt_value'] : '') . "\n";
    }
    // Foreign keys
    $fks = $db->query("PRAGMA foreign_key_list($t)")->fetchAll(PDO::FETCH_ASSOC);
    if ($fks) {
        echo "Foreign Keys:\n";
        foreach ($fks as $fk) {
            echo "  {$fk['from']} -> {$fk['table']}.{$fk['to']} ({$fk['on_delete']})\n";
        }
    }
    // Indexes
    $idxs = $db->query("PRAGMA index_list($t)")->fetchAll(PDO::FETCH_ASSOC);
    if ($idxs) {
        echo "Indexes:\n";
        foreach ($idxs as $idx) {
            $colInfo = $db->query("PRAGMA index_info({$idx['name']})")->fetchAll(PDO::FETCH_ASSOC);
            $cols = array_column($colInfo, 'name');
            echo "  {$idx['name']}: " . implode(', ', $cols) . ($idx['unique'] ? ' UNIQUE' : '') . "\n";
        }
    }
}