<?php namespace lang\mirrors\unittest;

use lang\XPClass;
use unittest\PrerequisitesNotMetError;
use unittest\Test;

class NotOnHHVM implements \unittest\TestAction, \unittest\TestClassAction {

  /**
   * Verifies HHVM
   *
   * @return void
   */
  private function verifyNotOnHHVM() {
    if (defined('HHVM_VERSION')) {
      throw new PrerequisitesNotMetError('This test can not be run on HHVM', null, ['php']);
    }
  }

  /**
   * Runs before test
   *
   * @param  lang.XPClass $c
   * @return void
   */
  public function beforeTestClass(XPClass $c) {
    $this->verifyNotOnHHVM();
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
    $this->verifyNotOnHHVM();
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