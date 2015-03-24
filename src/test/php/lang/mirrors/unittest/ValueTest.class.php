<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\mirrors\parse\Value;

class ValueTest extends \unittest\TestCase {
  private $type;

  /** @return void */
  public function setUp() {
    $this->type= new TypeMirror(__CLASS__);
  }

  #[@test]
  public function resolve_returns_backing_value() {
    $this->assertEquals(1, (new Value(1))->resolve($this->type));
  }
}