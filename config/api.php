<?php

return [
  'routes' => function ($kirby) {
    return [
      [
        'pattern' => 'tsp-search/(:any)',
        'method' => 'GET',
        'action' => function ($query) use ($kirby) {
        // get Panel language
        $lang = $kirby->user()->language() ?? 'en';
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

        $searchTerms = array_values(array_filter(explode(' ', $query), function ($term) {
          $term = trim($term, '*');
          if ($term === '') {
            return false;
          }
          return !in_array(strtoupper($term), ['AND', 'OR', 'NOT'], true);
        }));

        $cleanQuery = trim(implode(' ', array_map(function ($term) {
          return trim($term, '*');
        }, $searchTerms)));
        $startMatch = "$cleanQuery%";
        
        $pluginRoot = dirname(__DIR__);
        $dbPath = $pluginRoot . '/data/tsp.' . $lang . '.db';

        if(!file_exists($dbPath)) {
          $dbPath = $pluginRoot . '/data/tsp.en.db';
        }
        
        // check if database exists
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

        if (empty($cleanQuery)) {
          return [
            'status' => 'success',
            'results' => []
          ];
        }
        
        // open database
        $db = new SQLite3($dbPath);
        
        // check if FTS5 is available
        $fts5Available = false;
        
        // check if FTS5 extension is explicitly activated
        $enableFts5Extension = option('philippoehrlein.typo-search-paste.enableFts5ExtensionLoading', false);

        
        if ($enableFts5Extension) {
          $extensionPaths = option('philippoehrlein.typo-search-paste.fts5ExtensionLoadingPaths', ['fts5.so']);
          $extensionLoaded = false;
          
          foreach ($extensionPaths as $path) {
            try {
              if (method_exists($db, 'enableLoadExtension')) {
                // call dynamically to satisfy static analyzers/IDEs
                \call_user_func([$db, 'enableLoadExtension'], true);
              }
              $db->loadExtension($path);
              $extensionLoaded = true;
              break;
            } catch (Exception $e) {
              // try next path
              continue;
            }
          }
        }
        
        try {
          $testStmt = $db->prepare('SELECT fts5_version()');
          $testStmt->execute();
          $fts5Available = true;
        } catch (Exception $e) {
          // FTS5 not available, use fallback
        }
        
        $results = [];
        
        if ($fts5Available) {
          try {
            $stmt = $db->prepare('
              SELECT c.value, c.name, bm25(search) as rank
              FROM characters c
              JOIN search ON c.id = search.character_id
              WHERE search MATCH :query
              ORDER BY
                CASE
                  WHEN c.name = :exactName THEN 1
                  WHEN c.name LIKE :startMatch THEN 2
                  WHEN c.name LIKE :containsMatch THEN 3
                  ELSE 4
                END,
                rank,
                c.name
              LIMIT 20
            ');
            
            $stmt->bindValue(':query', $query, SQLITE3_TEXT);
            $stmt->bindValue(':exactName', $cleanQuery, SQLITE3_TEXT);
            $stmt->bindValue(':startMatch', "$cleanQuery%", SQLITE3_TEXT);
            $stmt->bindValue(':containsMatch', "%$cleanQuery%", SQLITE3_TEXT);
            $result = $stmt->execute();
            
            // collect results
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
              $results[] = [
                'value' => $row['value'],
                'name' => $row['name']
              ];
            }          } catch (Exception $e) {
            // FTS5 error, use fallback
            $fts5Available = false;
          }
        }
        
        if (!$fts5Available) {
          // fallback: LIKE-based search
          if (empty($searchTerms)) {
            $db->close();
            return [
              'status' => 'success',
              'results' => []
            ];
          }

          $whereConditions = [];
          $params = [];
          
          foreach ($searchTerms as $i => $term) {
            $term = trim($term, '*'); // remove wildcards for LIKE
            $whereConditions[] = "(c.name LIKE :term$i OR c.value LIKE :term$i)";
            $params[":term$i"] = "%$term%";
          }
          
          $whereClause = implode(' AND ', $whereConditions);
          
          $stmt = $db->prepare("
            SELECT c.value, c.name, 1 as rank
            FROM characters c
            WHERE $whereClause
            ORDER BY 
              CASE 
                WHEN c.name = :exactName THEN 1
                WHEN c.name LIKE :startMatch THEN 2
                WHEN c.name LIKE :containsMatch THEN 3
                ELSE 4
              END,
              c.name
            LIMIT 20
          ");
          
          // bind parameters
          foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, SQLITE3_TEXT);
          }
          
          // additional parameters for ranking
          $stmt->bindValue(':exactName', $cleanQuery, SQLITE3_TEXT);
          $stmt->bindValue(':containsMatch', "%$cleanQuery%", SQLITE3_TEXT);
          $stmt->bindValue(':startMatch', $startMatch, SQLITE3_TEXT);
          
          $result = $stmt->execute();
          
          // collect results
          while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $results[] = [
              'value' => $row['value'],
              'name' => $row['name']
            ];
          }
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
