<?php namespace lang\mirrors\unittest\parse;

use lang\ElementNotFoundException;
use lang\mirrors\parse\Constant;
use unittest\{Expect, Test, Values};

class ConstantTest extends ResolveableTest {

  #[Test, Values([['true', true], ['false', false], ['null', null]])]
  public function resolve_looks_up_special($name, $value) {
    $this->assertEquals($value, (new Constant($name))->resolve($this->source));
  }

  #[Test]
  public function resolve_looks_up_M_PI() {
    $this->assertEquals(M_PI, (new Constant('M_PI'))->resolve($this->source));
  }

  #[Test, Expect(ElementNotFoundException::class)]
  public function resolving_non_existant_constant_raises_exception() {
    (new Constant('not.a.constant'))->resolve($this->source);
  }
}