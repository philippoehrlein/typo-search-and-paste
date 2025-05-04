<?php

return [
  'routes' => function ($kirby) {
    return [
      [
        'pattern' => 'tsp-search/(:any)',
        'method' => 'GET',
        'action' => function ($query) use ($kirby) {
        // Panel-Sprache abrufen
        $lang = $kirby->language()->code();
        $query = urldecode($query);

        $words = preg_split('/\s+/', $query);
        $query = implode(' ', array_map(function ($w) {
            $w = trim($w);
            $upper = strtoupper($w);
            if (in_array($upper, ['AND', 'OR', 'NOT']) || str_ends_with($w, '*')) {
                return $w;
            }
            return $w . '*';
        }, $words));
        
        // Datenbankpfad
        $dbPath = $kirby->root('plugins') . '/typo-search-and-paste/data/tsp.' . $lang . '.db';

        if(!file_exists($dbPath)) {
          $dbPath = $kirby->root('plugins') . '/typo-search-and-paste/data/tsp.en.db';
        }
        
        // Überprüfen, ob Datenbank existiert
        if (!file_exists($dbPath)) {
          return [
            'status' => 'error',
            'message' => 'Database not found for language: ' . $lang
          ];
        }
        
        
        if (empty($query)) {
          return [
            'status' => 'error',
            'message' => 'No search query provided'
          ];
        }
        
        // Datenbank öffnen
        $db = new SQLite3($dbPath);
        
        // Suche durchführen
        $stmt = $db->prepare('
          SELECT c.value, c.name, bm25(search) as rank
          FROM characters c
          JOIN search ON c.id = search.character_id
          WHERE search MATCH :query
          ORDER BY rank
          LIMIT 20
        ');
        
        $stmt->bindValue(':query', $query, SQLITE3_TEXT);
        $result = $stmt->execute();
        
        // Ergebnisse sammeln
        $results = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
          $results[] = [
            'value' => $row['value'],
            'name' => $row['name']
          ];
        }
        
        $db->close();
        
        return [
          'status' => 'success',
          'results' => $results
        ];
      }
      ]
    ];
  }
];
