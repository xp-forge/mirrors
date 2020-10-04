<?php namespace lang\mirrors\parse;

class StringSource extends \text\parse\Tokens {
  private $tokens;

  public function __construct($string) {
    $this->tokens= token_get_all('<?php '.$string);
    array_shift($this->tokens);
  }

  public function next() {
    return array_shift($this->tokens);
  }

  public function name($token) {
    return is_int($token) ? token_name($token) : '`'.$token.'`';
  }

  public function lastComment() {
    return null;
  }
}