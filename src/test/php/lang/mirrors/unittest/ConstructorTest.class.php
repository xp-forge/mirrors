<?php namespace lang\mirrors\unittest;

use lang\mirrors\Constructor;
use lang\mirrors\TypeMirror;
use lang\IllegalArgumentException;

class ConstructorTest extends \unittest\TestCase {

  #[@test]
  public function can_create_from_type_only() {
    new Constructor(new TypeMirror('unittest.TestCase'));
  }

  #[@test]
  public function can_create_from_reflection_method() {
    new Constructor(new TypeMirror(__CLASS__), (new \ReflectionClass(self::class))->getConstructor());
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function cannot_create_for_classes_wihtout_constructor() {
    new Constructor(new TypeMirror('lang.Object'));
  }

  #[@test]
  public function this_class_constructors_declaring_type() {
    $type= new TypeMirror(__CLASS__);
    $this->assertEquals($type->parent(), (new Constructor($type))->declaredIn());
  }
}