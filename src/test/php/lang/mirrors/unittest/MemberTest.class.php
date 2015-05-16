<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\mirrors\parse\Member;

class MemberTest extends \unittest\TestCase {
  const CONSTANT = 'Test';

  private $type;

  /** @return void */
  public function setUp() {
    $this->type= new TypeMirror(__CLASS__);
  }

  #[@test, @values(['self', '\lang\mirrors\unittest\MemberTest', 'MemberTest'])]
  public function resolve_class_constant($class) {
    $this->assertEquals(self::CONSTANT, (new Member($class, 'CONSTANT'))->resolve($this->type));
  }
}