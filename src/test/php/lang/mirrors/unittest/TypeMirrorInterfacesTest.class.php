<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\Generic;

class TypeMirrorInterfacesTest extends \unittest\TestCase implements FixtureInterface {
  private $fixture;

  public function setUp() {
    $this->fixture= new TypeMirror(self::class);
  }

  #[@test]
  public function contains_fixture_class() {
    $this->assertTrue($this->fixture->interfaces()->contains(FixtureInterface::class));
  }

  #[@test]
  public function contains_fixture_mirror() {
    $this->assertTrue($this->fixture->interfaces()->contains(new TypeMirror(FixtureInterface::class)));
  }

  #[@test]
  public function all_interfaces() {
    $this->assertEquals(
      [new TypeMirror(Generic::class), new TypeMirror(FixtureInterface::class)],
      iterator_to_array($this->fixture->interfaces())
    );
  }

  #[@test]
  public function declared_interfaces() {
    $this->assertEquals(
      [new TypeMirror(FixtureInterface::class)],
      iterator_to_array($this->fixture->interfaces()->declared())
    );
  }
}