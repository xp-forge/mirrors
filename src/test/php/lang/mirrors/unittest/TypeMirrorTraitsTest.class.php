<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\mirrors\unittest\fixture\FixtureTrait;

class TypeMirrorTraitsTest extends \unittest\TestCase {
  use FixtureTrait;
  private $fixture;

  public function setUp() {
    $this->fixture= new TypeMirror(__CLASS__);
  }

  #[@test]
  public function contains_trait_class() {
    $this->assertTrue($this->fixture->traits()->contains('lang.mirrors.unittest.fixture.FixtureTrait'));
  }

  #[@test]
  public function contains_trait_mirror() {
    $this->assertTrue($this->fixture->traits()->contains(new TypeMirror('lang.mirrors.unittest.fixture.FixtureTrait')));
  }


  #[@test]
  public function all_traits() {
    $this->assertEquals([new TypeMirror('lang.mirrors.unittest.fixture.FixtureTrait')], iterator_to_array($this->fixture->traits()));
  }
}