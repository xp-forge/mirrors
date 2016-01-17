<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\IllegalStateException;
use lang\ClassFormatException;

abstract class NoParentTest extends \unittest\TestCase {
  use TypeDefinition;

  #[@test, @expect(IllegalStateException::class)]
  public function return_type() {
    $this->mirror('{ /** @return parent */ public function fixture() { } }', [])
      ->methods()
      ->named('fixture')
      ->returns()
    ;
  }

  #[@test, @expect(IllegalStateException::class)]
  public function parameter_type() {
    $this->mirror('{ /** @param parent */ public function fixture($param) { } }', [])
      ->methods()
      ->named('fixture')
      ->parameters()
      ->first()
      ->type()
    ;
  }

  #[@test, @expect(ClassFormatException::class)]
  public function annotation_value() {
    $this->mirror("{ #[@fixture(new parent())]\npublic function fixture() { } }", [])
      ->methods()
      ->named('fixture')
      ->annotations()
      ->named('fixture')
      ->value()
    ;
  }
}