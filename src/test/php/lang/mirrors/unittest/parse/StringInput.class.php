<?php namespace lang\mirrors\unittest\parse;

class StringInput extends \lang\mirrors\parse\ClassSource {

  /**
   * Reads tokens from string instead of via class loader
   *
   * @param  string $input
   */
  public function __construct($input) {
    $this->tokens= token_get_all($input);
  }
}