<?php namespace lang\mirrors\unittest;

use lang\mirrors\parse\Constant;
use lang\ElementNotFoundException;

class ParsedConstantTest extends ResolveableTest {

  #[@test, @values([['true', true], ['false', false], ['null', null]])]
  public function resolve_looks_up_special($name, $value) {
    $this->assertEquals($value, (new Constant($name))->resolve($this->type));
  }

  #[@test]
  public function resolve_looks_up_M_PI() {
    $this->assertEquals(M_PI, (new Constant('M_PI'))->resolve($this->type));
  }

  #[@test, @expect(ElementNotFoundException::class)]
  public function resolving_non_existant_constant_raises_exception() {
    (new Constant('not.a.constant'))->resolve($this->type);
  }
}