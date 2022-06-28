<?php

namespace Drupal\reservation\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\reservation\Service\ReservationService;


/**
 * Returns responses for Reservation routes.
 */
class ReservationController extends ControllerBase {


  public function showAvailableTimes() {
    /*** @var ReservationService $reservationService */
    $reservationService = \Drupal::service(ReservationService::SERVICE_ID);
    $times = $reservationService->availTimes();
    return [
      '#theme' => 'reservation_list',
      '#items' => $times,
      '#attached' => ['library' => ['reservation/reservation']],
    ];
  }


  public function reservationForm() {
    $form = \Drupal::formBuilder()
      ->getForm('Drupal\reservation\Form\ReservationForm');
    return [
      'form' => $form,
    ];
  }

  public function test() {
    $form = \Drupal::formBuilder()
      ->getForm('Drupal\reservation\Form\ReservationForm');
    return [
      'form' => $form,
    ];
  }


}
