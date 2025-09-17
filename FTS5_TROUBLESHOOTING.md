# FTS5 Troubleshooting

## Problem

The plugin uses SQLite FTS5 for full-text search. On some shared hosting environments, FTS5 is not available by default, which causes the following error:

```
SQLite3::prepare(): Unable to prepare statement: no such module: fts5
```

## Solution

The plugin automatically implements a fallback strategy:

1. **Automatic Detection**: The plugin automatically checks if FTS5 is available
2. **Graceful Fallback**: If FTS5 is not available, it automatically falls back to LIKE-based search
3. **No Functionality Loss**: Search continues to work, only with slightly reduced relevance ranking

## Advanced Configuration

If you want to manually enable FTS5, you can add the following configuration to your `site/config/config.php`:

```php
return [
  'typo-search-and-paste' => [
    // Enables dynamic loading of FTS5 extension
    'enableFts5ExtensionLoading' => true,
    
    // Alternative paths for FTS5 extension
    'fts5ExtensionLoadingPaths' => [
      'fts5.so',
      '/usr/lib/sqlite3/fts5.so',
      '/usr/local/lib/sqlite3/fts5.so',
      '/opt/homebrew/lib/sqlite3/fts5.so'
    ],
  ]
];
```

## Security Notes

- Dynamic loading of extensions can pose security risks
- Only use this option if you are sure the extension is trustworthy
- The default configuration is secure and works without extensions

## Performance

- **FTS5**: Optimal performance with relevance ranking
- **LIKE Fallback**: Good performance with simple text search
- The difference is minimal in practice