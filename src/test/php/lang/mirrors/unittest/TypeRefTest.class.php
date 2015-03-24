<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\mirrors\parse\TypeRef;
use lang\Primitive;
use lang\Type;

class TypeRefTest extends \unittest\TestCase {
  private $type;

  /** @return void */
  public function setUp() {
    $this->type= new TypeMirror(__CLASS__);
  }

  #[@test, @values([[Primitive::$INT], [Primitive::$BOOL], [Primitive::$DOUBLE], [Primitive::$STRING]])]
  public function primitives($primitive) {
    $this->assertEquals($primitive, (new TypeRef($primitive))->resolve($this->type));
  }

  #[@test, @values([[Type::$VAR], [Type::$VOID]])]
  public function special($type) {
    $this->assertEquals($type, (new TypeRef($type))->resolve($this->type));
  }
}