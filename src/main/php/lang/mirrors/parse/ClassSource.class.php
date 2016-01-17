<?php namespace lang\mirrors\parse;

use lang\IllegalArgumentException;
use lang\ClassLoader;
use lang\IClassLoader;
use lang\ClassNotFoundException;
use lang\ClassFormatException;

/**
 * Parser source from a class
 *
 * @test  xp://lang.mirrors.unittest.parse.ClassSourceTest
 */
class ClassSource extends \text\parse\Tokens {
  protected $tokens;
  private $comment, $syntax;
  private $raw= false;

  /**
   * Creates a new class source
   *
   * @param  string $class Dotted fully qualified name
   * @throws lang.ClassFormatException
   */
  public function __construct($class) {
    $cl= ClassLoader::getDefault()->findClass($class);
    if ($cl instanceof IClassLoader) {
      $this->tokenize($cl->loadClassBytes($class), $class);
    } else {
      $this->tokens= null;
    }
  }

  /** @return bool */
  public function present() { return is_array($this->tokens); }

  /**
   * Tokenize code
   *
   * @param  string $code Class source code
   * @param  string $class Class name
   * @throws lang.ClassFormatException
   */
  protected function tokenize($code, $class) {
    if (0 === strncmp($code, '<?hh', 4)) {
      $this->syntax= 'hh';
      $this->tokens= token_get_all('<?php'.substr($code, 4));
    } else if (0 === strncmp($code, '<?php', 5)) {
      $this->syntax= 'php';
      $this->tokens= token_get_all($code);
    } else {
      throw new ClassFormatException($class.' does not start with PHP open tag: '.substr($code, 0, 6).'...');
    }

    array_shift($this->tokens);
  }

  /** @return string */
  public function usedSyntax() { return $this->syntax; }

  /** @return string */
  public function lastComment() { return $this->comment; }

  /** @return var */
  protected function next() {
    static $annotations= [T_COMMENT, T_WHITESPACE];

    do {
      $token= array_shift($this->tokens);
      if ($this->raw) {
        return $token;
      } else if (T_WHITESPACE === $token[0]) {
        // Skip
      } else if (T_COMMENT === $token[0]) {
        if ('#' === $token[1]{0}) {
          $annotation= '<?=';
          do {
            $annotation.= trim(substr($token[1], 1));
            $token= array_shift($this->tokens);
          } while (in_array($token[0], $annotations));

          $expand= token_get_all($annotation);
          $expand[0]= '#';
          $this->tokens= array_merge($expand, [$token], $this->tokens);
        }
      } else if (T_DOC_COMMENT === $token[0]) {
        $this->comment= $token[1];
      } else {
        return $token;
      }
    } while (true);
  }

  public function block($open, $close) {
    $braces= 1;
    $block= '';
    $this->raw= true;

    do {
      $token= $this->token();
      if ($open === $token) {
        $braces++;
        $block.= $open;
      } else if ($close === $token) {
        $braces--;
        if ($braces <= 0) {
          $this->raw= false;
          $this->forward();
          return $block;
        }
        $block.= $close;
      } else {
        $block.= is_array($token) ? $token[1] : $token;
      }
      $this->forward();
    } while ($token);

    $this->raw= false;
    return null;
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