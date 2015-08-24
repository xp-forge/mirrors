<?php namespace lang\mirrors\unittest\parse;

use lang\mirrors\parse\NewInstance;
use lang\mirrors\parse\Value;

class NewInstanceTest extends ResolveableTest {

  #[@test]
  public function resolved() {
    $this->assertInstanceOf(
      'lang.Object',
      (new NewInstance('lang.Object', []))->resolve($this->source)
    );
  }

  #[@test]
  public function passes_args_to_constructor() {
    $fixture= newinstance('lang.Object', [], '{
      public $passed= null;
      public function __construct() { $this->passed= func_get_args(); }
    }');
    $this->assertEquals(
      ['Test', 1],
      (new NewInstance($fixture->getClassName(), [new Value('Test'), new Value(1)]))->resolve($this->source)->passed
    );
  }
}