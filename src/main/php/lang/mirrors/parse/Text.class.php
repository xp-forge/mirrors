<?php namespace lang\mirrors\parse;

use text\parse\Values;
use text\parse\Unexpected;

class Text extends \text\parse\Rule {

  /**
   * Consume
   *
   * @param  [:text.parse.Rule] $rules
   * @param  text.parse.Tokens $tokens
   * @param  var[] $values
   * @return text.parse.Consumed
   */
  public function consume($rules, $tokens, $values) {
    $text= '';
    do {
      $token= $tokens->token();
      if ("\n" === $token) {
        break;
      } else {
        $text.= is_array($token) ? $token[1] : $token;
      }
      $tokens->forward();
    } while ($token);

    return new Values(trim($text));
  }
}