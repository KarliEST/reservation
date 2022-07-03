<?php

namespace Drupal\reservation;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a reservation entity type.
 */
interface ReservationInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
