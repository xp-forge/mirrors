<?php namespace lang\mirrors\parse;

use lang\IllegalArgumentException;

class ClassSource extends \text\parse\Tokens {
  protected $tokens;
  private $comment;

  public function __construct($class) {
    if (!isset(\xp::$cl[$class])) {
      throw new IllegalArgumentException('No source for '.$class);
    }

    sscanf(\xp::$cl[$class], '%[^:]://%[^$]', $loader, $argument);
    $cl= call_user_func([literal($loader), 'instanceFor'], $argument);
    $this->tokens= token_get_all($cl->loadClassBytes($class));
  }

  /** @return string */
  public function lastComment() { return $this->comment; }

  protected function next() {
    static $annotations= [T_COMMENT, T_WHITESPACE];

    do {
      $token= array_shift($this->tokens);
      if (T_WHITESPACE === $token[0]) {
        // Skip
      } else if (T_COMMENT === $token[0]) {
        if ('#' === $token[1]{0}) {
          $annotation= '<?=';
          do {
            $annotation.= trim(substr($token[1], 1));
            $token= array_shift($this->tokens);
          } while (in_array($token[0], $annotations));
          $this->tokens= array_merge(array_slice(token_get_all($annotation), 1), [$token], $this->tokens);
        }
      } else if (T_DOC_COMMENT === $token[0]) {
        $this->comment= $token[1];
      } else {
        return $token;
      }
    } while (true);
  }

  /**
   * Returns the name of a given token
   *
   * @param  var $token Either an integer ID or a character
   * @return string
   */
  protected function name($token) {
    return is_int($token) ? token_name($token) : '`'.$token.'`';
  }
}