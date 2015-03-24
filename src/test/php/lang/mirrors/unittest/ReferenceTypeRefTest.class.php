<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\mirrors\parse\ReferenceTypeRef;
use lang\ReferenceType;
use lang\XPClass;

class ReferenceTypeRefTest extends \unittest\TestCase {
  private $type;

  /** @return void */
  public function setUp() {
    $this->type= new TypeMirror(__CLASS__);
  }

  #[@test]
  public function fully_qualified_class_name() {
    $this->assertEquals(
      $this->getClass(),
      (new ReferenceTypeRef(__CLASS__))->resolve($this->type)
    );
  }

  #[@test]
  public function looks_up_unqualified_class_names_in_imports() {
    $this->assertEquals(
      XPClass::forName('lang.mirrors.TypeMirror'),
      (new ReferenceTypeRef('TypeMirror'))->resolve($this->type)
    );
  }

  #[@test]
  public function unqualified_class_names_default_to_current_namespace() {
    $this->assertEquals(
      XPClass::forName('lang.mirrors.unittest.TypeRefTest'),
      (new ReferenceTypeRef('TypeRefTest'))->resolve($this->type)
    );
  }

  #[@test]
  public function unqualified_class_name_with_same_name_as_this_class() {
    $this->assertEquals(
      $this->getClass(),
      (new ReferenceTypeRef('ReferenceTypeRefTest'))->resolve($this->type)
    );
  }

  #[@test]
  public function self_keyword() {
    $this->assertEquals(
      $this->getClass(),
      (new ReferenceTypeRef('self'))->resolve($this->type)
    );
  }

  #[@test]
  public function parent_keyword() {
    $this->assertEquals(
      $this->getClass()->getParentclass(),
      (new ReferenceTypeRef('parent'))->resolve($this->type)
    );
  }
}