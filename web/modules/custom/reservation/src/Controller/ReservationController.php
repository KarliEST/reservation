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
    $tests = $reservationService->availTimes();

    foreach ($tests as $key => $value) {
      if ($value) {
        $result = 'TRUE';
      }else{
        $result = 'FALSE';
      }
      $times[$key] = ['time'=>$key,'available'=>$result];
    }

    return [
      '#theme' => 'reservation_list',
      '#items' => $times,
      '#attached'=>['library'=>['reservation/reservation']]
    ];

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
