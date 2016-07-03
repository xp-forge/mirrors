<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\mirrors\parse\Member;

class MemberTest extends \unittest\TestCase {
  const FIXTURE = 'constant';
  public static $FIXTURE = 'static';

  private $type;

  /** @return void */
  public function setUp() {
    $this->type= new TypeMirror(__CLASS__);
  }

  #[@test, @values(['self', '\lang\mirrors\unittest\MemberTest', 'MemberTest'])]
  public function resolve_class_constant($class) {
    $this->assertEquals(self::FIXTURE, (new Member($class, 'FIXTURE'))->resolve($this->type->reflect));
  }

  #[@test, @values(['self', '\lang\mirrors\unittest\MemberTest', 'MemberTest'])]
  public function resolve_class_member($class) {
    $this->assertEquals(self::$FIXTURE, (new Member($class, '$FIXTURE'))->resolve($this->type->reflect));
  }

  #[@test, @values(['self', '\lang\mirrors\unittest\MemberTest', 'MemberTest'])]
  public function resolve_class_reference($class) {
    $this->assertEquals(__CLASS__, (new Member($class, 'class'))->resolve($this->type->reflect));
  }
}