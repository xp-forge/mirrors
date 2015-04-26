<?php namespace lang\mirrors\unittest\parse;

use lang\mirrors\parse\Value;
use lang\mirrors\parse\ArrayExpr;

class ArrayExprTest extends ResolveableTest {

  #[@test]
  public function resolve_resolves_values_in_array_backing() {
    $this->assertEquals([1], (new ArrayExpr([new Value(1)]))->resolve($this->type));
  }

  #[@test]
  public function resolve_resolves_values_in_map_backing() {
    $this->assertEquals(['key' => 1], (new ArrayExpr(['key' => new Value(1)]))->resolve($this->type));
  }
}