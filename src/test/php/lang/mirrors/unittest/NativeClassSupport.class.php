<?php namespace lang\mirrors\unittest;

use lang\Primitive;
use lang\mirrors\TypeMirror;
use unittest\{TestCase, Test};

class NativeClassSupport extends TestCase {

  #[Test]
  public function ReflectionClass_has_constructor() {
    $this->assertTrue((new TypeMirror('ReflectionClass'))->constructor()->present());
  }

  #[Test]
  public function ReflectionClass_has_getName_method() {
    $this->assertTrue((new TypeMirror('ReflectionClass'))->methods()->provides('getName'));
  }

  #[Test]
  public function ReflectionClass_has_name_field() {
    $this->assertTrue((new TypeMirror('ReflectionClass'))->fields()->provides('name'));
  }
}