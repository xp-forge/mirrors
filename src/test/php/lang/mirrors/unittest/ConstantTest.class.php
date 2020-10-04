<?php namespace lang\mirrors\unittest;

use lang\mirrors\{Constant, TypeMirror};
use unittest\Test;

class ConstantTest extends \unittest\TestCase {
  const fixture = 'value';
  private $type;

  /** @return void */
  public function setUp() {
    $this->type= new TypeMirror(self::class);
  }

  #[Test]
  public function name() {
    $this->assertEquals('fixture', (new Constant($this->type, 'fixture', 'value'))->name());
  }

  #[Test]
  public function value() {
    $this->assertEquals('value', (new Constant($this->type, 'fixture', 'value'))->value());
  }

  #[Test]
  public function declaredIn() {
    $this->assertEquals($this->type, (new Constant($this->type, 'fixture', 'value'))->declaredIn());
  }

  #[Test]
  public function string_representation() {
    $this->assertEquals(
      'lang.mirrors.Constant(fixture= "value")',
      (new Constant($this->type, 'fixture', 'value'))->toString()
    );
  }
}