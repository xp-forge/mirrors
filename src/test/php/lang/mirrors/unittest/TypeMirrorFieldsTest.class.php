<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\ElementNotFoundException;

class TypeMirrorFieldsTest extends \unittest\TestCase {
  private $fixture;

  public function setUp() {
    $this->fixture= new TypeMirror(self::class);
  }

  #[@test]
  public function provides_field() {
    $this->assertTrue($this->fixture->fields()->provides('fixture'));
  }

  #[@test]
  public function field_named() {
    $this->assertInstanceOf('lang.mirrors.Field', $this->fixture->fields()->named('fixture'));
  }

  #[@test, @expect(ElementNotFoundException::class)]
  public function no_field_named() {
    $this->fixture->fields()->named('does-not-exist');
  }

  #[@test]
  public function all_fields() {
    $result= [];
    foreach ($this->fixture->fields() as $field) {
      $result[]= $field;
    }
    $this->assertInstanceOf('lang.mirrors.Field[]', $result);
  }
}