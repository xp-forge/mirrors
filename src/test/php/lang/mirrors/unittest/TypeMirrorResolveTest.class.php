<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\mirrors\unittest\fixture\FixtureAbstract as Aliased;
use lang\mirrors\unittest\fixture\FixtureUsed;
use unittest\{Test, TestCase, Values};

class TypeMirrorResolveTest extends TestCase {
  private $fixture;

  /**
   * Creates fixture
   *
   * @return void
   */
  public function setUp() {
    $this->fixture= new TypeMirror(self::class);
  }

  #[Test]
  public function self() {
    $this->assertEquals($this->fixture, $this->fixture->resolve('self'));
  }

  #[Test]
  public function parent() {
    $this->assertEquals($this->fixture->parent(), $this->fixture->resolve('parent'));
  }

  #[Test]
  public function by_class_name() {
    $this->assertEquals($this->fixture, $this->fixture->resolve('TypeMirrorResolveTest'));
  }

  #[Test]
  public function by_unqualified() {
    $this->assertEquals(
      new TypeMirror(TypeMirrorTest::class),
      $this->fixture->resolve('TypeMirrorTest')
    );
  }

  #[Test]
  public function by_relative() {
    $this->assertEquals(
      new TypeMirror(FixtureUsed::class),
      $this->fixture->resolve('fixture\FixtureUsed')
    );
  }

  #[Test, Values(['lang.mirrors.unittest.TypeMirrorTest', '\lang\mirrors\unittest\TypeMirrorTest'])]
  public function by_fully_qualified($name) {
    $this->assertEquals(
      new TypeMirror(TypeMirrorTest::class),
      $this->fixture->resolve($name)
    );
  }

  #[Test]
  public function imported_class() {
    $this->assertEquals(
      new TypeMirror(TestCase::class),
      $this->fixture->resolve('TestCase')
    );
  }

  #[Test]
  public function aliased_class() {
    $this->assertEquals(
      new TypeMirror(Aliased::class),
      $this->fixture->resolve('Aliased')
    );
  }

  #[Test]
  public function unknown_class() {
    $this->assertEquals(
      new TypeMirror('lang\mirrors\unittest\IllegalArgumentException'),
      $this->fixture->resolve('IllegalArgumentException')
    );
  }
}