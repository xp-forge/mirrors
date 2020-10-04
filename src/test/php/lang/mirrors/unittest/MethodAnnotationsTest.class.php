<?php namespace lang\mirrors\unittest;

use lang\ElementNotFoundException;
use lang\mirrors\TypeMirror;
use lang\mirrors\unittest\fixture\Identity;
use unittest\{Expect, Fixture, Other, Test, Values};

class MethodAnnotationsTest extends AbstractMethodTest {

  /** @return iterable */
  private function attributes() {
    yield ['#[Fixture(0)]', 0];
    yield ['#[Fixture(-1)]', -1];
    yield ['#[Fixture(0.5)]', 0.5];
    yield ['#[Fixture(-0.5)]', -0.5];
    yield ['#[Fixture(true)]', true];
    yield ['#[Fixture(false)]', false];
    yield ['#[Fixture(null)]', null];
    yield ['#[Fixture(DIRECTORY_SEPARATOR)]', DIRECTORY_SEPARATOR];
    yield ['#[Fixture("test")]', 'test'];
    yield ['#[Fixture([])]', []];
    yield ['#[Fixture([1, 2, 3])]', [1, 2, 3]];
    yield ['#[Fixture(["key" => "value"])]', ['key' => 'value']];
    yield ['#[Fixture(new Identity("Test"))]', new Identity('Test')];
    yield ['#[Fixture(Identity::$NULL)]', Identity::$NULL];
    yield ['#[Fixture(Identity::NAME)]', Identity::NAME];
    yield ['#[Fixture(Identity::class)]', Identity::class];
  }

  private function noAnnotationFixture() { }

  #[Fixture]
  private function singleAnnotationFixture() { }

  #[Fixture, Other('value')]
  private function multipleAnnotationFixture() { }

  private function paramAnnotationFixture(
    #[Fixture]
    $param
  ) { }

  #[Test, Values([['noAnnotationFixture'], ['singleAnnotationFixture'], ['multipleAnnotationFixture'], ['paramAnnotationFixture']])]
  public function annotations($fixture) {
    $this->assertInstanceOf('lang.mirrors.Annotations', $this->fixture($fixture)->annotations());
  }

  #[Test, Values([['noAnnotationFixture', false], ['singleAnnotationFixture', true], ['multipleAnnotationFixture', true]])]
  public function present($fixture, $outcome) {
    $this->assertEquals($outcome, $this->fixture($fixture)->annotations()->present());
  }

  #[Test, Values([['noAnnotationFixture', []], ['singleAnnotationFixture', ['fixture' => null]], ['multipleAnnotationFixture', ['fixture' => null, 'other' => 'value']]])]
  public function all_annotations($fixture, $outcome) {
    $result= [];
    foreach ($this->fixture($fixture)->annotations() as $annotation) {
      $result[$annotation->name()]= $annotation->value();
    }
    $this->assertEquals($outcome, $result);
  }

  #[Test, Values([['noAnnotationFixture', false], ['singleAnnotationFixture', true], ['multipleAnnotationFixture', true]])]
  public function provides_fixture_annotation($fixture, $outcome) {
    $this->assertEquals($outcome, $this->fixture($fixture)->annotations()->provides('fixture'));
  }

  #[Test, Values(['singleAnnotationFixture', 'multipleAnnotationFixture'])]
  public function fixture_annotation_name($fixture) {
    $this->assertEquals('fixture', $this->fixture($fixture)->annotations()->named('fixture')->name());
  }

  #[Test, Values(['singleAnnotationFixture', 'multipleAnnotationFixture'])]
  public function fixture_annotation_value($fixture) {
    $this->assertNull($this->fixture($fixture)->annotations()->named('fixture')->value());
  }

  #[Test, Expect(ElementNotFoundException::class)]
  public function named_raises_exception_when_given_non_existant_annotation() {
    $this->fixture('noAnnotationFixture')->annotations()->named('fixture');
  }

  #[Test, Values('attributes')]
  public function values($annotation, $expected) {
    $type= $this->mirror("{ ".$annotation."\npublic function fixture() { } }");
    $this->assertEquals(
      $expected,
      $type->methods()->named('fixture')->annotations()->named('fixture')->value()
    );
  }

  #[Test]
  public function closures() {
    $type= $this->mirror("{ #[@fixture(function() { return 'Test'; })]\npublic function fixture() { } }");
    $function= $type->methods()->named('fixture')->annotations()->named('fixture')->value();
    $this->assertEquals('Test', $function());
  }
}