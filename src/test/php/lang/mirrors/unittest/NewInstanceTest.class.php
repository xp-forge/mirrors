<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\mirrors\parse\NewInstance;
use lang\mirrors\parse\Value;

class NewInstanceTest extends \unittest\TestCase {
  private $type;

  /** @return void */
  public function setUp() {
    $this->type= new TypeMirror(__CLASS__);
  }

  #[@test]
  public function resolved() {
    $this->assertInstanceOf(
      'lang.Object',
      (new NewInstance('lang.Object', []))->resolve($this->type)
    );
  }

  #[@test]
  public function passes_args_to_constructor() {
    $fixture= newinstance('lang.Object', [], [
      'passed'      => null,
      '__construct' => function(... $args) { $this->passed= $args; }
    ]);
    $this->assertEquals(
      ['Test', 1],
      (new NewInstance($fixture->getClassName(), [new Value('Test'), new Value(1)]))->resolve($this->type)->passed
    );
  }
}