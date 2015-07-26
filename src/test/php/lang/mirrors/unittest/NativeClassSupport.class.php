<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;

class NativeClassSupport extends \unittest\TestCase {

  #[@test]
  public function ReflectionClass_has_constructor() {
    $this->assertTrue((new TypeMirror('ReflectionClass'))->constructor()->present());
  }

  #[@test]
  public function ReflectionClass_has_getName_method() {
    $this->assertTrue((new TypeMirror('ReflectionClass'))->methods()->provides('getName'));
  }

  #[@test]
  public function ReflectionClass_has_name_field() {
    $this->assertTrue((new TypeMirror('ReflectionClass'))->fields()->provides('name'));
  }
}