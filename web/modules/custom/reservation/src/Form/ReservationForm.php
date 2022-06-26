<?php

/**
 * @file
 * Contains \Drupal\reservation\Form\ReservationForm.
 */

namespace Drupal\reservation\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\reservation\Service\ReservationService;

class ReservationForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'reservation_form';
  }


  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    /*** @var ReservationService $reservationService */
    $reservationService = \Drupal::service(ReservationService::SERVICE_ID);
    $availTimes = $reservationService->availTimes();

    foreach ($availTimes as $key => $value) {
      if ($value) {
        $result = 'TRUE';
      }else{
        $result = 'FALSE';
      }
      $times[$key] = ['time'=>$key,'available'=>$result];
    }

    $form['title'] = array(
      '#type' => 'textfield',
      '#title' => t('Title:'),
      '#required' => TRUE,
    );
    $form['body'] = array(
      '#type' => 'textfield',
      '#title' => t('Body:'),
      '#required' => TRUE,
    );
    $form['field_e_mail'] = array(
      '#type' => 'email',
      '#title' => t('Email:'),
      '#required' => TRUE,
    );
    $form['field_start_date'] = array(
      '#type' => 'date',
      '#title' => t('Start date'),
      '#required' => TRUE,
    );
    $form['candidate_confirmation'] = array(
      '#type' => 'radios',
      '#title' => ('Start time'),
      '#options' => array(
        $times

//        'Yes' => t('Yes'),
//        'No' => t('No')
      ),
    );
    $form['field_confirmed'] = array(
      '#type' => 'checkbox',
      '#title' => t('Confirmed.'),
    );
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    );
    return $form;
  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
// drupal_set_message($this->t('@can_name ,Your application is being submitted!', array('@can_name' => $form_state->getValue('candidate_name'))));
    foreach ($form_state->getValues() as $key => $value) {
      drupal_set_message($key . ': ' . $value);
    }
  }


}
