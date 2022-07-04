<?php

namespace Drupal\reservation\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\reservation\Service\ReservationService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for the entity add content.
 */
class CustomReservationForm extends FormBase {

  protected ReservationService $reservationService;

  public function __construct(ReservationService $reservationService) {
    $this->reservationService = $reservationService;
  }

  public static function create(ContainerInterface $container) {
    return new static (
      $container->get(ReservationService::SERVICE_ID)
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'reservation_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $times = $this->reservationService->getAvailTimes();

    foreach ($times as $time => $bool) {
      if (!$bool) {
        unset($times[$time]);
      } else {
        $formattedTime = mktime($time, 00, 00);
        $formattedTime = date('Y-m-d H:i:s', $formattedTime);
        $formattedTimes[$formattedTime] = $formattedTime;
      }
    }


    $form['start_time'] = [
      '#type' => 'radios',
      '#title' => ('Reservation time'),
      '#options' => $formattedTimes,
      '#required' => TRUE,
    ];

    $form['contact_name'] = [
      '#type' => 'textfield',
      '#title' => t('Contact name:'),
      '#required' => TRUE,
    ];

    $form['contact_email'] = [
      '#type' => 'email',
      '#title' => t('Contact email:'),
      '#required' => TRUE,
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    ];
    return $form;
  }


  public function submitForm(array &$form, FormStateInterface $form_state) {
    $reservationTime = $form_state->getValue('start_time');
    $contactName = $form_state->getValue('contact_name');
    $contactEmail = $form_state->getValue('contact_email');

    $dateTimeWithZone = new DrupalDateTime($form_state->getValue('start_time'));
    $dateTimeWithZone->setTimezone(new \DateTimeZone('UTC'));
    $dateTimeWithZone = $dateTimeWithZone->format('Y-m-d\TH:i:s');

    $this->messenger()
      ->addStatus(($this->t('Your reservation has been created',)));

    $this->messenger()->addStatus(($this->t('Reservation time is:  @date', [
      '@date' => $reservationTime,
    ])));

    $this->messenger()->addStatus(($this->t('Contact name is:  @name', [
      '@name' => $contactName,
    ])));

    $this->messenger()->addStatus(($this->t('Contact email is:  @email', [
      '@email' => $contactEmail,
    ])));

    /**
     * add Node
     */

    $node = \Drupal::entityTypeManager()->getStorage('reservation')->create(['type' => '']);
    $node
      ->set('label', $contactName)
      ->set('field_email', $contactEmail)
      ->set('field_start_date', $dateTimeWithZone)
      ->set('field_confirmed', 1);
    $node->save();


    $this->reservationService->sendEmail($reservationTime, $contactName, $contactEmail);

    $url = \Drupal\Core\Url::fromUri('https://reservation.ddev.site/');
    $form_state->setRedirectUrl($url);
  }


}
