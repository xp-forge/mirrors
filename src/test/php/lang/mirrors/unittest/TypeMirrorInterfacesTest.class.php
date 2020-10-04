<?php namespace lang\mirrors\unittest;

use lang\Closeable;
use lang\mirrors\TypeMirror;
use lang\mirrors\unittest\fixture\{FixtureBase, FixtureInterface};
use unittest\Test;

class TypeMirrorInterfacesTest extends \unittest\TestCase {
  private $fixture;

  public function setUp() {
    $this->fixture= new TypeMirror(FixtureBase::class);
  }

  #[Test]
  public function contains_fixture_class() {
    $this->assertTrue($this->fixture->interfaces()->contains(FixtureInterface::class));
  }

  #[Test]
  public function contains_fixture_dotted() {
    $this->assertTrue($this->fixture->interfaces()->contains('lang.mirrors.unittest.fixture.FixtureInterface'));
  }

  #[Test]
  public function contains_fixture_mirror() {
    $this->assertTrue($this->fixture->interfaces()->contains(new TypeMirror(FixtureInterface::class)));
  }

  #[Test]
  public function all_interfaces() {
    $this->assertEquals(
      [new TypeMirror(FixtureInterface::class)],
      iterator_to_array($this->fixture->interfaces())
    );
  }

  #[Test]
  public function declared_interfaces() {
    $this->assertEquals(
      [new TypeMirror(FixtureInterface::class)],
      iterator_to_array($this->fixture->interfaces()->declared())
    );
  }
}