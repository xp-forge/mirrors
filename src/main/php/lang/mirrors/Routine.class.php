<?php namespace lang\mirrors;

use lang\Type;
use lang\mirrors\parse\TagsSyntax;
use lang\mirrors\parse\TagsSource;

/**
 * Base class for constructors and methods
 */
abstract class Routine extends Member {
  protected static $kind= 'method';
  private $parameters;
  private $tags= null;

  /**
   * Creates a new method
   *
   * @param  lang.mirrors.TypeMirror $mirror
   * @param  php.ReflectionMethod $reflect
   */
  public function __construct($mirror, $reflect) {
    parent::__construct($mirror, $reflect);
    $reflect->setAccessible(true);
    $this->parameters= new Parameters($this, $reflect);
  }

  /** @return string */
  public function comment() {
    $raw= $this->reflect->getDocComment();
    if (false === $raw) {
      return null;
    } else {
      $text= trim(preg_replace('/\n\s+\* ?/', "\n", "\n".substr(
        $raw,
        4,                          // "/**\n"
        strpos($raw, '* @') - 2     // position of first details token
      )));
      return '' === $text ? null : $text;
    }
  }

  /**
   * Returns tags from doc comment
   *
   * @return [:var]
   */
  public function tags() {
    if (null === $this->tags) {
      $this->tags= ['param' => [], 'return' => [], 'throws' => []];
      if ($raw= $this->reflect->getDocComment()) {
        $parsed= (new TagsSyntax())->parse(new TagsSource(preg_replace('/\n\s+\* ?/', "\n", substr(
          $raw,
          strpos($raw, '* @') + 2,    // position of first details token
          - 2                         // "*/"
        ))));
        $this->tags= array_merge($this->tags, $parsed);
      }
    }
    return $this->tags;
  }

  /** @return lang.mirrors.Parameters */
  public function parameters() { return $this->parameters; }

  /** @return lang.mirrors.Throws */
  public function throws() { return new Throws($this); }
}