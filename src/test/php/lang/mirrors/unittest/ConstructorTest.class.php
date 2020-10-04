<?php namespace lang\mirrors\unittest;

use lang\mirrors\unittest\fixture\{FixtureAbstract, FixtureBase, FixtureInterface, FixtureTrait};
use lang\mirrors\{Constructor, Modifiers, TargetInvocationException, TypeMirror};
use lang\{ClassLoader, Error, IllegalArgumentException};
use unittest\{Expect, Test, TestCase};

class ConstructorTest extends TestCase {

  #[Test]
  public function can_create() {
    new Constructor(new TypeMirror('unittest.TestCase'));
  }

  #[Test]
  public function this_class_constructors_declaring_type() {
    $type= new TypeMirror(self::class);
    $this->assertEquals($type->parent(), (new Constructor($type))->declaredIn());
  }

  #[Test]
  public function this_classes_constructor_has_one_parameter() {
    $type= new TypeMirror(self::class);
    $this->assertEquals(1, (new Constructor($type))->parameters()->length());
  }

  #[Test]
  public function base_classes_constructor_has_no_params() {
    $type= new TypeMirror(FixtureBase::class);
    $this->assertEquals(0, (new Constructor($type))->parameters()->length());
  }

  #[Test]
  public function base_classes_constructor_is_public() {
    $type= new TypeMirror(FixtureBase::class);
    $this->assertEquals(new Modifiers('public'), (new Constructor($type))->modifiers());
  }

  #[Test]
  public function creating_new_object_instances() {
    $this->assertInstanceOf(
      FixtureBase::class,
      (new Constructor(new TypeMirror(FixtureBase::class)))->newInstance()
    );
  }

  #[Test]
  public function creating_instances_invokes_constructor() {
    $fixture= newinstance(FixtureBase::class, [], '{
      public $passed= null;
      public function __construct(... $args) { $this->passed= $args; }
    }');
    $this->assertEquals(
      [1, 2, 3],
      (new Constructor(new TypeMirror(typeof($fixture))))->newInstance(1, 2, 3)->passed
    );
  }

  #[Test, Expect(TargetInvocationException::class)]
  public function creating_instances_wraps_exceptions() {
    $fixture= ClassLoader::defineClass($this->name, FixtureBase::class, [], [
      '__construct' => function($arg) { throw new IllegalArgumentException('Test'); }
    ]);
    (new Constructor(new TypeMirror($fixture)))->newInstance(null);
  }

  #[Test, Expect(TargetInvocationException::class)]
  public function creating_instances_wraps_argument_mismatch_exceptions() {
    $fixture= ClassLoader::defineClass($this->name, FixtureBase::class, [], [
      '__construct' => function(TypeMirror $arg) { }
    ]);
    (new Constructor(new TypeMirror($fixture)))->newInstance(null);
  }

  #[Test, Expect(TargetInvocationException::class)]
  public function creating_instances_wraps_errors() {
    $fixture= ClassLoader::defineClass($this->name, FixtureBase::class, [], [
      '__construct' => function($arg) { $arg->invoke(); }
    ]);
    (new Constructor(new TypeMirror($fixture)))->newInstance(null);
  }

  #[Test]
  public function sets_cause_for_exceptions_thrown() {
    try {
      $fixture= ClassLoader::defineClass($this->name, FixtureBase::class, [], [
        '__construct' => function($arg) { throw new IllegalArgumentException('Test'); }
      ]);
      (new Constructor(new TypeMirror($fixture)))->newInstance(null);
      $this->fail('No exception raised', null, TargetInvocationException::class);
    } catch (TargetInvocationException $expected) {
      $this->assertInstanceOf(IllegalArgumentException::class, $expected->getCause());
    }
  }

  #[Test]
  public function sets_cause_for_errors_raised() {
    try {
      $fixture= ClassLoader::defineClass($this->name, FixtureBase::class, [], [
        '__construct' => function($arg) { $arg->invoke(); }
      ]);
      (new Constructor(new TypeMirror($fixture)))->newInstance(null);
      $this->fail('No exception raised', null, TargetInvocationException::class);
    } catch (TargetInvocationException $expected) {
      $this->assertInstanceOf(Error::class, $expected->getCause());
    }
  }

  #[Test, Expect(IllegalArgumentException::class)]
  public function cannot_create_instances_from_interfaces() {
    (new Constructor(new TypeMirror(FixtureInterface::class)))->newInstance();
  }

  #[Test, Expect(IllegalArgumentException::class)]
  public function cannot_create_instances_from_traits() {
    (new Constructor(new TypeMirror(FixtureTrait::class)))->newInstance();
  }

  #[Test, Expect(IllegalArgumentException::class)]
  public function cannot_create_instances_from_abstract_classes() {
    (new Constructor(new TypeMirror(FixtureAbstract::class)))->newInstance();
  }

  #[Test]
  public function string_representation() {
    $this->assertEquals(
      'lang.mirrors.Constructor(public __construct(string $name))',
      (new Constructor(new TypeMirror('unittest.TestCase')))->toString()
    );
  }
}