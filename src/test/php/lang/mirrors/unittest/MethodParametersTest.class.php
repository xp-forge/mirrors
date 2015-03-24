<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\Type;
use lang\Object;
use lang\ElementNotFoundException;

class MethodParametersTest extends AbstractMethodTest {

  private function noParameterFixture() { }

  private function singleParameterFixture($fixture) { }

  private function multipleParameterFixture($fixture, $other) { }

  private function arrayParameterFixture(array $fixture) { }

  private function callableParameterFixture(callable $fixture) { }

  private function objectParameterFixture(Object $fixture) { }

  private function selfParameterFixture(self $fixture) { }

  /** @param string $fixture */
  private function shortFormParameterFixture($fixture) { }

  /**
   * Fixture
   *
   * @param  int $fixture
   * @param  self[] $other
   * @return void
   */
  private function longFormParameterFixture($fixture, $other) { }

  #[@test, @values([
  #  ['noParameterFixture', false],
  #  ['singleParameterFixture', true],
  #  ['multipleParameterFixture', true]
  #])]
  public function present($fixture, $outcome) {
    $this->assertEquals($outcome, $this->fixture($fixture)->parameters()->present());
  }

  #[@test, @values([
  #  ['noParameterFixture', []],
  #  ['singleParameterFixture', ['fixture' => 'var']],
  #  ['multipleParameterFixture', ['fixture' => 'var', 'other' => 'var']],
  #  ['arrayParameterFixture', ['fixture' => 'array']],
  #  ['callableParameterFixture', ['fixture' => 'callable']],
  #  ['objectParameterFixture', ['fixture' => 'lang.Object']],
  #  ['selfParameterFixture', ['fixture' => 'lang.mirrors.unittest.MethodParametersTest']],
  #  ['shortFormParameterFixture', ['fixture' => 'string']],
  #  ['longFormParameterFixture', ['fixture' => 'int', 'other' => 'lang.mirrors.unittest.MethodParametersTest[]']]
  #])]
  public function all_parameters($fixture, $outcome) {
    $result= [];
    foreach ($this->fixture($fixture)->parameters() as $parameter) {
      $result[$parameter->name()]= $parameter->type()->getName();
    }
    $this->assertEquals($outcome, $result);
  }

  #[@test, @values([
  #  ['noParameterFixture', false],
  #  ['singleParameterFixture', true],
  #  ['multipleParameterFixture', true]
  #])]
  public function provides_fixture_parameter($fixture, $outcome) {
    $this->assertEquals($outcome, $this->fixture($fixture)->parameters()->provides('fixture'));
  }

  #[@test, @values(['singleParameterFixture', 'multipleParameterFixture'])]
  public function fixture_parameter_name($fixture) {
    $this->assertEquals('fixture', $this->fixture($fixture)->parameters()->named('fixture')->name());
  }

  #[@test, @expect(ElementNotFoundException::class)]
  public function named_raises_exception_when_given_non_existant_parameter() {
    $this->fixture('noParameterFixture')->parameters()->named('fixture');
  }

  #[@test, @values(['singleParameterFixture', 'multipleParameterFixture'])]
  public function first_parameter_name($fixture) {
    $this->assertEquals('fixture', $this->fixture($fixture)->parameters()->first()->name());
  }

  #[@test, @expect(ElementNotFoundException::class)]
  public function first_raises_exception_when_no_parameters_exist() {
    $this->assertEquals('fixture', $this->fixture('noParameterFixture')->parameters()->first());
  }
}