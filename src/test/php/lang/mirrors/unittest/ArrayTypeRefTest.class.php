<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\mirrors\parse\ArrayTypeRef;
use lang\mirrors\parse\TypeRef;
use lang\ArrayType;
use lang\Type;

class ArrayTypeRefTest extends \unittest\TestCase {
  private $type;

  /** @return void */
  public function setUp() {
    $this->type= new TypeMirror(__CLASS__);
  }

  #[@test]
  public function component_type_resolved() {
    $this->assertEquals(
      new ArrayType(Type::$VAR),
      (new ArrayTypeRef(new TypeRef(Type::$VAR)))->resolve($this->type)
    );
  }
}