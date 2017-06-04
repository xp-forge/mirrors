<?php namespace lang\mirrors\unittest;

use unittest\TestCase;

class NotOnHHVM implements \unittest\TestAction {

  /**
   * Returns whether we're running on HHVM runtime
   *
   * @return bool
   */
  public static function runtime() {
    return defined('HHVM_VERSION');
  }

  /**
   * Runs before test
   *
   * @param  unittest.TestCase $t
   * @return void
   */
  public function beforeTest(TestCase $t) {
    if (self::runtime()) {
      $t->skip('This test is not intended to run on HHVM');
    }
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