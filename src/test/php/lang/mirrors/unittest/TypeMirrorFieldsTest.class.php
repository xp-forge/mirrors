<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\mirrors\Field;
use lang\mirrors\Fields;
use lang\mirrors\Member;
use lang\ElementNotFoundException;
use lang\mirrors\unittest\fixture\MemberFixture;

class TypeMirrorFieldsTest extends \unittest\TestCase {
  private $fixture;

  /**
   * Returns the elements of an iterator on Field instances sorted by name
   *
   * @param  php.Traversable $iterator
   * @return lang.mirrors.Field[]
   */
  private function sorted($iterator) {
    $list= iterator_to_array($iterator);
    usort($list, function($a, $b) { return strcmp($a->name(), $b->name()); });
    return $list;
  }

  public function setUp() {
    $this->fixture= new TypeMirror('lang.mirrors.unittest.fixture.MemberFixture');
  }

  #[@test]
  public function provides_field() {
    $this->assertTrue($this->fixture->fields()->provides('publicInstanceField'));
  }

  #[@test]
  public function does_not_provide_non_existant() {
    $this->assertFalse($this->fixture->fields()->provides('does-not-exist'));
  }

  #[@test]
  public function field_named() {
    $this->assertInstanceOf('lang.mirrors.Field', $this->fixture->fields()->named('publicInstanceField'));
  }

  #[@test, @expect(ElementNotFoundException::class)]
  public function no_field_named() {
    $this->fixture->fields()->named('does-not-exist');
  }

  #[@test]
  public function all_fields_by_iterating_field_directly() {
    $this->assertEquals(
      [
        new Field($this->fixture, 'annotatedClassField'),
        new Field($this->fixture, 'annotatedInstanceField'),
        new Field($this->fixture, 'annotatedTraitField'),
        new Field($this->fixture, 'inheritedField'),
        new Field($this->fixture, 'privateClassField'),
        new Field($this->fixture, 'privateInstanceField'),
        new Field($this->fixture, 'protectedClassField'),
        new Field($this->fixture, 'protectedInstanceField'),
        new Field($this->fixture, 'publicClassField'),
        new Field($this->fixture, 'publicInstanceField'),
        new Field($this->fixture, 'traitField')
      ],
      $this->sorted($this->fixture->fields())
    );
  }

  #[@test]
  public function all_fields() {
    $this->assertEquals(
      [
        new Field($this->fixture, 'annotatedClassField'),
        new Field($this->fixture, 'annotatedInstanceField'),
        new Field($this->fixture, 'annotatedTraitField'),
        new Field($this->fixture, 'inheritedField'),
        new Field($this->fixture, 'privateClassField'),
        new Field($this->fixture, 'privateInstanceField'),
        new Field($this->fixture, 'protectedClassField'),
        new Field($this->fixture, 'protectedInstanceField'),
        new Field($this->fixture, 'publicClassField'),
        new Field($this->fixture, 'publicInstanceField'),
        new Field($this->fixture, 'traitField')
      ],
      $this->sorted($this->fixture->fields()->all())
    );
  }

  #[@test]
  public function declared_fields() {
    $this->assertEquals(
      [
        new Field($this->fixture, 'annotatedClassField'),
        new Field($this->fixture, 'annotatedInstanceField'),
        new Field($this->fixture, 'annotatedTraitField'),
        new Field($this->fixture, 'privateClassField'),
        new Field($this->fixture, 'privateInstanceField'),
        new Field($this->fixture, 'protectedClassField'),
        new Field($this->fixture, 'protectedInstanceField'),
        new Field($this->fixture, 'publicClassField'),
        new Field($this->fixture, 'publicInstanceField'),
        new Field($this->fixture, 'traitField')
      ],
      $this->sorted($this->fixture->fields()->declared())
    );
  }

  #[@test]
  public function instance_fields_via_deprecated_of() {
    $this->assertEquals(
      [
        new Field($this->fixture, 'annotatedInstanceField'),
        new Field($this->fixture, 'annotatedTraitField'),
        new Field($this->fixture, 'inheritedField'),
        new Field($this->fixture, 'privateInstanceField'),
        new Field($this->fixture, 'protectedInstanceField'),
        new Field($this->fixture, 'publicInstanceField'),
        new Field($this->fixture, 'traitField')
      ],
      $this->sorted($this->fixture->fields()->of(Member::$INSTANCE))
    );
  }

  #[@test]
  public function static_fields_via_deprecated_of() {
    $this->assertEquals(
      [
        new Field($this->fixture, 'annotatedClassField'),
        new Field($this->fixture, 'privateClassField'),
        new Field($this->fixture, 'protectedClassField'),
        new Field($this->fixture, 'publicClassField')
      ],
      $this->sorted($this->fixture->fields()->of(Member::$STATIC))
    );
  }

  #[@test]
  public function instance_fields() {
    $this->assertEquals(
      [
        new Field($this->fixture, 'annotatedInstanceField'),
        new Field($this->fixture, 'annotatedTraitField'),
        new Field($this->fixture, 'inheritedField'),
        new Field($this->fixture, 'privateInstanceField'),
        new Field($this->fixture, 'protectedInstanceField'),
        new Field($this->fixture, 'publicInstanceField'),
        new Field($this->fixture, 'traitField')
      ],
      $this->sorted($this->fixture->fields()->all(Fields::ofInstance()))
    );
  }

  #[@test]
  public function static_fields() {
    $this->assertEquals(
      [
        new Field($this->fixture, 'annotatedClassField'),
        new Field($this->fixture, 'privateClassField'),
        new Field($this->fixture, 'protectedClassField'),
        new Field($this->fixture, 'publicClassField')
      ],
      $this->sorted($this->fixture->fields()->all(Fields::ofClass()))
    );
  }

  #[@test]
  public function annotated_fields() {
    $this->assertEquals(
      [
        new Field($this->fixture, 'annotatedClassField'),
        new Field($this->fixture, 'annotatedInstanceField'),
      ],
      $this->sorted($this->fixture->fields()->all(Fields::withAnnotation('annotation')))
    );
  }

  #[@test]
  public function fields_by_predicate() {
    $namedTrait= function($member) { return (bool)strstr($member->name(), 'trait'); };
    $this->assertEquals(
      [new Field($this->fixture, 'traitField')],
      $this->sorted($this->fixture->fields()->all(Fields::with($namedTrait)))
    );
  }
}