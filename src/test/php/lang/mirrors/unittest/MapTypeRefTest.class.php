<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\mirrors\parse\MapTypeRef;
use lang\mirrors\parse\TypeRef;
use lang\MapType;
use lang\Type;

class MapTypeRefTest extends \unittest\TestCase {
  private $type;

  /** @return void */
  public function setUp() {
    $this->type= new TypeMirror(__CLASS__);
  }

  #[@test]
  public function component_type_resolved() {
    $this->assertEquals(
      new MapType(Type::$VAR),
      (new MapTypeRef(new TypeRef(Type::$VAR)))->resolve($this->type)
    );
  }
}