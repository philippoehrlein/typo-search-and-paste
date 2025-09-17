<?php

return [
  'typo-search-and-paste' => [
    // Activate the dynamic loading of the FTS5 extension
    // Only activate if FTS5 is not natively available
    // and the fts5.so file is available in the system
    'enableFts5ExtensionLoading' => false,
    
    // Alternative paths for the FTS5 extension (if the standard path doesn't work)
    'fts5ExtensionLoadingPaths' => [
      'fts5.so',
      '/usr/lib/sqlite3/fts5.so',
      '/usr/local/lib/sqlite3/fts5.so',
      '/opt/homebrew/lib/sqlite3/fts5.so'
    ],
  ]
];
