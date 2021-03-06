<?php namespace lang\mirrors\unittest;

use lang\ElementNotFoundException;
use lang\mirrors\unittest\fixture\MemberFixture;
use lang\mirrors\{Member, Method, Methods, TypeMirror};
use unittest\{Expect, Test};

class TypeMirrorMethodsTest extends \unittest\TestCase {
  private $fixture;

  /**
   * Returns the elements of an iterator on Method instances sorted by name
   *
   * @param  php.Traversable $iterator
   * @return lang.mirrors.Method[]
   */
  private function sorted($iterator) {
    $list= iterator_to_array($iterator);
    usort($list, function($a, $b) { return strcmp($a->name(), $b->name()); });
    return $list;
  }

  public function setUp() {
    $this->fixture= new TypeMirror(MemberFixture::class);
  }

  #[Test]
  public function provides_method() {
    $this->assertTrue($this->fixture->methods()->provides('publicInstanceMethod'));
  }

  #[Test]
  public function does_not_provide_non_existant() {
    $this->assertFalse($this->fixture->methods()->provides('does-not-exist'));
  }

  #[Test]
  public function does_not_provide_constructor() {
    $this->assertFalse($this->fixture->methods()->provides('__construct'));
  }

  #[Test]
  public function method_named() {
    $this->assertInstanceOf('lang.mirrors.Method', $this->fixture->methods()->named('publicInstanceMethod'));
  }

  #[Test, Expect(ElementNotFoundException::class)]
  public function no_method_named() {
    $this->fixture->methods()->named('does-not-exist');
  }

  #[Test]
  public function all_methods_by_iterating_methods_directly() {
    $this->assertEquals(
      [
        new Method($this->fixture, 'annotatedClassMethod'),
        new Method($this->fixture, 'annotatedInstanceMethod'),
        new Method($this->fixture, 'annotatedTraitMethod'),
        new Method($this->fixture, 'inheritedMethod'),
        new Method($this->fixture, 'privateClassMethod'),
        new Method($this->fixture, 'privateInstanceMethod'),
        new Method($this->fixture, 'protectedClassMethod'),
        new Method($this->fixture, 'protectedInstanceMethod'),
        new Method($this->fixture, 'publicClassMethod'),
        new Method($this->fixture, 'publicInstanceMethod'),
        new Method($this->fixture, 'traitMethod')
      ],
      $this->sorted($this->fixture->methods())
    );
  }

  #[Test]
  public function all_methods() {
    $this->assertEquals(
      [
        new Method($this->fixture, 'annotatedClassMethod'),
        new Method($this->fixture, 'annotatedInstanceMethod'),
        new Method($this->fixture, 'annotatedTraitMethod'),
        new Method($this->fixture, 'inheritedMethod'),
        new Method($this->fixture, 'privateClassMethod'),
        new Method($this->fixture, 'privateInstanceMethod'),
        new Method($this->fixture, 'protectedClassMethod'),
        new Method($this->fixture, 'protectedInstanceMethod'),
        new Method($this->fixture, 'publicClassMethod'),
        new Method($this->fixture, 'publicInstanceMethod'),
        new Method($this->fixture, 'traitMethod')
      ],
      $this->sorted($this->fixture->methods()->all())
    );
  }

  #[Test]
  public function declared_methods() {
    $this->assertEquals(
      [
        new Method($this->fixture, 'annotatedClassMethod'),
        new Method($this->fixture, 'annotatedInstanceMethod'),
        new Method($this->fixture, 'annotatedTraitMethod'),
        new Method($this->fixture, 'privateClassMethod'),
        new Method($this->fixture, 'privateInstanceMethod'),
        new Method($this->fixture, 'protectedClassMethod'),
        new Method($this->fixture, 'protectedInstanceMethod'),
        new Method($this->fixture, 'publicClassMethod'),
        new Method($this->fixture, 'publicInstanceMethod'),
        new Method($this->fixture, 'traitMethod')
      ],
      $this->sorted($this->fixture->methods()->declared())
    );
  }

  #[Test]
  public function instance_methods_via_deprecated_of() {
    $this->assertEquals(
      [
        new Method($this->fixture, 'annotatedInstanceMethod'),
        new Method($this->fixture, 'annotatedTraitMethod'),
        new Method($this->fixture, 'inheritedMethod'),
        new Method($this->fixture, 'privateInstanceMethod'),
        new Method($this->fixture, 'protectedInstanceMethod'),
        new Method($this->fixture, 'publicInstanceMethod'),
        new Method($this->fixture, 'traitMethod')
      ],
      $this->sorted($this->fixture->methods()->of(Member::$INSTANCE))
    );
  }

  #[Test]
  public function static_methods_via_deprecated_of() {
    $this->assertEquals(
      [
        new Method($this->fixture, 'annotatedClassMethod'),
        new Method($this->fixture, 'privateClassMethod'),
        new Method($this->fixture, 'protectedClassMethod'),
        new Method($this->fixture, 'publicClassMethod'),
      ],
      $this->sorted($this->fixture->methods()->of(Member::$STATIC))
    );
  }

  #[Test]
  public function instance_methods() {
    $this->assertEquals(
      [
        new Method($this->fixture, 'annotatedInstanceMethod'),
        new Method($this->fixture, 'annotatedTraitMethod'),
        new Method($this->fixture, 'inheritedMethod'),
        new Method($this->fixture, 'privateInstanceMethod'),
        new Method($this->fixture, 'protectedInstanceMethod'),
        new Method($this->fixture, 'publicInstanceMethod'),
        new Method($this->fixture, 'traitMethod')
      ],
      $this->sorted($this->fixture->methods()->all(Methods::ofInstance()))
    );
  }

  #[Test]
  public function static_methods() {
    $this->assertEquals(
      [
        new Method($this->fixture, 'annotatedClassMethod'),
        new Method($this->fixture, 'privateClassMethod'),
        new Method($this->fixture, 'protectedClassMethod'),
        new Method($this->fixture, 'publicClassMethod'),
      ],
      $this->sorted($this->fixture->methods()->all(Methods::ofClass()))
    );
  }

  #[Test]
  public function annotated_methods() {
    $this->assertEquals(
      [
        new Method($this->fixture, 'annotatedClassMethod'),
        new Method($this->fixture, 'annotatedInstanceMethod'),
      ],
      $this->sorted($this->fixture->methods()->all(Methods::withAnnotation('annotation')))
    );
  }

  #[Test]
  public function methods_by_predicate() {
    $namedTrait= function($member) { return (bool)strstr($member->name(), 'trait'); };
    $this->assertEquals(
      [new Method($this->fixture, 'traitMethod')],
      $this->sorted($this->fixture->methods()->all(Methods::with($namedTrait)))
    );
  }
}