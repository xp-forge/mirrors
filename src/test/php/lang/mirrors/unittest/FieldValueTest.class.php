<?php namespace lang\mirrors\unittest;

use lang\mirrors\Field;
use lang\mirrors\TypeMirror;
use lang\IllegalArgumentException;
use lang\mirrors\unittest\fixture\Identity;

class FieldValueTest extends AbstractFieldTest {
  private $noValueFixture;
  private $valueFixture= 'Test';
  private static $staticFixture= 'Test';

  #[@test]
  public function no_value() {
    $this->assertNull($this->fixture('noValueFixture')->read($this));
  }

  #[@test]
  public function with_value() {
    $this->assertEquals($this->valueFixture, $this->fixture('valueFixture')->read($this));
  }

  #[@test]
  public function modifiying_member() {
    $modified= 'Tested';
    $this->fixture('valueFixture')->modify($this, $modified);
    $this->assertEquals($modified, $this->fixture('valueFixture')->read($this));
  }

  #[@test]
  public function static_member() {
    $this->assertEquals(self::$staticFixture, $this->fixture('staticFixture')->read(null));
  }

  #[@test]
  public function modifying_static_member() {
    $modified= 'Tested';
    $this->fixture('staticFixture')->modify(null, $modified);
    $this->assertEquals($modified, $this->fixture('staticFixture')->read(null));
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function read_raises_exception_with_incompatible_instance() {
    $this->fixture('valueFixture')->read(new Identity('Test'));
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function read_raises_exception_with_null_instance() {
    $this->fixture('valueFixture')->read(null);
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function modify_raises_exception_with_incompatible_instance() {
    $this->fixture('valueFixture')->modify(new Identity('Test'), 'any-value');
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function modify_raises_exception_with_null_instance() {
    $this->fixture('valueFixture')->modify(null, 'any-value');
  }
}