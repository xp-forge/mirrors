<?php namespace lang\mirrors\unittest\parse;

use lang\mirrors\parse\TypeRef;
use lang\{Primitive, Type};
use unittest\{Test, Values};

class TypeRefTest extends ResolveableTest {

  #[Test, Values([[Primitive::$INT], [Primitive::$BOOL], [Primitive::$DOUBLE], [Primitive::$STRING]])]
  public function primitives($primitive) {
    $this->assertEquals($primitive, (new TypeRef($primitive))->resolve($this->source));
  }

  #[Test, Values([[Type::$VAR], [Type::$VOID]])]
  public function special($type) {
    $this->assertEquals($type, (new TypeRef($type))->resolve($this->source));
  }
}