<?php

/**
 * @file
 * Contains \Drupal\reservation\Form\ReservationForm.
 */

namespace Drupal\reservation\Form;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\reservation\Service\ReservationService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for the reservation list entity edit forms.
 */
class ReservationForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $result = parent::save($form, $form_state);

    $entity = $this->getEntity();

    $message_arguments = ['%label' => $entity->toLink()->toString()];
    $logger_arguments = [
      '%label' => $entity->label(),
      'link' => $entity->toLink($this->t('View'))->toString(),
    ];

    switch ($result) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('New reservation %label has been created.', $message_arguments));
        $this->logger('reservation')->notice('Created new reservation %label', $logger_arguments);
        break;

      case SAVED_UPDATED:
        $this->messenger()->addStatus($this->t('The reservation %label has been updated.', $message_arguments));
        $this->logger('reservation')->notice('Updated reservation %label.', $logger_arguments);
        break;
    }

    $form_state->setRedirect('entity.reservation.canonical', ['reservation' => $entity->id()]);

    return $result;
  }

}
