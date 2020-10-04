<?php namespace lang\mirrors\unittest\parse;

use lang\mirrors\parse\{ArrayTypeRef, TypeRef};
use lang\{ArrayType, Type};
use unittest\Test;

class ArrayTypeRefTest extends ResolveableTest {

  #[Test]
  public function component_type_resolved() {
    $this->assertEquals(
      new ArrayType(Type::$VAR),
      (new ArrayTypeRef(new TypeRef(Type::$VAR)))->resolve($this->source)
    );
  }
}