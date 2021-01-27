<?php

namespace Drupal\log\Plugin\Action;

/**
 * Action that clones a log entity.
 *
 * @Action(
 *   id = "log_clone_action",
 *   label = @Translation("Clones a log"),
 *   type = "log",
 *   confirm_form_route_name = "log.log_clone_action_form"
 * )
 */
class LogClone extends LogActionBase {}
