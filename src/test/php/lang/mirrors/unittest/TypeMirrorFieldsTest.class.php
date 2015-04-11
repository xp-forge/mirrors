<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\mirrors\Field;
use lang\mirrors\Member;
use lang\ElementNotFoundException;

class TypeMirrorFieldsTest extends \unittest\TestCase {
  private $fixture;

  public function setUp() {
    $this->fixture= new TypeMirror(MemberFixture::class);
  }

  #[@test]
  public function provides_field() {
    $this->assertTrue($this->fixture->fields()->provides('publicInstanceField'));
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
  public function all_fields() {
    $this->assertEquals(
      [
        new Field($this->fixture, 'publicInstanceField'),
        new Field($this->fixture, 'protectedInstanceField'),
        new Field($this->fixture, 'privateInstanceField'),
        new Field($this->fixture, 'publicClassField'),
        new Field($this->fixture, 'protectedClassField'),
        new Field($this->fixture, 'privateClassField'),
        new Field($this->fixture, 'inheritedField')
      ],
      iterator_to_array($this->fixture->fields())
    );
  }

  #[@test]
  public function declared_fields() {
    $this->assertEquals(
      [
        new Field($this->fixture, 'publicInstanceField'),
        new Field($this->fixture, 'protectedInstanceField'),
        new Field($this->fixture, 'privateInstanceField'),
        new Field($this->fixture, 'publicClassField'),
        new Field($this->fixture, 'protectedClassField'),
        new Field($this->fixture, 'privateClassField')
      ],
      iterator_to_array($this->fixture->fields()->declared())
    );
  }

  #[@test]
  public function instance_fields() {
    $this->assertEquals(
      [
        new Field($this->fixture, 'publicInstanceField'),
        new Field($this->fixture, 'protectedInstanceField'),
        new Field($this->fixture, 'privateInstanceField'),
        new Field($this->fixture, 'inheritedField')
      ],
      iterator_to_array($this->fixture->fields()->of(Member::$INSTANCE))
    );
  }

  #[@test]
  public function static_fields() {
    $this->assertEquals(
      [
        new Field($this->fixture, 'publicClassField'),
        new Field($this->fixture, 'protectedClassField'),
        new Field($this->fixture, 'privateClassField')
      ],
      iterator_to_array($this->fixture->fields()->of(Member::$STATIC))
    );
  }
}