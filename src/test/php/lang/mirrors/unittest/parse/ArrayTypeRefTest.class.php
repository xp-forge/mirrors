<?php namespace lang\mirrors\unittest\parse;

use lang\mirrors\parse\ArrayTypeRef;
use lang\mirrors\parse\TypeRef;
use lang\ArrayType;
use lang\Type;

class ArrayTypeRefTest extends ResolveableTest {

  #[@test]
  public function component_type_resolved() {
    $this->assertEquals(
      new ArrayType(Type::$VAR),
      (new ArrayTypeRef(new TypeRef(Type::$VAR)))->resolve($this->source)
    );
  }
}