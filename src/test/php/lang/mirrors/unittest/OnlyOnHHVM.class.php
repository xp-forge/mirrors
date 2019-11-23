<?php namespace lang\mirrors\unittest;

use lang\XPClass;
use unittest\PrerequisitesNotMetError;
use unittest\Test;

class OnlyOnHHVM implements \unittest\TestAction, \unittest\TestClassAction {

  /**
   * Verifies HHVM
   *
   * @return void
   */
  private function verifyOnHHVM() {
    if (!defined('HHVM_VERSION')) {
      throw new PrerequisitesNotMetError('This test can only be run on HHVM', null, ['hhvm']);
    }
  }

  /**
   * Runs before test
   *
   * @param  lang.XPClass $c
   * @return void
   */
  public function beforeTestClass(XPClass $c) {
    $this->verifyOnHHVM();
  }

  /**
   * Runs after test
   *
   * @param  lang.XPClass $c
   * @return void
   */
  public function afterTestClass(XPClass $c) {
    // Empty
  }

  /**
   * Runs before test
   *
   * @param  unittest.Test $t
   * @return void
   */
  public function beforeTest(Test $t) {
    $this->verifyOnHHVM();
  }

  /**
   * Runs after test
   *
   * @param  unittest.Test $t
   * @return void
   */
  public function afterTest(Test $t) {
    // Empty
  }
}