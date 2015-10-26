<?php namespace lang\mirrors\parse;

use text\parse\Values;
use text\parse\Unexpected;

class Block extends \text\parse\Rule {
  private $started, $open, $close;

  /**
   * Creates a new block rule with opening and closing tokens, defaulting
   * to curly braces.
   *
   * @param  bool $begun Whether block has already begun (inside Match e.g.)
   * @param  string $open
   * @param  string $close
   */
  public function __construct($started= false, $open= '{', $close= '}') {
    $this->started= $started;
    $this->open= $open;
    $this->close= $close;
  }

  /**
   * Consume
   *
   * @param  [:text.parse.Rule] $rules
   * @param  text.parse.Tokens $tokens
   * @param  var[] $values
   * @return text.parse.Consumed
   */
  public function consume($rules, $tokens, $values) {
    if (!$this->started) {
      $first= $tokens->token();
      if ($first === $this->open) {
        $tokens->forward();
      } else {
        return new Unexpected($tokens->nameOf($first).', expecting '.$this->open, $tokens->line());
      }
    }

    if (null === ($block= $tokens->block($this->open, $this->close))) {
      return new Unexpected('End of file', $tokens->line());
    } else {
      return new Values(trim($block));
    }
  }
}