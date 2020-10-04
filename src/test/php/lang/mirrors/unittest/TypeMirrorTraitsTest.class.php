<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\mirrors\unittest\fixture\FixtureTrait;
use unittest\Test;

class TypeMirrorTraitsTest extends \unittest\TestCase {
  use FixtureTrait;
  private $fixture;

  public function setUp() {
    $this->fixture= new TypeMirror(self::class);
  }

  #[Test]
  public function contains_trait_class() {
    $this->assertTrue($this->fixture->traits()->contains(FixtureTrait::class));
  }

  #[Test]
  public function contains_trait_mirror() {
    $this->assertTrue($this->fixture->traits()->contains(new TypeMirror(FixtureTrait::class)));
  }


  #[Test]
  public function all_traits() {
    $this->assertEquals([new TypeMirror(FixtureTrait::class)], iterator_to_array($this->fixture->traits()));
  }
}