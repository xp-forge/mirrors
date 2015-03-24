<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\ElementNotFoundException;

class MethodAnnotationsTest extends AbstractMethodTest {

  private function noAnnotationFixture() { }

  #[@fixture]
  private function singleAnnotationFixture() { }

  #[@fixture, @other('value')]
  private function multipleAnnotationFixture() { }

  #[@test, @values([
  #  ['noAnnotationFixture', false],
  #  ['singleAnnotationFixture', true],
  #  ['multipleAnnotationFixture', true]
  #])]
  public function present($fixture, $outcome) {
    $this->assertEquals($outcome, $this->fixture($fixture)->annotations()->present());
  }

  #[@test, @values([
  #  ['noAnnotationFixture', []],
  #  ['singleAnnotationFixture', ['fixture' => null]],
  #  ['multipleAnnotationFixture', ['fixture' => null, 'other' => 'value']]
  #])]
  public function all_annotations($fixture, $outcome) {
    $result= [];
    foreach ($this->fixture($fixture)->annotations() as $annotation) {
      $result[$annotation->name()]= $annotation->value();
    }
    $this->assertEquals($outcome, $result);
  }

  #[@test, @values([
  #  ['noAnnotationFixture', false],
  #  ['singleAnnotationFixture', true],
  #  ['multipleAnnotationFixture', true]
  #])]
  public function provides_fixture_annotation($fixture, $outcome) {
    $this->assertEquals($outcome, $this->fixture($fixture)->annotations()->provides('fixture'));
  }

  #[@test, @values(['singleAnnotationFixture', 'multipleAnnotationFixture'])]
  public function fixture_annotation_name($fixture) {
    $this->assertEquals('fixture', $this->fixture($fixture)->annotations()->named('fixture')->name());
  }

  #[@test, @values(['singleAnnotationFixture', 'multipleAnnotationFixture'])]
  public function fixture_annotation_value($fixture) {
    $this->assertNull($this->fixture($fixture)->annotations()->named('fixture')->value());
  }

  #[@test, @expect(ElementNotFoundException::class)]
  public function named_raises_exception_when_given_non_existant_annotation() {
    $this->fixture('noAnnotationFixture')->annotations()->named('fixture');
  }
}