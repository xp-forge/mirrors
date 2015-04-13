<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\mirrors\parse\GenericTypeRef;
use lang\mirrors\parse\ReferenceTypeRef;
use lang\mirrors\parse\TypeRef;
use lang\Type;
use lang\Primitive;

class GenericTypeRefTest extends \unittest\TestCase {
  private $type;

  /** @return void */
  public function setUp() {
    $this->type= new TypeMirror(__CLASS__);
  }

  #[@test]
  public function filter_of_int() {
    $this->assertEquals(
      Type::forName('util.Filter<int>'),
      (new GenericTypeRef(new ReferenceTypeRef('util.Filter'), [new TypeRef(Primitive::$INT)]))->resolve($this->type)
    );
  }
}