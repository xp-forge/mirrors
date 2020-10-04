<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use unittest\Test;

class NativeReflectionTest extends \unittest\TestCase {
  protected $type;

  /** @return void */
  public function setUp() {
    $this->type= new TypeMirror(\Exception::class);
  }

  #[Test]
  public function name_has_no_dots() {
    $this->assertEquals('Exception', $this->type->name());
  }

  #[Test]
  public function is_in_global_package() {
    $this->assertTrue($this->type->package()->isGlobal());
  }

  #[Test]
  public function declaration_works_correctly() {
    $this->assertEquals('Exception', $this->type->declaration());
  }

  #[Test]
  public function accessing_class_annotations() {
    $this->assertFalse($this->type->annotations()->present());
  }

  #[Test]
  public function accessing_field_annotations() {
    $this->assertFalse($this->type->fields()->named('message')->annotations()->present());
  }

  #[Test]
  public function accessing_methods_annotations() {
    $this->assertFalse($this->type->methods()->named('getMessage')->annotations()->present());
  }

  #[Test]
  public function accessing_constructor_annotations() {
    $this->assertFalse($this->type->constructor()->annotations()->present());
  }

  #[Test]
  public function accessing_parameter_annotations() {
    $this->assertFalse($this->type->constructor()->parameters()->at(0)->annotations()->present());
  }
}