<?php namespace lang\mirrors\unittest;

use lang\mirrors\Annotation;
use lang\mirrors\TypeMirror;
use lang\mirrors\unittest\fixture\Identity;
use lang\mirrors\parse\Value;
use lang\mirrors\parse\Member;
use lang\mirrors\parse\NewInstance;

class AnnotationTest extends \unittest\TestCase {
  private $type;

  public static $FIXTURE = 'static';
  const FIXTURE = 'constant';

  /** @return void */
  public function setUp() {
    $this->type= new TypeMirror(self::class);
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
  public function class_reference() {
    $this->assertEquals(self::class, (new Annotation($this->type, 'name', new Member('self', 'class')))->value());
  }

  #[@test]
  public function class_constant() {
    $this->assertEquals(self::FIXTURE, (new Annotation($this->type, 'name', new Member('self', 'FIXTURE')))->value());
  }

  #[@test]
  public function static_class_member() {
    $this->assertEquals(self::$FIXTURE, (new Annotation($this->type, 'name', new Member('self', '$FIXTURE')))->value());
  }

  #[@test]
  public function newinstance() {
    $this->assertEquals(
      new Identity('Test'),
      (new Annotation($this->type, 'name', new NewInstance('Identity', [new Value('Test')])))->value()
    );
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