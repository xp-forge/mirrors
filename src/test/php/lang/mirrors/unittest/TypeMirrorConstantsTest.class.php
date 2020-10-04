<?php namespace lang\mirrors\unittest;

use lang\ElementNotFoundException;
use lang\mirrors\TypeMirror;
use unittest\{Expect, Test};

class TypeMirrorConstantsTest extends \unittest\TestCase {
  private $fixture;
  const CONSTANT = 1;

  public function setUp() {
    $this->fixture= new TypeMirror(self::class);
  }

  #[Test]
  public function provides_constant() {
    $this->assertTrue($this->fixture->constants()->provides('CONSTANT'));
  }

  #[Test]
  public function does_not_provide_non_existant() {
    $this->assertFalse($this->fixture->constants()->provides('does-not-exist'));
  }

  #[Test]
  public function constant_named() {
    $this->assertInstanceOf('lang.mirrors.Constant', $this->fixture->constants()->named('CONSTANT'));
  }

  #[Test, Expect(ElementNotFoundException::class)]
  public function no_constant_named() {
    $this->fixture->constants()->named('does-not-exist');
  }

  #[Test]
  public function all_constants() {
    $result= [];
    foreach ($this->fixture->constants() as $constant) {
      $result[]= $constant;
    }
    $this->assertInstanceOf('lang.mirrors.Constant[]', $result);
  }
}