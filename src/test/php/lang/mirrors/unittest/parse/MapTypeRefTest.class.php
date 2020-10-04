<?php namespace lang\mirrors\unittest\parse;

use lang\mirrors\parse\{MapTypeRef, TypeRef};
use lang\{MapType, Type};
use unittest\Test;

class MapTypeRefTest extends ResolveableTest {

  #[Test]
  public function component_type_resolved() {
    $this->assertEquals(
      new MapType(Type::$VAR),
      (new MapTypeRef(new TypeRef(Type::$VAR)))->resolve($this->source)
    );
  }
}