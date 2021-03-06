<?php namespace lang\mirrors\unittest;

use lang\mirrors\parse\Value;
use lang\mirrors\{Annotation, TypeMirror};
use unittest\{Test, TestCase};

class AnnotationTest extends TestCase {
  private $type;

  /** @return void */
  public function setUp() {
    $this->type= new TypeMirror(self::class);
  }

  #[Test]
  public function name() {
    $this->assertEquals('name', (new Annotation($this->type, 'name', null))->name());
  }

  #[Test]
  public function null_value() {
    $this->assertNull((new Annotation($this->type, 'name', null))->value());
  }

  #[Test]
  public function value() {
    $this->assertEquals('Test', (new Annotation($this->type, 'name', new Value('Test')))->value());
  }

  #[Test]
  public function declaredIn() {
    $this->assertEquals($this->type, (new Annotation($this->type, 'fixture', null))->declaredIn());
  }

  #[Test]
  public function string_representation_without_value() {
    $this->assertEquals(
      'lang.mirrors.Annotation(@test)',
      (new Annotation($this->type, 'test', null))->toString()
    );
  }

  #[Test]
  public function string_representation() {
    $this->assertEquals(
      'lang.mirrors.Annotation(@test("Test"))',
      (new Annotation($this->type, 'test', new Value('Test')))->toString()
    );
  }
}