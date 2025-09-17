<?php

use Kirby\Cms\App as Kirby;

Kirby::plugin('philippoehrlein/typo-search-and-paste', [
  'translations' => require __DIR__ . '/config/translations.php',
  'api' => require __DIR__ . '/config/api.php',
  'options' => require __DIR__ . '/config/config.php',
  'version' => '1.1.0'
]);
