<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\ElementNotFoundException;
use lang\mirrors\unittest\fixture\FixtureHHVMAnnotations;

#[@action(new OnlyOnHHVM())]
class HHVMAnnotationsTest extends \unittest\TestCase {

  /** @return var[][] */
  private function targets() {
    $mirror= new TypeMirror(FixtureHHVMAnnotations::class);
    return [
      [$mirror],
      [$mirror->constructor()],
      [$mirror->fields()->named('field')],
      [$mirror->methods()->named('method')],
      [$mirror->methods()->named('method')->parameters()->named('param')]
    ];
  }

  #[@test, @values('targets')]
  public function provides_annotation($target) {
    $this->assertTrue($target->annotations()->provides('test'));
  }

  #[@test, @values('targets')]
  public function does_not_provide_non_existant_annotation($target) {
    $this->assertFalse($target->annotations()->provides('does.not.exist'));
  }

  #[@test, @values('targets')]
  public function test_annotation($target) {
    $this->assertNull($target->annotations()->named('test')->value());
  }

  #[@test, @values('targets')]
  public function runtime_annotation($target) {
    $this->assertEquals('~3.6', $target->annotations()->named('runtime')->value());
  }

  #[@test, @values('targets')]
  public function expect_annotation($target) {
    $this->assertEquals(['class' => 'lang.IllegalArgumentException'], $target->annotations()->named('expect')->value());
  }

  #[@test, @expect(ElementNotFoundException::class), @values('targets')]
  public function non_existant_annotation($target) {
    $target->annotations()->named('does.not.exist');
  }
}