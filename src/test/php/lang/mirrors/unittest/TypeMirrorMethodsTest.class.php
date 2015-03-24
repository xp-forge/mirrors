<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\ElementNotFoundException;

class TypeMirrorMethodsTest extends \unittest\TestCase {
  private $fixture;

  private function fixture() { }

  public function setUp() {
    $this->fixture= new TypeMirror(self::class);
  }

  #[@test]
  public function provides_method() {
    $this->assertTrue($this->fixture->methods()->provides('fixture'));
  }

  #[@test]
  public function does_not_provide_constructor() {
    $this->assertFalse($this->fixture->methods()->provides('__construct'));
  }

  #[@test]
  public function method_named() {
    $this->assertInstanceOf('lang.mirrors.Method', $this->fixture->methods()->named('fixture'));
  }

  #[@test, @expect(ElementNotFoundException::class)]
  public function no_method_named() {
    $this->fixture->methods()->named('does-not-exist');
  }

  #[@test]
  public function all_methods() {
    $result= [];
    foreach ($this->fixture->methods() as $method) {
      $result[]= $method;
    }
    $this->assertInstanceOf('lang.mirrors.Method[]', $result);
  }
}