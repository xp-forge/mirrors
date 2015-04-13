<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\mirrors\Method;
use lang\mirrors\Member;
use lang\ElementNotFoundException;

class TypeMirrorMethodsTest extends \unittest\TestCase {
  private $fixture;

  public function setUp() {
    $this->fixture= new TypeMirror(MemberFixture::class);
  }

  #[@test]
  public function provides_method() {
    $this->assertTrue($this->fixture->methods()->provides('publicInstanceMethod'));
  }

  #[@test]
  public function does_not_provide_non_existant() {
    $this->assertFalse($this->fixture->methods()->provides('does-not-exist'));
  }

  #[@test]
  public function does_not_provide_constructor() {
    $this->assertFalse($this->fixture->methods()->provides('__construct'));
  }

  #[@test]
  public function method_named() {
    $this->assertInstanceOf('lang.mirrors.Method', $this->fixture->methods()->named('publicInstanceMethod'));
  }

  #[@test, @expect(ElementNotFoundException::class)]
  public function no_method_named() {
    $this->fixture->methods()->named('does-not-exist');
  }

  #[@test]
  public function all_methods() {
    $this->assertEquals(
      [
        new Method($this->fixture, 'publicInstanceMethod'),
        new Method($this->fixture, 'protectedInstanceMethod'),
        new Method($this->fixture, 'privateInstanceMethod'),
        new Method($this->fixture, 'publicClassMethod'),
        new Method($this->fixture, 'protectedClassMethod'),
        new Method($this->fixture, 'privateClassMethod'),
        new Method($this->fixture, 'inheritedMethod')
      ],
      iterator_to_array($this->fixture->methods())
    );
  }

  #[@test]
  public function declared_methods() {
    $this->assertEquals(
      [
        new Method($this->fixture, 'publicInstanceMethod'),
        new Method($this->fixture, 'protectedInstanceMethod'),
        new Method($this->fixture, 'privateInstanceMethod'),
        new Method($this->fixture, 'publicClassMethod'),
        new Method($this->fixture, 'protectedClassMethod'),
        new Method($this->fixture, 'privateClassMethod')
      ],
      iterator_to_array($this->fixture->methods()->declared())
    );
  }

  #[@test]
  public function instance_methods() {
    $this->assertEquals(
      [
        new Method($this->fixture, 'publicInstanceMethod'),
        new Method($this->fixture, 'protectedInstanceMethod'),
        new Method($this->fixture, 'privateInstanceMethod'),
        new Method($this->fixture, 'inheritedMethod')
      ],
      iterator_to_array($this->fixture->methods()->of(Member::$INSTANCE))
    );
  }

  #[@test]
  public function static_methods() {
    $this->assertEquals(
      [
        new Method($this->fixture, 'publicClassMethod'),
        new Method($this->fixture, 'protectedClassMethod'),
        new Method($this->fixture, 'privateClassMethod')
      ],
      iterator_to_array($this->fixture->methods()->of(Member::$STATIC))
    );
  }
}