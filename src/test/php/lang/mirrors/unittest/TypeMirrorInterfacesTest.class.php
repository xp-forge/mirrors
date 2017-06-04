<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\Closeable;
use lang\mirrors\unittest\fixture\FixtureInterface;
use lang\mirrors\unittest\fixture\FixtureBase;

class TypeMirrorInterfacesTest extends \unittest\TestCase {
  private $fixture;

  public function setUp() {
    $this->fixture= new TypeMirror(FixtureBase::class);
  }

  #[@test]
  public function contains_fixture_class() {
    $this->assertTrue($this->fixture->interfaces()->contains(FixtureInterface::class));
  }

  #[@test]
  public function contains_fixture_dotted() {
    $this->assertTrue($this->fixture->interfaces()->contains('lang.mirrors.unittest.fixture.FixtureInterface'));
  }

  #[@test]
  public function contains_fixture_mirror() {
    $this->assertTrue($this->fixture->interfaces()->contains(new TypeMirror(FixtureInterface::class)));
  }

  #[@test]
  public function all_interfaces() {
    $this->assertEquals(
      [new TypeMirror(FixtureInterface::class)],
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