<?php

namespace Drupal\Tests\log\Functional;

/**
 * Tests the Log form actions.
 *
 * @group Log
 */
class LogActionsTest extends LogTestBase {

  /**
   * Tests cloning a single log.
   */
  public function testCloneSingleLog() {
    $log = $this->createLogEntity([
      'name' => $this->randomMachineName(),
      'created' => \Drupal::time()->getRequestTime(),
      'done' => TRUE,
      'timestamp' => 386121600,
    ]);
    $log->save();

    $num_of_logs = $this->storage->getQuery()->count()->execute();
    $this->assertEqual($num_of_logs, 1, 'There is one log in the system.');

    $edit = [];
    $edit['action'] = 'log_clone_action';
    $edit['entities[1]'] = TRUE;
    $this->drupalPostForm('admin/content/log', $edit, $this->t('Apply to selected items'));
    $this->assertResponse(200);
    $this->assertText($this->t('Are you sure you want to clone this log?'));
    $this->assertText($this->t('New date'));

    $edit_clone = [];
    $edit_clone['date[month]'] = 12;
    $edit_clone['date[year]'] = 1981;
    $edit_clone['date[day]'] = 3;
    $this->drupalPostForm(NULL, $edit_clone, $this->t('Clone'));
    $this->assertResponse(200);
    $this->assertUrl('admin/content/log');
    $this->assertText($this->t('Cloned 1 log'));
    $logs = $this->storage->loadMultiple();
    $this->assertEqual(count($logs), 2, 'There are two logs in the system.');
    $timestamps = [];
    foreach ($logs as $log) {
      $timestamps[] = $log->get('timestamp')->value;
    }
    $this->assertEqual($timestamps, [386121600, 376146000], 'Timestamp on the new log has been updated.');
  }

  /**
   * Tests cloning multiple logs.
   */
  public function testCloneMultipleLogs() {
    $timestamps = [
      386121600,
      286121600,
      186121600,
    ];
    $expected_timestamps = [];
    foreach ($timestamps as $timestamp) {
      $expected_timestamps[] = $timestamp;
      $log = $this->createLogEntity([
        'name' => $this->randomMachineName(),
        'created' => \Drupal::time()->getRequestTime(),
        'done' => TRUE,
        'timestamp' => $timestamp,
      ]);
      $log->save();
    }

    $num_of_logs = $this->storage->getQuery()->count()->execute();
    $this->assertEqual($num_of_logs, 3, 'There are three logs in the system.');

    $edit = [];
    $edit['action'] = 'log_clone_action';
    for ($i = 1; $i <= 3; $i++) {
      $edit['entities[' . $i . ']'] = TRUE;
    }
    $this->drupalPostForm('admin/content/log', $edit, $this->t('Apply to selected items'));
    $this->assertResponse(200);
    $this->assertText($this->t('Are you sure you want to clone these logs?'));
    $this->assertText($this->t('New date'));

    $edit_clone = [];
    $edit_clone['date[month]'] = 12;
    $edit_clone['date[year]'] = 1981;
    $edit_clone['date[day]'] = 3;
    $this->drupalPostForm(NULL, $edit_clone, $this->t('Clone'));
    $this->assertResponse(200);
    $this->assertUrl('admin/content/log');
    $this->assertText($this->t('Cloned 3 logs'));

    $logs = $this->storage->loadMultiple();
    $this->assertEqual(count($logs), 6, 'There are six logs in the system.');
    for ($i = 1; $i <= 3; $i++) {
      $expected_timestamps[] = 376146000;
    }
    $log_timestamps = [];
    foreach ($logs as $log) {
      $log_timestamps[] = $log->get('timestamp')->value;
    }
    $this->assertEqual($log_timestamps, $expected_timestamps, 'Timestamp on the new logs has been updated.');
  }

  /**
   * Tests rescheduling a single log to an absolute date.
   */
  public function testRescheduleSingleLogAbsolute() {
    $log = $this->createLogEntity([
      'name' => $this->randomMachineName(),
      'created' => \Drupal::time()->getRequestTime(),
      'done' => TRUE,
      'timestamp' => 386121600,
    ]);
    $log->save();

    $num_of_logs = $this->storage->getQuery()->count()->execute();
    $this->assertEqual($num_of_logs, 1, 'There is one log in the system.');

    $edit = [];
    $edit['action'] = 'log_reschedule_action';
    $edit['entities[1]'] = TRUE;
    $this->drupalPostForm('admin/content/log', $edit, $this->t('Apply to selected items'));
    $this->assertResponse(200);
    $this->assertText($this->t('Are you sure you want to reschedule this log?'));
    $this->assertText($this->t('New date'));

    $edit_reschedule = [];
    $edit_reschedule['date[month]'] = 01;
    $edit_reschedule['date[year]'] = 2037;
    $edit_reschedule['date[day]'] = 01;
    $this->drupalPostForm(NULL, $edit_reschedule, $this->t('Reschedule'));
    $this->assertResponse(200);
    $this->assertUrl('admin/content/log');
    $this->assertText($this->t('Rescheduled 1 log'));

    $logs = $this->storage->loadMultiple();
    $this->assertEqual($num_of_logs, 1, 'There is one log in the system.');
    $log = reset($logs);
    $this->assertEqual($log->get('timestamp')->value, '2114341200', 'Timestamp on the log has changed.');
    $this->assertEqual($log->get('status')->value, 'pending', 'Log has been set to pending.');
  }

  /**
   * Tests rescheduling multiple logs to an absolute date.
   */
  public function testRescheduleMultipleLogsAbsolute() {
    $timestamps = [
      386121600,
      286121600,
      186121600,
    ];
    $expected_timestamps = [];
    foreach ($timestamps as $timestamp) {
      $expected_timestamps[] = $timestamp;
      $log = $this->createLogEntity([
        'name' => $this->randomMachineName(),
        'created' => \Drupal::time()->getRequestTime(),
        'done' => TRUE,
        'timestamp' => $timestamp,
      ]);
      $log->save();
    }

    $num_of_logs = $this->storage->getQuery()->count()->execute();
    $this->assertEqual($num_of_logs, 3, 'There are three logs in the system.');

    $edit = [];
    $edit['action'] = 'log_reschedule_action';
    for ($i = 1; $i <= 3; $i++) {
      $edit['entities[' . $i . ']'] = TRUE;
    }
    $this->drupalPostForm('admin/content/log', $edit, $this->t('Apply to selected items'));
    $this->assertResponse(200);
    $this->assertText($this->t('Are you sure you want to reschedule these logs?'));
    $this->assertText($this->t('New date'));

    $edit_reschedule = [];
    $edit_reschedule['date[month]'] = 01;
    $edit_reschedule['date[year]'] = 2037;
    $edit_reschedule['date[day]'] = 01;
    $this->drupalPostForm(NULL, $edit_reschedule, $this->t('Reschedule'));
    $this->assertResponse(200);
    $this->assertUrl('admin/content/log');
    $this->assertText($this->t('Rescheduled 3 logs'));

    $logs = $this->storage->loadMultiple();
    $this->assertEqual(count($logs), 3, 'There are three logs in the system.');
    foreach ($logs as $log) {
      $this->assertEqual($log->get('timestamp')->value, '2114341200', 'Timestamp on the log has changed.');
      $this->assertEqual($log->get('status')->value, 'pending', 'Log has been set to pending.');
    }
  }

  /**
   * Tests rescheduling a single log to an relative date.
   */
  public function testRescheduleSingleLogRelative() {
    $log = $this->createLogEntity([
      'name' => $this->randomMachineName(),
      'created' => \Drupal::time()->getRequestTime(),
      'done' => TRUE,
      'timestamp' => 386121600,
    ]);
    $log->save();

    $num_of_logs = $this->storage->getQuery()->count()->execute();
    $this->assertEqual($num_of_logs, 1, 'There is one log in the system.');

    $edit = [];
    $edit['action'] = 'log_reschedule_action';
    $edit['entities[1]'] = TRUE;
    $this->drupalPostForm('admin/content/log', $edit, $this->t('Apply to selected items'));
    $this->assertResponse(200);
    $this->assertText($this->t('Are you sure you want to reschedule this log?'));
    $this->assertText($this->t('New date'));

    $edit_reschedule = [];
    $edit_reschedule['type_of_date'] = 1;
    $this->drupalPostForm(NULL, $edit_reschedule, $this->t('Reschedule'));
    $this->assertResponse(200);
    $this->assertUrl('admin/content/log/reschedule');
    $this->assertText($this->t('Please enter the amount of time for rescheduling.'));

    $edit_reschedule = [];
    $edit_reschedule['type_of_date'] = 1;
    $edit_reschedule['amount'] = 1;
    $edit_reschedule['time'] = 'day';
    $this->drupalPostForm(NULL, $edit_reschedule, $this->t('Reschedule'));
    $this->assertResponse(200);
    $this->assertUrl('admin/content/log');
    $this->assertText($this->t('Rescheduled 1 log'));

    $logs = $this->storage->loadMultiple();
    $this->assertEqual($num_of_logs, 1, 'There is one log in the system.');
    $log = reset($logs);
    $this->assertEqual($log->get('timestamp')->value, '386208000', 'Timestamp on the log has changed.');
    $this->assertEqual($log->get('status')->value, 'pending', 'Log has been set to pending.');
  }

  /**
   * Tests rescheduling multiple logs to an relative date.
   */
  public function testRescheduleMultipleLogsRelative() {
    $timestamps = [
      386121600,
      286121600,
      186121600,
    ];
    $expected_timestamps = [
      383702400,
      283443200,
      183446800,
    ];
    foreach ($timestamps as $timestamp) {
      $log = $this->createLogEntity([
        'name' => $this->randomMachineName(),
        'created' => \Drupal::time()->getRequestTime(),
        'done' => TRUE,
        'timestamp' => $timestamp,
      ]);
      $log->save();
    }

    $num_of_logs = $this->storage->getQuery()->count()->execute();
    $this->assertEqual($num_of_logs, 3, 'There are three logs in the system.');

    $edit = [];
    $edit['action'] = 'log_reschedule_action';
    for ($i = 1; $i <= 3; $i++) {
      $edit['entities[' . $i . ']'] = TRUE;
    }
    $this->drupalPostForm('admin/content/log', $edit, $this->t('Apply to selected items'));
    $this->assertResponse(200);
    $this->assertText($this->t('Are you sure you want to reschedule these logs?'));
    $this->assertText($this->t('New date'));

    $edit_reschedule = [];
    $edit_reschedule['type_of_date'] = 1;
    $edit_reschedule['amount'] = -1;
    $edit_reschedule['time'] = 'month';
    $this->drupalPostForm(NULL, $edit_reschedule, $this->t('Reschedule'));
    $this->assertResponse(200);
    $this->assertUrl('admin/content/log');
    $this->assertText($this->t('Rescheduled 3 logs'));

    $logs = $this->storage->loadMultiple();
    $this->assertEqual(count($logs), 3, 'There are three logs in the system.');
    $log_timestamps = [];
    foreach ($logs as $log) {
      $log_timestamps[] = $log->get('timestamp')->value;
    }
    $this->assertEqual($log_timestamps, $expected_timestamps, 'Logs have been rescheduled');
  }

}
