<?php

namespace Drupal\reservation\Service;

use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\mailsystem\MailsystemManager;
use Drupal\webprofiler\Entity\EntityManagerWrapper;
use Drupal\webprofiler\Mail\MailManagerWrapper;

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

  protected $entityTypeManager;
  protected $dateFormatter;
  protected $mailManager;


  /**
   * @param $entityTypeManager
   */
  public function __construct(EntityTypeManager $entityTypeManager, DateFormatter $dateFormatter, MailsystemManager $mailManager) {
    $this->entityTypeManager = $entityTypeManager;
    $this->dateFormatter = $dateFormatter;
    $this->mailManager = $mailManager;
  }


  /**
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function availTimes(): array {
    $availTimes = $this->getAvailTimes();
    foreach ($availTimes as $key => $value) {
      if ($value) {
        $result = 'TRUE';
      } else {
        $result = 'FALSE';
      }
      $times[$key] = ['time' => $key, 'available' => $result];
    }
    return $times;
  }

  public function getAvailTimes(): array {

    $nodeStorage = $this->entityTypeManager->getStorage('node');
    $reservationIds = $nodeStorage->getQuery()
      ->condition('type', 'reservation')
      ->condition('field_start_date', date('Y-m-d') . 'T00:00:00', '>')
      ->condition('field_start_date', date('Y-m-d') . 'T23:59:59', '<')
      ->condition('field_confirmed', 1)
      ->execute();
    $availTimes = self::AVAILABLE_TIMES;

    foreach ($reservationIds as $reservationId) {
      /*** @var \Drupal\node\NodeInterface $reservation */
      $reservation = $nodeStorage->load($reservationId);

      $date_original = new DrupalDateTime($reservation->field_start_date->value, 'UTC');
      $dateTime = $this->dateFormatter->format($date_original->getTimestamp(), 'custom', 'Y-m-d H:i:s');
      $reservationHour = (new \DateTime($dateTime))->format('G');
      $availTimes[$reservationHour] = FALSE;
    }
    return $availTimes;
  }

  public function sendEmail($reservationTime, $contactName, $contactEmail) {

    $module = 'reservation';
    $key = 'reservationId';
    $to = $contactEmail;
    $langcode = 'en';
    $params['contact_name'] = $contactName;
    $params['reservation_time'] = $reservationTime;
    $send = TRUE;

    $this->mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);

    return [];
  }

}
