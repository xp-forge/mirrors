<?php namespace lang\mirrors\unittest;

use lang\XPClass;
use unittest\TestCase;
use unittest\PrerequisitesNotMetError;

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
   * @param  unittest.TestCase $t
   * @return void
   */
  public function beforeTest(TestCase $t) {
    $this->verifyNotOnHHVM();
  }

  /**
   * Runs after test
   *
   * @param  unittest.TestCase $t
   * @return void
   */
  public function afterTest(TestCase $t) {
    // Empty
  }
}