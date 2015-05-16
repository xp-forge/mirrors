<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\mirrors\unittest\fixture\FixtureUsed;
use unittest\TestCase;

class TypeMirrorResolveTest extends TestCase {
  private $fixture;

  /**
   * Creates fixture
   *
   * @return void
   */
  public function setUp() {
    $this->fixture= new TypeMirror(self::class, \lang\mirrors\Sources::$CODE);
  }

  #[@test]
  public function self() {
    $this->assertEquals($this->fixture, $this->fixture->resolve('self'));
  }

  #[@test]
  public function parent() {
    $this->assertEquals($this->fixture->parent(), $this->fixture->resolve('parent'));
  }

  #[@test]
  public function by_class_name() {
    $this->assertEquals($this->fixture, $this->fixture->resolve('TypeMirrorResolveTest'));
  }

  #[@test]
  public function by_unqualified() {
    $this->assertEquals(
      new TypeMirror(TypeMirrorTest::class),
      $this->fixture->resolve('TypeMirrorTest')
    );
  }

  #[@test]
  public function by_relative() {
    $this->assertEquals(
      new TypeMirror(FixtureUsed::class),
      $this->fixture->resolve('fixture\FixtureUsed')
    );
  }

  #[@test, @values([
  #  'lang.mirrors.unittest.TypeMirrorTest',
  #  '\lang\mirrors\unittest\TypeMirrorTest'
  #])]
  public function by_fully_qualified($name) {
    $this->assertEquals(
      new TypeMirror(TypeMirrorTest::class),
      $this->fixture->resolve($name)
    );
  }

  #[@test]
  public function imported_class() {
    $this->assertEquals(
      new TypeMirror(TestCase::class),
      $this->fixture->resolve('TestCase')
    );
  }

  #[@test]
  public function unknown_class() {
    $this->assertEquals(
      new TypeMirror('lang\mirrors\unittest\IllegalArgumentException'),
      $this->fixture->resolve('IllegalArgumentException')
    );
  }
}