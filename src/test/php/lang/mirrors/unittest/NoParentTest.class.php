<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\{ClassFormatException, IllegalStateException};
use unittest\{Expect, Test};

abstract class NoParentTest extends \unittest\TestCase {
  use TypeDefinition;

  #[Test, Expect(IllegalStateException::class)]
  public function return_type() {
    $this->mirror('{ /** @return parent */ public function fixture() { } }', [])
      ->methods()
      ->named('fixture')
      ->returns()
    ;
  }

  #[Test, Expect(IllegalStateException::class)]
  public function parameter_type() {
    $this->mirror('{ /** @param parent */ public function fixture($param) { } }', [])
      ->methods()
      ->named('fixture')
      ->parameters()
      ->first()
      ->type()
    ;
  }

  #[Test, Expect(ClassFormatException::class)]
  public function annotation_value() {
    $this->mirror("{ #[Fixture(eval: 'new parent()')]\npublic function fixture() { } }", [])
      ->methods()
      ->named('fixture')
      ->annotations()
      ->named('fixture')
      ->value()
    ;
  }
}