<?php namespace lang\mirrors\unittest\parse;

use lang\mirrors\parse\ReferenceTypeRef;
use lang\XPClass;

class ReferenceTypeRefTest extends ResolveableTest {

  #[@test]
  public function fully_qualified_class_name() {
    $this->assertEquals(
      typeof($this),
      (new ReferenceTypeRef('\\'.__CLASS__))->resolve($this->source)
    );
  }

  #[@test]
  public function looks_up_unqualified_class_names_in_imports() {
    $this->assertEquals(
      XPClass::forName('lang.XPClass'),
      (new ReferenceTypeRef('XPClass'))->resolve($this->source)
    );
  }

  #[@test]
  public function unqualified_class_names_default_to_current_namespace() {
    $this->assertEquals(
      XPClass::forName('lang.mirrors.unittest.parse.TypeRefTest'),
      (new ReferenceTypeRef('TypeRefTest'))->resolve($this->source)
    );
  }

  #[@test]
  public function unqualified_class_name_with_same_name_as_this_class() {
    $this->assertEquals(
      typeof($this),
      (new ReferenceTypeRef('ReferenceTypeRefTest'))->resolve($this->source)
    );
  }

  #[@test]
  public function self_keyword() {
    $this->assertEquals(
      typeof($this),
      (new ReferenceTypeRef('self'))->resolve($this->source)
    );
  }

  #[@test]
  public function parent_keyword() {
    $this->assertEquals(
      typeof($this)->getParentclass(),
      (new ReferenceTypeRef('parent'))->resolve($this->source)
    );
  }
}