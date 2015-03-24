<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\ElementNotFoundException;

class TypeMirrorConstantsTest extends \unittest\TestCase {
  private $fixture;
  const CONSTANT = 1;

  public function setUp() {
    $this->fixture= new TypeMirror(self::class);
  }

  #[@test]
  public function provides_constant() {
    $this->assertTrue($this->fixture->constants()->provides('CONSTANT'));
  }

  #[@test]
  public function constant_named() {
    $this->assertInstanceOf('lang.mirrors.Constant', $this->fixture->constants()->named('CONSTANT'));
  }

  #[@test, @expect(ElementNotFoundException::class)]
  public function no_constant_named() {
    $this->fixture->constants()->named('does-not-exist');
  }

  #[@test]
  public function all_constants() {
    $result= [];
    foreach ($this->fixture->constants() as $constant) {
      $result[]= $constant;
    }
    $this->assertInstanceOf('lang.mirrors.Constant[]', $result);
  }
}