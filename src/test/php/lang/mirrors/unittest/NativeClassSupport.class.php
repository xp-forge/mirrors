<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\Primitive;

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

  #[@test, @action(new OnlyOnHHVM())]
  public function ReflectionClass_hasMethod() {
    $hasMethod= (new TypeMirror('ReflectionClass'))->methods()->named('hasMethod');
    $this->assertEquals(
      [Primitive::$STRING, Primitive::$BOOL],
      [$hasMethod->parameters()->first()->type(), $hasMethod->returns()]
    );
  }
}