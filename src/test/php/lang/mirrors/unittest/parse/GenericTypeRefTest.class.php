<?php namespace lang\mirrors\unittest\parse;

use lang\mirrors\parse\{GenericTypeRef, ReferenceTypeRef, TypeRef};
use lang\{Primitive, Type};
use unittest\Test;

class GenericTypeRefTest extends ResolveableTest {

  #[Test]
  public function filter_of_int() {
    $this->assertEquals(
      Type::forName('util.Filter<int>'),
      (new GenericTypeRef(new ReferenceTypeRef('util.Filter'), [new TypeRef(Primitive::$INT)]))->resolve($this->source)
    );
  }
}