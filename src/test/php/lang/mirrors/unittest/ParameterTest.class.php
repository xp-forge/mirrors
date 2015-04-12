<?php namespace lang\mirrors\unittest;

use lang\mirrors\Parameter;
use lang\mirrors\Method;
use lang\mirrors\TypeMirror;
use lang\IllegalArgumentException;

class ParameterTest extends \unittest\TestCase {

  private function noArg() { }
  private function oneArg($arg) { }

  #[@test]
  public function can_create_from_method_and_offset() {
    new Parameter(new Method(new TypeMirror(self::class), 'oneArg'), 0);
  }

  #[@test]
  public function can_create_from_method_and_parameter() {
    new Parameter(
      new Method(new TypeMirror(self::class), 'oneArg'),
      new \ReflectionParameter([__CLASS__, 'oneArg'], 0)
    );
  }
}