<?php
use Civi\API\Event\PrepareEvent;

/**
 * Class for changing the CiviCRM API limit settings.
 */
class CRM_Apilimitsettings_Event_Listener_ChangeApiSetting {

  /**
   * Change the number of results returned.
   *
   * @param \Civi\API\Event\PrepareEvent $event
   *   API Prepare Event Object.
   */
  public static function changeLimit(PrepareEvent $event): void {
    $apiRequest = $event->getApiRequest();
    if (!self::shouldRun($apiRequest)) {
      return;
    }

    $apiRequest['params']['options']['limit'] = 0;
    $event->setApiRequest($apiRequest);
  }

  /**
   * Check if the event should run.
   *
   * @param array $apiRequest
   *   Api Request details.
   * @return bool
   *   True if the Event should run.
   */
  private static function shouldRun($apiRequest): bool {
    if ($apiRequest['version'] !== 3) {
      return FALSE;
    }

    if ($apiRequest['action'] !== 'get') {
      return FALSE;
    }

    if (isset($apiRequest['params']['options']['limit'])) {
      return FALSE;
    }

    return TRUE;
  }

}
