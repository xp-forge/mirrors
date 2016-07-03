<?php namespace lang\mirrors\unittest;

use lang\mirrors\Annotation;
use lang\mirrors\TypeMirror;
use lang\mirrors\parse\Value;

class AnnotationTest extends \unittest\TestCase {
  private $type;

  /** @return void */
  public function setUp() {
    $this->type= new TypeMirror(__CLASS__);
  }

  #[@test]
  public function name() {
    $this->assertEquals('name', (new Annotation($this->type, 'name', null))->name());
  }

  #[@test]
  public function null_value() {
    $this->assertNull((new Annotation($this->type, 'name', null))->value());
  }

  #[@test]
  public function value() {
    $this->assertEquals('Test', (new Annotation($this->type, 'name', new Value('Test')))->value());
  }

  #[@test]
  public function declaredIn() {
    $this->assertEquals($this->type, (new Annotation($this->type, 'fixture', null))->declaredIn());
  }

  #[@test]
  public function string_representation_without_value() {
    $this->assertEquals(
      'lang.mirrors.Annotation(@test)',
      (new Annotation($this->type, 'test', null))->toString()
    );
  }

  #[@test]
  public function string_representation() {
    $this->assertEquals(
      'lang.mirrors.Annotation(@test("Test"))',
      (new Annotation($this->type, 'test', new Value('Test')))->toString()
    );
  }
}