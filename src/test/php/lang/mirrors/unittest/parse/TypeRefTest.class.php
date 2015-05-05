<?php namespace lang\mirrors\unittest\parse;

use lang\mirrors\parse\TypeRef;
use lang\Primitive;
use lang\Type;

class TypeRefTest extends ResolveableTest {

  #[@test, @values([[Primitive::$INT], [Primitive::$BOOL], [Primitive::$DOUBLE], [Primitive::$STRING]])]
  public function primitives($primitive) {
    $this->assertEquals($primitive, (new TypeRef($primitive))->resolve($this->source));
  }

  #[@test, @values([[Type::$VAR], [Type::$VOID]])]
  public function special($type) {
    $this->assertEquals($type, (new TypeRef($type))->resolve($this->source));
  }
}