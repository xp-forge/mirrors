<?php namespace lang\mirrors\unittest;

use lang\mirrors\Constant;
use lang\mirrors\TypeMirror;

class ConstantTest extends \unittest\TestCase {
  const fixture = 'value';
  private $type;

  /** @return void */
  public function setUp() {
    $this->type= new TypeMirror(self::class);
  }

  #[@test]
  public function name() {
    $this->assertEquals('fixture', (new Constant($this->type, 'fixture', 'value'))->name());
  }

  #[@test]
  public function value() {
    $this->assertEquals('value', (new Constant($this->type, 'fixture', 'value'))->value());
  }

  #[@test]
  public function declaredIn() {
    $this->assertEquals($this->type, (new Constant($this->type, 'fixture', 'value'))->declaredIn());
  }

  #[@test]
  public function string_representation() {
    $this->assertEquals(
      'lang.mirrors.Constant(fixture= "value")',
      (new Constant($this->type, 'fixture', 'value'))->toString()
    );
  }
}