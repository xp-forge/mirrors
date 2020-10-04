<?php namespace lang\mirrors\unittest;

use lang\mirrors\{Annotation, Parameter, TypeMirror};
use lang\{ElementNotFoundException, Type, Value};
use unittest\{Expect, Test, Values};

class MethodParametersTest extends AbstractMethodTest {

  private function noParameterFixture() { }

  private function singleParameterFixture($fixture) { }

  private function multipleParameterFixture($fixture, $other) { }

  private function arrayParameterFixture(array $fixture) { }

  private function callableParameterFixture(callable $fixture) { }

  private function valueParameterFixture(Value $fixture) { }

  private function selfParameterFixture(self $fixture) { }

  /** @param string $fixture */
  private function shortFormParameterFixture($fixture) { }

  /** @param Parameter $fixture */
  private function resolvedParameterFixture($fixture) { }

  /**
   * Fixture
   *
   * @param  int $fixture
   * @param  self[] $other
   * @return void
   */
  private function longFormParameterFixture($fixture, $other) { }

  private function annotatedParameterFixture(
    #[Annotation]
    $fixture
  ) { }

  #[Test, Values([['noParameterFixture', false], ['singleParameterFixture', true], ['multipleParameterFixture', true]])]
  public function present($fixture, $outcome) {
    $this->assertEquals($outcome, $this->fixture($fixture)->parameters()->present());
  }

  #[Test, Values([['noParameterFixture', []], ['singleParameterFixture', ['fixture' => 'var']], ['multipleParameterFixture', ['fixture' => 'var', 'other' => 'var']], ['arrayParameterFixture', ['fixture' => 'array']], ['callableParameterFixture', ['fixture' => 'callable']], ['valueParameterFixture', ['fixture' => 'lang.Value']], ['selfParameterFixture', ['fixture' => 'lang.mirrors.unittest.MethodParametersTest']], ['shortFormParameterFixture', ['fixture' => 'string']], ['longFormParameterFixture', ['fixture' => 'int', 'other' => 'lang.mirrors.unittest.MethodParametersTest[]']]])]
  public function all_parameters($fixture, $outcome) {
    $result= [];
    foreach ($this->fixture($fixture)->parameters() as $parameter) {
      $result[$parameter->name()]= $parameter->type()->getName();
    }
    $this->assertEquals($outcome, $result);
  }

  #[Test, Values([['noParameterFixture', false], ['singleParameterFixture', true], ['multipleParameterFixture', true]])]
  public function provides_fixture_parameter($fixture, $outcome) {
    $this->assertEquals($outcome, $this->fixture($fixture)->parameters()->provides('fixture'));
  }

  #[Test, Values(['singleParameterFixture', 'multipleParameterFixture'])]
  public function fixture_parameter_name($fixture) {
    $this->assertEquals('fixture', $this->fixture($fixture)->parameters()->named('fixture')->name());
  }

  #[Test, Expect(ElementNotFoundException::class)]
  public function named_raises_exception_when_given_non_existant_parameter() {
    $this->fixture('noParameterFixture')->parameters()->named('fixture');
  }

  #[Test, Values(['singleParameterFixture', 'multipleParameterFixture'])]
  public function first_parameter_name($fixture) {
    $this->assertEquals('fixture', $this->fixture($fixture)->parameters()->first()->name());
  }

  #[Test, Expect(ElementNotFoundException::class)]
  public function first_raises_exception_when_no_parameters_exist() {
    $this->assertEquals('fixture', $this->fixture('noParameterFixture')->parameters()->first());
  }

  #[Test, Values(['singleParameterFixture', 'multipleParameterFixture'])]
  public function at_0($fixture) {
    $this->assertEquals('fixture', $this->fixture($fixture)->parameters()->at(0)->name());
  }

  #[Test]
  public function at_1() {
    $this->assertEquals('other', $this->fixture('multipleParameterFixture')->parameters()->at(1)->name());
  }

  #[Test, Values([['noParameterFixture', 0], ['singleParameterFixture', 1], ['multipleParameterFixture', 2]])]
 public function length($fixture, $expect) {
    $this->assertEquals($expect, $this->fixture($fixture)->parameters()->length());
  }

  #[Test]
  public function resolved() {
    $this->assertEquals(
      Type::forName('lang.mirrors.Parameter'),
      $this->fixture('resolvedParameterFixture')->parameters()->named('fixture')->type()
    );
  }

  #[Test]
  public function annotations() {
    $this->assertEquals(
      [new Annotation($this->type, 'annotation', null)],
      iterator_to_array($this->fixture('annotatedParameterFixture')->parameters()->first()->annotations())
    );
  }
}