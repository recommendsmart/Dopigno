<?php

namespace Drupal\log\Plugin\Action;

/**
 * Action that reschedules a log entity.
 *
 * @Action(
 *   id = "log_reschedule_action",
 *   label = @Translation("Reschedules a log"),
 *   type = "log",
 *   confirm_form_route_name = "log.log_schedule_action_form"
 * )
 */
class LogReschedule extends LogActionBase {}
