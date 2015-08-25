<?php namespace lang\mirrors\unittest;

use lang\mirrors\Constructor;
use lang\mirrors\TypeMirror;
use lang\IllegalArgumentException;
use lang\mirrors\TargetInvocationException;
use lang\mirrors\unittest\fixture\FixtureInterface;
use lang\mirrors\unittest\fixture\FixtureTrait;
use lang\mirrors\unittest\fixture\FixtureAbstract;
use lang\ClassLoader;
use unittest\actions\RuntimeVersion;

class ConstructorTest extends \unittest\TestCase {

  #[@test]
  public function can_create() {
    new Constructor(new TypeMirror('unittest.TestCase'));
  }

  #[@test]
  public function this_class_constructors_declaring_type() {
    $type= new TypeMirror(__CLASS__);
    $this->assertEquals($type->parent(), (new Constructor($type))->declaredIn());
  }

  #[@test]
  public function this_classes_constructor_has_one_parameter() {
    $type= new TypeMirror(__CLASS__);
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
    $fixture= newinstance('lang.Object', [], '{
      public $passed= null;
      public function __construct() { $this->passed= func_get_args(); }
    }');
    $this->assertEquals(
      [1, 2, 3],
      (new Constructor(new TypeMirror(typeof($fixture))))->newInstance(1, 2, 3)->passed
    );
  }

  #[@test, @expect(TargetInvocationException::class)]
  public function creating_instances_wraps_exceptions() {
    $fixture= ClassLoader::defineClass($this->name, 'lang.Object', [], [
      '__construct' => function($arg) { throw new IllegalArgumentException('Test'); }
    ]);
    (new Constructor(new TypeMirror($fixture)))->newInstance(null);
  }

  #[@test, @expect(TargetInvocationException::class)]
  public function creating_instances_wraps_argument_mismatch_exceptions() {
    $fixture= ClassLoader::defineClass($this->name, 'lang.Object', [], [
      '__construct' => function(TypeMirror $arg) { }
    ]);
    (new Constructor(new TypeMirror($fixture)))->newInstance(null);
  }

  #[@test, @expect(TargetInvocationException::class), @action(new RuntimeVersion('>=7.0.0-dev'))]
  public function creating_instances_wraps_errors() {
    $fixture= ClassLoader::defineClass($this->name, 'lang.Object', [], [
      '__construct' => function($arg) { $arg->invoke(); }
    ]);
    (new Constructor(new TypeMirror($fixture)))->newInstance(null);
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function cannot_create_instances_from_interfaces() {
    (new Constructor(new TypeMirror('lang.mirrors.unittest.fixture.FixtureInterface')))->newInstance();
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function cannot_create_instances_from_traits() {
    (new Constructor(new TypeMirror('lang.mirrors.unittest.fixture.FixtureTrait')))->newInstance();
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function cannot_create_instances_from_abstract_classes() {
    (new Constructor(new TypeMirror('lang.mirrors.unittest.fixture.FixtureAbstract')))->newInstance();
  }

  #[@test]
  public function string_representation() {
    $this->assertEquals(
      'lang.mirrors.Constructor(public __construct(string $name))',
      (new Constructor(new TypeMirror('unittest.TestCase')))->toString()
    );
  }
}