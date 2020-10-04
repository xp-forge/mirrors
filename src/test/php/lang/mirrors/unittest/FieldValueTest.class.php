<?php namespace lang\mirrors\unittest;

use lang\IllegalArgumentException;
use lang\mirrors\unittest\fixture\Identity;
use lang\mirrors\{Field, TypeMirror};
use unittest\{Expect, Test};

class FieldValueTest extends AbstractFieldTest {
  private $noValueFixture;
  private $valueFixture= 'Test';
  private static $staticFixture= 'Test';

  #[Test]
  public function no_value() {
    $this->assertNull($this->fixture('noValueFixture')->read($this));
  }

  #[Test]
  public function with_value() {
    $this->assertEquals($this->valueFixture, $this->fixture('valueFixture')->read($this));
  }

  #[Test]
  public function modifiying_member() {
    $modified= 'Tested';
    $this->fixture('valueFixture')->modify($this, $modified);
    $this->assertEquals($modified, $this->fixture('valueFixture')->read($this));
  }

  #[Test]
  public function static_member() {
    $this->assertEquals(self::$staticFixture, $this->fixture('staticFixture')->read(null));
  }

  #[Test]
  public function modifying_static_member() {
    $modified= 'Tested';
    $this->fixture('staticFixture')->modify(null, $modified);
    $this->assertEquals($modified, $this->fixture('staticFixture')->read(null));
  }

  #[Test, Expect(IllegalArgumentException::class)]
  public function read_raises_exception_with_incompatible_instance() {
    $this->fixture('valueFixture')->read(new Identity('Test'));
  }

  #[Test, Expect(IllegalArgumentException::class)]
  public function read_raises_exception_with_null_instance() {
    $this->fixture('valueFixture')->read(null);
  }

  #[Test, Expect(IllegalArgumentException::class)]
  public function modify_raises_exception_with_incompatible_instance() {
    $this->fixture('valueFixture')->modify(new Identity('Test'), 'any-value');
  }

  #[Test, Expect(IllegalArgumentException::class)]
  public function modify_raises_exception_with_null_instance() {
    $this->fixture('valueFixture')->modify(null, 'any-value');
  }
}