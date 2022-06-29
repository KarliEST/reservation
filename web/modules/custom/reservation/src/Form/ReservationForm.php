<?php

/**
 * @file
 * Contains \Drupal\reservation\Form\ReservationForm.
 */

namespace Drupal\reservation\Form;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\reservation\Service\ReservationService;


class ReservationForm extends FormBase {

  //  private ReservationService $reservationService;


  public function getFormId() {
    return 'reservation_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    /*** @var ReservationService $reservationService */
    $reservationService = \Drupal::service(ReservationService::SERVICE_ID);
    $times = $reservationService->getAvailTimes();

    foreach ($times as $time => $bool) {
      if (!$bool) {
        unset($times[$time]);
      }
      else {
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

    $form['contact_e_mail'] = [
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
    $contactEmail = $form_state->getValue('contact_e_mail');

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
    $node = Node::create(['type' => 'reservation']);
    $node->setTitle($contactName);
    $node->set('field_e_mail', $contactEmail);
    $node->set('field_start_date', $dateTimeWithZone);
    $node->set('field_confirmed', 1);
    $node->save();

    $reservationService= new ReservationService();
    $reservationService->sendEmail($reservationTime, $contactName, $contactEmail);

    $url = \Drupal\Core\Url::fromUri('https://reservation.ddev.site/');
    $form_state->setRedirectUrl($url);
  }

}
