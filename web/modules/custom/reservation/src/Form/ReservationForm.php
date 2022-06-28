<?php

/**
 * @file
 * Contains \Drupal\reservation\Form\ReservationForm.
 */

namespace Drupal\reservation\Form;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\reservation\Service\ReservationService;


class ReservationForm extends FormBase {

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

        $times[$time] = $formattedTime;
      }
    }
    //    var_dump($times);die();

    $form['title'] = [
      '#type' => 'textfield',
      '#title' => t('Title:'),
      '#required' => TRUE,
    ];

    $form['body'] = [
      '#type' => 'textfield',
      '#title' => t('Body:'),
      '#required' => TRUE,
    ];
    $form['field_e_mail'] = [
      '#type' => 'email',
      '#title' => t('Email:'),
      '#required' => TRUE,
    ];
//
//    $form['start_date'] = [
//      '#type' => 'date',
//      '#title' => t('Start date'),
//      '#required' => TRUE,
//    ];

    $form['field_start_date'] = [
      '#type' => 'radios',
      '#title' => ('Start time'),
      '#options' => $times,
      '#required' => TRUE,

    ];

    $form['field_confirmed'] = [
      '#type' => 'checkbox',
      '#title' => t('Confirmed?'),
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

//  public function submitForm(array &$form, FormStateInterface $form_state) {
//    // TODO: Implement submitForm() method.
//  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    // drupal_set_message($this->t('@can_name ,Your application is being submitted!', array('@can_name' => $form_state->getValue('candidate_name'))));
    foreach ($form_state->getValues() as $key => $value) {
      \Drupal::messenger()->addStatus($key . ': ' . $value);
    }
  }

}

