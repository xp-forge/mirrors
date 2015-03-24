<?php namespace lang\mirrors\unittest;

use lang\mirrors\Field;
use lang\mirrors\TypeMirror;
use lang\Object;
use lang\IllegalArgumentException;

class FieldValueTest extends AbstractFieldTest {
  private $noValueFixture;
  private $valueFixture= 'Test';
  private static $staticFixture= 'Test';

  #[@test]
  public function no_value() {
    $this->assertNull($this->fixture('noValueFixture')->get($this));
  }

  #[@test]
  public function with_value() {
    $this->assertEquals($this->valueFixture, $this->fixture('valueFixture')->get($this));
  }

  #[@test]
  public function modifiying_member() {
    $modified= 'Tested';
    $this->fixture('valueFixture')->set($this, $modified);
    $this->assertEquals($modified, $this->fixture('valueFixture')->get($this));
  }

  #[@test]
  public function static_member() {
    $this->assertEquals(self::$staticFixture, $this->fixture('staticFixture')->get(null));
  }

  #[@test]
  public function modifying_static_member() {
    $modified= 'Tested';
    $this->fixture('staticFixture')->set(null, $modified);
    $this->assertEquals($modified, $this->fixture('staticFixture')->get(null));
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function get_raises_exception_with_incompatible_instance() {
    $this->fixture('valueFixture')->get(new Object());
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function get_raises_exception_with_null_instance() {
    $this->fixture('valueFixture')->get(null);
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function set_raises_exception_with_incompatible_instance() {
    $this->fixture('valueFixture')->set(new Object(), 'any-value');
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function set_raises_exception_with_null_instance() {
    $this->fixture('valueFixture')->set(null, 'any-value');
  }
}