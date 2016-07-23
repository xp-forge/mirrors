<?php namespace lang\mirrors\unittest\parse;

use lang\Object;
use lang\mirrors\parse\NewInstance;
use lang\mirrors\parse\Value;

class NewInstanceTest extends ResolveableTest {

  #[@test]
  public function resolved() {
    $this->assertInstanceOf(
      Object::class,
      (new NewInstance('lang.Object', []))->resolve($this->source)
    );
  }

  #[@test]
  public function passes_args_to_constructor() {
    $fixture= newinstance(Object::class, [], '{
      public $passed= null;
      public function __construct(... $args) { $this->passed= $args; }
    }');
    $this->assertEquals(
      ['Test', 1],
      (new NewInstance(nameof($fixture), [new Value('Test'), new Value(1)]))->resolve($this->source)->passed
    );
  }
}