<?php namespace lang\mirrors\unittest\parse;

use lang\mirrors\parse\{FunctionTypeRef, TypeRef};
use lang\{FunctionType, Primitive, Type};
use unittest\Test;

class FunctionTypeRefTest extends ResolveableTest {

  #[Test]
  public function empty_parameter_list() {
    $this->assertEquals(
      new FunctionType([], Type::$VOID),
      (new FunctionTypeRef([], new TypeRef(Type::$VOID)))->resolve($this->source)
    );
  }

  #[Test]
  public function one_parameter() {
    $this->assertEquals(
      new FunctionType([Type::$VAR], Primitive::$BOOL),
      (new FunctionTypeRef([new TypeRef(Type::$VAR)], new TypeRef(Primitive::$BOOL)))->resolve($this->source)
    );
  }

  #[Test]
  public function two_parameters() {
    $this->assertEquals(
      new FunctionType([Type::$VAR, Primitive::$STRING], Primitive::$INT),
      (new FunctionTypeRef([new TypeRef(Type::$VAR), new TypeRef(Primitive::$STRING)], new TypeRef(Primitive::$INT)))->resolve($this->source)
    );
  }
}