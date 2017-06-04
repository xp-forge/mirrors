<?php namespace lang\mirrors\unittest\parse;

use lang\mirrors\parse\NewInstance;
use lang\mirrors\parse\Value;
use lang\mirrors\unittest\fixture\FixtureBase;

class NewInstanceTest extends ResolveableTest {

  #[@test]
  public function resolved() {
    $this->assertInstanceOf(
      FixtureBase::class,
      (new NewInstance('lang.mirrors.unittest.fixture.FixtureBase', []))->resolve($this->source)
    );
  }

  #[@test]
  public function passes_args_to_constructor() {
    $fixture= newinstance(FixtureBase::class, [], '{
      public $passed= null;
      public function __construct(... $args) { $this->passed= $args; }
    }');
    $this->assertEquals(
      ['Test', 1],
      (new NewInstance(nameof($fixture), [new Value('Test'), new Value(1)]))->resolve($this->source)->passed
    );
  }
}