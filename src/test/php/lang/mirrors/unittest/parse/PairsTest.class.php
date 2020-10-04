<?php namespace lang\mirrors\unittest\parse;

use lang\mirrors\parse\{Pairs, Value};
use unittest\Test;

class PairsTest extends ResolveableTest {

  #[Test]
  public function empty_pairs() {
    $this->assertEquals([], (new Pairs([]))->resolve($this->source));
  }

  #[Test]
  public function one_pair() {
    $this->assertEquals(
      ['key' => 'value'],
      (new Pairs(['key' => new Value('value')]))->resolve($this->source)
    );
  }

  #[Test]
  public function two_pairs() {
    $this->assertEquals(
      ['a' => 1, 'b' => 2],
      (new Pairs(['a' => new Value(1), 'b' => new Value(2)]))->resolve($this->source)
    );
  }
}