<?php

/**
 * Implements hook_theme().
 */
function reservation_theme($existing, $type, $theme, $path) {

  return [
    'reservations_list' => [
      'variables' => [
        'items' => [],
      ],
    ],
  ];
}
