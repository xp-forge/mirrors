<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;

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

  #[@test]
  public function no_comment() {
    $this->assertNull($this->fixture('noCommentFixture')->comment());
  }

  #[@test]
  public function short_comment() {
    $this->assertNull($this->fixture('shortCommentFixture')->comment());
  }

  #[@test]
  public function long_comment() {
    $this->assertEquals('Test', $this->fixture('longCommentFixture')->comment());
  }
}