<?php

namespace Drupal\reservation\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormBuilder;
use Drupal\reservation\Service\ReservationService;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Returns responses for Reservation routes.
 */
class ReservationController extends ControllerBase {

  protected $reservationService;

  protected $formBuilder;

  public function __construct(ReservationService $reservationService, FormBuilder $formBuilder) {
    $this->reservationService = $reservationService;
    $this->formBuilder = $formBuilder;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get(ReservationService::SERVICE_ID),
      $container->get('form_builder')
    );
  }

  public function showAvailableTimes() {
    $times = $this->reservationService->availTimes();
    return [
      '#theme' => 'reservation_list',
      '#items' => $times,
      '#attached' => ['library' => ['reservation/reservation']],
    ];
  }

  public function reservationForm() {
    $form = $this->formBuilder->getForm('Drupal\reservation\Form\CustomReservationForm');
    return [
      'form' => $form,
    ];
  }

}
