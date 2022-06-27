<?php

namespace Drupal\reservation\Controller;

use Drupal\Core\Controller\ControllerBase;
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
      '#theme' => 'list_reservations',
      '#items' => $times,
      '#attached'=>['library'=>['reservation/reservation']]
    ];

  }


  public function test() {
    /*** @var ReservationService $reservationService */
    $reservationService = \Drupal::service(ReservationService::SERVICE_ID);
    $availTimes = $reservationService->availTimes();
    $getAvailTimes = $reservationService->getAvailTimes();
    var_dump($setDate);die();
  }

  /**
   * Builds the response.
   */
  public function example() {
    $example = new ReservationService();
    $result = $example->getExample();
    return $result;
  }


  //  public function showAvailableTimes() {
  //    /*** @var ReservationService $reservationService */
  //    $reservationService = \Drupal::service(ReservationService::SERVICE_ID);
  //
  //    return new JsonResponse([
  //      'data' => $reservationService->availTimes(),
  //      'method' => 'GET',
  //      'status' => 200,
  //    ]);
  //
  //  }

}
