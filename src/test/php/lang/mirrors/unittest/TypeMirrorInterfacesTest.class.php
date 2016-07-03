<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\Generic;
use lang\mirrors\unittest\fixture\FixtureInterface;

class TypeMirrorInterfacesTest extends \unittest\TestCase implements FixtureInterface {
  private $fixture;

  public function setUp() {
    $this->fixture= new TypeMirror(__CLASS__);
  }

  #[@test]
  public function contains_fixture_class() {
    $this->assertTrue($this->fixture->interfaces()->contains('lang.mirrors.unittest.fixture.FixtureInterface'));
  }

  #[@test]
  public function contains_fixture_dotted() {
    $this->assertTrue($this->fixture->interfaces()->contains('lang.mirrors.unittest.fixture.FixtureInterface'));
  }

  #[@test]
  public function contains_fixture_mirror() {
    $this->assertTrue($this->fixture->interfaces()->contains(new TypeMirror('lang.mirrors.unittest.fixture.FixtureInterface')));
  }

  #[@test]
  public function all_interfaces() {
    $interfaces= iterator_to_array($this->fixture->interfaces());
    usort($interfaces, function($a, $b) { return strcmp($a->name(), $b->name()); });
    $this->assertEquals(
      [new TypeMirror('lang.Generic'), new TypeMirror('lang.mirrors.unittest.fixture.FixtureInterface')],
      $interfaces
    );
  }

  #[@test]
  public function declared_interfaces() {
    $this->assertEquals(
      [new TypeMirror('lang.mirrors.unittest.fixture.FixtureInterface')],
      iterator_to_array($this->fixture->interfaces()->declared())
    );
  }
}