<?php namespace lang\mirrors\unittest;

use lang\mirrors\unittest\fixture\Identity;
use lang\mirrors\{Field, Modifiers, TypeMirror};
use lang\{ElementNotFoundException, IllegalArgumentException, Type};
use unittest\{Expect, Test, Values};

class FieldTest extends AbstractFieldTest {

  /** @type lang.mirrors.Field */
  private $fixture;

  /** @type Field */
  private $resolved;

  #[Test]
  public function can_create_from_field_name() {
    new Field($this->type, 'fixture');
  }

  #[Test]
  public function can_create_from_reflection_field() {
    new Field($this->type, new \ReflectionProperty(self::class, 'fixture'));
  }

  #[Test, Expect(ElementNotFoundException::class)]
  public function constructor_raises_exception_if_field_does_not_exist() {
    new Field($this->type, 'not.a.field');
  }

  #[Test]
  public function name() {
    $this->assertEquals('fixture', $this->fixture('fixture')->name());
  }

  #[Test]
  public function modifiers() {
    $this->assertEquals(new Modifiers('private'), $this->fixture('fixture')->modifiers());
  }

  #[Test]
  public function fixture_fields_declaring_type() {
    $this->assertEquals($this->type, $this->fixture('fixture')->declaredIn());
  }

  #[Test]
  public function type_fields_declaring_type() {
    $this->assertEquals($this->type->parent(), $this->fixture('type')->declaredIn());
  }

  #[Test]
  public function string_representation_with_type() {
    $this->assertEquals(
      'lang.mirrors.Field(private lang.mirrors.Field $fixture)',
      $this->fixture('fixture')->toString()
    );
  }

  #[Test]
  public function string_representation() {
    $this->assertEquals(
      'lang.mirrors.Field(protected var $type)',
      $this->fixture('type')->toString()
    );
  }

  #[Test, Values([['/** @var lang.mirrors.unittest.fixture.Identity */', Identity::class], ['/** @var \lang\mirrors\unittest\fixture\Identity */', Identity::class], ['/** @var Identity */', Identity::class], ['/** @var int */', 'int'], ['/** @var string[] */', 'string[]'], ['/** @var [:bool] */', '[:bool]'], ['/** @type lang.mirrors.unittest.fixture.Identity */', Identity::class], ['/** @type \lang\mirrors\unittest\fixture\Identity */', Identity::class], ['/** @type Identity */', Identity::class]])]
  public function type_determined_via_apidoc($comment, $expected) {
    $this->assertEquals(
      Type::forName($expected),
      $this->mirror('{ '.$comment.' public $fixture; }')->fields()->named('fixture')->type()
    );
  }
}