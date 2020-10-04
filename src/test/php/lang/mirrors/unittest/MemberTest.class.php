<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\mirrors\parse\Member;
use unittest\{Test, Values};

class MemberTest extends \unittest\TestCase {
  const FIXTURE = 'constant';
  public static $FIXTURE = 'static';

  private $type;

  /** @return void */
  public function setUp() {
    $this->type= new TypeMirror(self::class);
  }

  #[Test, Values(['self', '\lang\mirrors\unittest\MemberTest', 'MemberTest'])]
  public function resolve_class_constant($class) {
    $this->assertEquals(self::FIXTURE, (new Member($class, 'FIXTURE'))->resolve($this->type->reflect));
  }

  #[Test, Values(['self', '\lang\mirrors\unittest\MemberTest', 'MemberTest'])]
  public function resolve_class_member($class) {
    $this->assertEquals(self::$FIXTURE, (new Member($class, '$FIXTURE'))->resolve($this->type->reflect));
  }

  #[Test, Values(['self', '\lang\mirrors\unittest\MemberTest', 'MemberTest'])]
  public function resolve_class_reference($class) {
    $this->assertEquals(self::class, (new Member($class, 'class'))->resolve($this->type->reflect));
  }
}