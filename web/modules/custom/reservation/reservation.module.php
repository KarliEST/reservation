<?php

/**
 * Implements hook_theme().
 */
function reservation_theme($existing, $type, $theme, $path) {
  return [
    'reservation_list' => [
      'variables' => [
        'items' => [],
      ],
    ],
  ];
}
