<?php namespace lang\mirrors\unittest\parse;

use lang\mirrors\parse\MapTypeRef;
use lang\mirrors\parse\TypeRef;
use lang\MapType;
use lang\Type;

class MapTypeRefTest extends ResolveableTest {

  #[@test]
  public function component_type_resolved() {
    $this->assertEquals(
      new MapType(Type::$VAR),
      (new MapTypeRef(new TypeRef(Type::$VAR)))->resolve($this->source)
    );
  }
}