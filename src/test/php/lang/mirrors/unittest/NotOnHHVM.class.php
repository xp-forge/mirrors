<?php namespace lang\mirrors\unittest;

use unittest\TestCase;

class NotOnHHVM extends \lang\Object implements \unittest\TestAction {

  public static function runtime() {
    return defined('HHVM_VERSION');
  }

  public function beforeTest(TestCase $t) {
    if (self::runtime()) {
      $t->skip('This test is not intended to run on HHVM');
    }
  }

  public function afterTest(TestCase $t) {
    // Empty
  }
}