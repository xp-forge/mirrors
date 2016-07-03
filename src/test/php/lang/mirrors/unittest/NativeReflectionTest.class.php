<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;

class NativeReflectionTest extends \unittest\TestCase {
  protected $type;

  /** @return void */
  public function setUp() {
    $this->type= new TypeMirror('Exception');
  }

  #[@test]
  public function name_has_no_dots() {
    $this->assertEquals('Exception', $this->type->name());
  }

  #[@test]
  public function is_in_global_package() {
    $this->assertTrue($this->type->package()->isGlobal());
  }

  #[@test]
  public function declaration_works_correctly() {
    $this->assertEquals('Exception', $this->type->declaration());
  }

  #[@test]
  public function accessing_class_annotations() {
    $this->assertFalse($this->type->annotations()->present());
  }

  #[@test]
  public function accessing_field_annotations() {
    $this->assertFalse($this->type->fields()->named('message')->annotations()->present());
  }

  #[@test]
  public function accessing_methods_annotations() {
    $this->assertFalse($this->type->methods()->named('getMessage')->annotations()->present());
  }

  #[@test]
  public function accessing_constructor_annotations() {
    $this->assertFalse($this->type->constructor()->annotations()->present());
  }

  #[@test]
  public function accessing_parameter_annotations() {
    $this->assertFalse($this->type->constructor()->parameters()->at(0)->annotations()->present());
  }
}