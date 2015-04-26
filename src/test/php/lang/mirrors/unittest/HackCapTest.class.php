<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\mirrors\Modifiers;
use lang\mirrors\unittest\fixture\FixtureHackCapClass;

#[@action(new OnlyOnHHVM())]
abstract class HackCapTest extends \unittest\TestCase {
  private $fixture;

  /** @return lang.mirrors.Sources */
  protected abstract function source();

  /**
   * Creates fixture
   *
   * @return  void
   */
  public function setUp() {
    $this->fixture= new TypeMirror(FixtureHackCapClass::class, $this->source());
  }

  #[@test]
  public function has_public_name_member() {
    $this->assertEquals(new Modifiers('public'), $this->fixture->fields()->named('name')->modifiers());
  }

  #[@test]
  public function has_protected_age_member() {
    $this->assertEquals(new Modifiers('protected'), $this->fixture->fields()->named('age')->modifiers());
  }

  #[@test]
  public function has_private_gender_member() {
    $this->assertEquals(new Modifiers('private'), $this->fixture->fields()->named('gender')->modifiers());
  }

  #[@test]
  public function constructor_parameter() {
    $params= iterator_to_array($this->fixture->constructor()->parameters());
    $this->assertEquals(
      ['string name', 'int age', 'bool gender'],
      array_map(function($param) { return $param->type().' '.$param->name(); }, $params)
    );
  }
}