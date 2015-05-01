<?php namespace lang\mirrors\unittest\parse;

use lang\mirrors\parse\GenericTypeRef;
use lang\mirrors\parse\ReferenceTypeRef;
use lang\mirrors\parse\TypeRef;
use lang\Type;
use lang\Primitive;

class GenericTypeRefTest extends ResolveableTest {

  #[@test]
  public function filter_of_int() {
    $this->assertEquals(
      Type::forName('util.Filter<int>'),
      (new GenericTypeRef(new ReferenceTypeRef('util.Filter'), [new TypeRef(Primitive::$INT)]))->resolve($this->source)
    );
  }
}