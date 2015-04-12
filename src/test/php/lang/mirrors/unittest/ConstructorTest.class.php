<?php namespace lang\mirrors\unittest;

use lang\mirrors\Constructor;
use lang\mirrors\TypeMirror;
use lang\IllegalArgumentException;
use lang\mirrors\TargetInvocationException;

class ConstructorTest extends \unittest\TestCase {

  #[@test]
  public function can_create() {
    new Constructor(new TypeMirror('unittest.TestCase'));
  }

  #[@test]
  public function this_class_constructors_declaring_type() {
    $type= new TypeMirror(self::class);
    $this->assertEquals($type->parent(), (new Constructor($type))->declaredIn());
  }

  #[@test]
  public function this_classes_constructor_has_one_parameter() {
    $type= new TypeMirror(self::class);
    $this->assertEquals(1, (new Constructor($type))->parameters()->length());
  }

  #[@test]
  public function object_classes_constructor_has_no_params() {
    $type= new TypeMirror('lang.Object');
    $this->assertEquals(0, (new Constructor($type))->parameters()->length());
  }

  #[@test]
  public function creating_new_object_instances() {
    $this->assertInstanceOf(
      'lang.Object',
      (new Constructor(new TypeMirror('lang.Object')))->newInstance()
    );
  }

  #[@test]
  public function creating_instances_invokes_constructor() {
    $fixture= newinstance('lang.Object', [], [
      'passed'      => null,
      '__construct' => function(... $args) { $this->passed= $args; }
    ]);
    $this->assertEquals(
      [1, 2, 3],
      (new Constructor(new TypeMirror(typeof($fixture))))->newInstance(1, 2, 3)->passed
    );
  }

  #[@test, @expect(TargetInvocationException::class)]
  public function creating_instances_wraps_exceptions() {
    $fixture= newinstance('lang.Object', ['Test'], [
      '__construct' => function($arg) { if (null === $arg) throw new IllegalArgumentException('Test'); }
    ]);
    (new Constructor(new TypeMirror(typeof($fixture))))->newInstance(null);
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function cannot_create_instances_from_interfaces() {
    (new Constructor(new TypeMirror(FixtureInterface::class)))->newInstance();
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function cannot_create_instances_from_traits() {
    (new Constructor(new TypeMirror(FixtureTrait::class)))->newInstance();
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function cannot_create_instances_from_abstract_classes() {
    (new Constructor(new TypeMirror(FixtureAbstract::class)))->newInstance();
  }
}