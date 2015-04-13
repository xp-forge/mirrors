<?php namespace lang\mirrors\unittest;

use lang\mirrors\parse\Pairs;
use lang\mirrors\parse\Value;

class PairsTest extends ResolveableTest {

  #[@test]
  public function empty_pairs() {
    $this->assertEquals([], (new Pairs([]))->resolve($this->type));
  }

  #[@test]
  public function one_pair() {
    $this->assertEquals(
      ['key' => 'value'],
      (new Pairs(['key' => new Value('value')]))->resolve($this->type)
    );
  }

  #[@test]
  public function two_pairs() {
    $this->assertEquals(
      ['a' => 1, 'b' => 2],
      (new Pairs(['a' => new Value(1), 'b' => new Value(2)]))->resolve($this->type)
    );
  }
}