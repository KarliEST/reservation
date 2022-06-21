<?php

namespace Drupal\reservation\Service;

use Drupal\Core\Datetime\DrupalDateTime;

class ReservationService {

  const SERVICE_ID = 'reservation.reservation_service';

  const AVAILABLE_TIMES = [
    8 => TRUE,
    9 => TRUE,
    10 => TRUE,
    11 => TRUE,
    12 => TRUE,
    13 => TRUE,
    14 => TRUE,
    15 => TRUE,
    16 => TRUE,
    17 => TRUE,
    18 => TRUE,
    19 => TRUE,
    20 => TRUE,
    21 => TRUE,
  ];

  public function getExample() {
    $build['content'] = [
      '#markup' => 'KOOD TÖÖTAB!!!!!!!!!!!!!!!!',
    ];
    return $build;
  }

  /**
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function availTimes(): array {

    $nodeStorage = \Drupal::entityTypeManager()->getStorage('node');

    $reservationIds = $nodeStorage->getQuery()
      ->condition('type', 'reservation')
      ->condition('field_start_date', '2022-06-17T00:00:00', '>')
      ->condition('field_start_date', '2022-06-17T23:59:59', '<')
      ->condition('field_confirmed', 1)
      ->execute();

    $availTimes = self::AVAILABLE_TIMES;

    foreach ($reservationIds as $reservationId) {
      /*** @var \Drupal\node\NodeInterface $reservation */
      $reservation = $nodeStorage->load($reservationId);


      $date_original = new DrupalDateTime($reservation->field_start_date->value, 'UTC');
      $dateTime = \Drupal::service('date.formatter')
        ->format($date_original->getTimestamp(), 'custom', 'Y-m-d H:i:s');
      $reservationHour = (new \DateTime($dateTime))->format('G');
      unset($availTimes[$reservationHour]);

    }
    return $availTimes;
  }

}
