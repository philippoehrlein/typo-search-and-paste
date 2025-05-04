<?php

use Kirby\Cms\App as Kirby;

Kirby::plugin('philippoehrlein/typo-search-and-paste', [
  'translations' => require __DIR__ . '/config/translations.php',
  'api' => require __DIR__ . '/config/api.php',
  'version' => '1.0.0'
]);
