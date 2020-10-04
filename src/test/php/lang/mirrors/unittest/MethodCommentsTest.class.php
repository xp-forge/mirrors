<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use unittest\Test;

class MethodCommentsTest extends AbstractMethodTest {

  private function noCommentFixture() { }

  /** @return void */
  private function shortCommentFixture() { }

  /**
   * Test
   *
   * @return void
   */
  private function longCommentFixture() { }

  #[Test]
  public function no_comment() {
    $this->assertNull($this->fixture('noCommentFixture')->comment());
  }

  #[Test]
  public function short_comment() {
    $this->assertNull($this->fixture('shortCommentFixture')->comment());
  }

  #[Test]
  public function long_comment() {
    $this->assertEquals('Test', $this->fixture('longCommentFixture')->comment());
  }
}