<?php namespace lang\mirrors\unittest;

use lang\mirrors\Field;
use lang\mirrors\Modifiers;
use lang\mirrors\TypeMirror;
use lang\IllegalArgumentException;

class FieldTest extends AbstractFieldTest {

  /** @type lang.mirrors.Field */
  private $fixture;

  #[@test]
  public function can_create_from_field_name() {
    new Field($this->type, 'fixture');
  }

  #[@test]
  public function can_create_from_reflection_field() {
    new Field($this->type, new \ReflectionProperty(self::class, 'fixture'));
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function constructor_raises_exception_if_field_does_not_exist() {
    new Field($this->type, 'not.a.field');
  }

  #[@test]
  public function name() {
    $this->assertEquals('fixture', $this->fixture('fixture')->name());
  }

  #[@test]
  public function modifiers() {
    $this->assertEquals(new Modifiers('private'), $this->fixture('fixture')->modifiers());
  }

  #[@test]
  public function fixture_fields_declaring_type() {
    $this->assertEquals($this->type, $this->fixture('fixture')->declaredIn());
  }

  #[@test]
  public function type_fields_declaring_type() {
    $this->assertEquals($this->type->parent(), $this->fixture('type')->declaredIn());
  }

  #[@test]
  public function string_representation_with_type() {
    $this->assertEquals(
      'lang.mirrors.Field(private lang.mirrors.Field $fixture)',
      $this->fixture('fixture')->toString()
    );
  }

  #[@test]
  public function string_representation() {
    $this->assertEquals(
      'lang.mirrors.Field(protected var $type)',
      $this->fixture('type')->toString()
    );
  }
}