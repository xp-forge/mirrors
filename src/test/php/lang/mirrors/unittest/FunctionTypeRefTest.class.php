<?php namespace lang\mirrors\unittest;

use lang\mirrors\parse\FunctionTypeRef;
use lang\mirrors\parse\TypeRef;
use lang\FunctionType;
use lang\Type;
use lang\Primitive;

class FunctionTypeRefTest extends ResolveableTest {

  #[@test]
  public function empty_parameter_list() {
    $this->assertEquals(
      new FunctionType([], Type::$VOID),
      (new FunctionTypeRef([], new TypeRef(Type::$VOID)))->resolve($this->type)
    );
  }

  #[@test]
  public function one_parameter() {
    $this->assertEquals(
      new FunctionType([Type::$VAR], Primitive::$BOOL),
      (new FunctionTypeRef([new TypeRef(Type::$VAR)], new TypeRef(Primitive::$BOOL)))->resolve($this->type)
    );
  }

  #[@test]
  public function two_parameters() {
    $this->assertEquals(
      new FunctionType([Type::$VAR, Primitive::$STRING], Primitive::$INT),
      (new FunctionTypeRef([new TypeRef(Type::$VAR), new TypeRef(Primitive::$STRING)], new TypeRef(Primitive::$INT)))->resolve($this->type)
    );
  }
}