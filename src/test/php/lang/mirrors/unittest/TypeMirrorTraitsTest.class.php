<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;

class TypeMirrorTraitsTest extends \unittest\TestCase {
  use FixtureTrait;
  private $fixture;

  public function setUp() {
    $this->fixture= new TypeMirror(self::class);
  }

  #[@test]
  public function contains_trait_class() {
    $this->assertTrue($this->fixture->traits()->contains(FixtureTrait::class));
  }

  #[@test]
  public function contains_trait_mirror() {
    $this->assertTrue($this->fixture->traits()->contains(new TypeMirror(FixtureTrait::class)));
  }


  #[@test]
  public function all_traits() {
    $this->assertEquals([new TypeMirror(FixtureTrait::class)], iterator_to_array($this->fixture->traits()));
  }
}