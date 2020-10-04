<?php namespace lang\mirrors\unittest;

use lang\ElementNotFoundException;
use lang\mirrors\TypeMirror;
use unittest\{Expect, Test};

#[Fixture]
class TypeMirrorAnnotationsTest extends \unittest\TestCase {
  private $fixture;

  public function setUp() {
    $this->fixture= new TypeMirror(self::class);
  }

  #[Test]
  public function provides_annotation() {
    $this->assertTrue($this->fixture->annotations()->provides('fixture'));
  }

  #[Test]
  public function does_not_provide_non_existant() {
    $this->assertFalse($this->fixture->annotations()->provides('does-not-exist'));
  }

  #[Test]
  public function annotation_named() {
    $this->assertInstanceOf('lang.mirrors.Annotation', $this->fixture->annotations()->named('fixture'));
  }

  #[Test, Expect(ElementNotFoundException::class)]
  public function no_annotation_named() {
    $this->fixture->annotations()->named('does-not-exist');
  }

  #[Test]
  public function all_annotations() {
    $result= [];
    foreach ($this->fixture->annotations() as $annotation) {
      $result[]= $annotation;
    }
    $this->assertInstanceOf('lang.mirrors.Annotation[]', $result);
  }
}