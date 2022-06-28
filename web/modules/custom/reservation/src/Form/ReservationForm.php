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
    $this->messenger()->addStatus(($this->t('Your reservation has been created',)));

    $this->messenger()->addStatus(($this->t('Reservation time is:  @date', [
      '@date' => $form_state->getValue('start_time')])));

    $this->messenger()->addStatus(($this->t('Contact name is:  @name', [
      '@name' => $form_state->getValue('contact_name')])));

    $this->messenger()->addStatus(($this->t('Contact email is:  @email', [
      '@email' => $form_state->getValue('contact_e_mail')])));

    /**
     * add Node
     */
    $node = Node::create(['type' => 'reservation']);
    $node->setTitle($form_state->getValue('contact_name'));
    $node->set('field_e_mail',$form_state->getValue('contact_e_mail'));

    $dateTimeWithZone = new DrupalDateTime($form_state->getValue('start_time'));
    $dateTimeWithZone->setTimezone(new \DateTimeZone('UTC'));
    $dateTimeWithZone = $dateTimeWithZone->format('Y-m-d\TH:i:s');

    $node->set('field_start_date', $dateTimeWithZone);
    $node->set('field_confirmed', 1);
    $node->save();

    $url = \Drupal\Core\Url::fromUri('https://reservation.ddev.site/');
    $form_state->setRedirectUrl($url);
  }

}




//use Drupal\Core\Entity\ContentEntityForm;
//use Drupal\Core\Form\FormStateInterface;
//
///**
// * Form controller for the reservation entity edit forms.
// */
//class ReservationForm extends ContentEntityForm {
//
//  /**
//   * {@inheritdoc}
//   */
//  public function save(array $form, FormStateInterface $form_state) {
//    $result = parent::save($form, $form_state);
//
//    $entity = $this->getEntity();
//
//    $message_arguments = ['%label' => $entity->toLink()->toString()];
//    $logger_arguments = [
//      '%label' => $entity->label(),
//      'link' => $entity->toLink($this->t('View'))->toString(),
//    ];
//
//    switch ($result) {
//      case SAVED_NEW:
//        $this->messenger()->addStatus($this->t('New reservation %label has been created.', $message_arguments));
//        $this->logger('reservation')->notice('Created new reservation %label', $logger_arguments);
//        break;
//
//      case SAVED_UPDATED:
//        $this->messenger()->addStatus($this->t('The reservation %label has been updated.', $message_arguments));
//        $this->logger('reservation')->notice('Updated reservation %label.', $logger_arguments);
//        break;
//    }
//
//    $form_state->setRedirect('entity.reservation.canonical', ['reservation' => $entity->id()]);
//
//    return $result;
//  }
//
//}
