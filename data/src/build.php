<?php
echo "Building database...\n";
// Kommandozeilen-Optionen parsen
$options = getopt('', ['lang:', 'exclude:', 'include:']);

// Sprachen
$languages = [];
if (!empty($options['lang'])) {
    $languages = explode(',', $options['lang']);
} else {
    // Automatische Erkennung
    foreach (glob('data/src/*/*.json') as $file) {
        $lang = pathinfo($file, PATHINFO_FILENAME);
        $languages[] = $lang;
    }
    $languages = array_unique($languages);
}

// Auszuschließende Kategorien
$exclude = !empty($options['exclude']) ? explode(',', $options['exclude']) : [];

// Einzuschließende Kategorien
$include = !empty($options['include']) ? explode(',', $options['include']) : [];

// Überprüfen, ob include und exclude gleichzeitig verwendet werden
if (!empty($include) && !empty($exclude)) {
    echo "Warnung: --include und --exclude können nicht gleichzeitig verwendet werden. --exclude wird ignoriert.\n";
    $exclude = [];
}

// Build für jede Sprache
foreach ($languages as $lang) {
    $dbFile = __DIR__ . "/../tsp.$lang.db";
  
    echo "Building database for language: $lang\n";
    
    // DB-Datei löschen, falls vorhanden
    if (file_exists($dbFile)) {
      unlink($dbFile);
    }
    // DB-Datei erstellen/öffnen
    $db = new SQLite3($dbFile);
    // Tabellen erstellen
    $db->exec('
        CREATE TABLE IF NOT EXISTS characters (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            value TEXT NOT NULL,
            name TEXT NOT NULL
        );
        
        CREATE VIRTUAL TABLE IF NOT EXISTS search USING fts5(
            character_id UNINDEXED,
            name,
            aliases,
            tokenize = "porter unicode61",
            prefix="3 4 5 6"
        );
    ');
    
    // Kategorien durchgehen
    foreach (glob(__DIR__ . "/*/*.json") as $file) {
        $category = basename(dirname($file));
        $fileLang = pathinfo($file, PATHINFO_FILENAME);
        
        // Nur aktuelle Sprache und gewünschte Kategorien
        if ($fileLang !== $lang) continue;
        if (!empty($include) && !in_array($category, $include)) continue;
        if (!empty($exclude) && in_array($category, $exclude)) continue;
        
        echo "  Processing category: $category\n";
        
        $json = json_decode(file_get_contents($file), true);
        foreach ($json['characters'] as $char) {
            // Zeichen einfügen
            $stmt = $db->prepare('
                INSERT INTO characters (value, name)
                VALUES (:value, :name)
            ');
            $stmt->bindValue(':value', $char['value'], SQLITE3_TEXT);
            $stmt->bindValue(':name', $char['name'], SQLITE3_TEXT);
            $stmt->execute();
            
            $charId = $db->lastInsertRowID();
            $aliases = isset($char['aliases']) ? implode(' ', $char['aliases']) : '';
            
            // Aliases für Suche einfügen
            $stmt = $db->prepare('
              INSERT INTO search (character_id, name, aliases)
              VALUES (:id, :name, :aliases)
            ');
            $stmt->bindValue(':id', $charId, SQLITE3_INTEGER);
            $stmt->bindValue(':name', $char['name'], SQLITE3_TEXT);
            $stmt->bindValue(':aliases', $aliases, SQLITE3_TEXT);
            $stmt->execute();
        }
    }
    
    // DB optimieren
    $db->exec('VACUUM;');
    $db->close();
    
    echo "Finished building database for $lang\n";
}

echo "Finished building all databases\n";