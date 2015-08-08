<?php namespace lang\mirrors;

abstract class Members extends \lang\Object implements \IteratorAggregate {
  protected $mirror;

  /**
   * Creates a new methods instance
   *
   * @param  lang.mirrors.TypeMirror $mirror
   */
  public function __construct(TypeMirror $mirror) {
    $this->mirror= $mirror;
  }

  /**
   * Returns class members
   *
   * @return lang.mirrors.Predicates
   */
  public static function ofClass() { return (new Predicates())->ofClass(); }

  /**
   * Returns instance members
   *
   * @return lang.mirrors.Predicates
   */
  public static function ofInstance() { return (new Predicates())->ofInstance(); }

  /**
   * Returns members with a given annotation
   *
   * @param  string $annotation
   * @return lang.mirrors.Predicates
   */
  public static function withAnnotation($annotation) { return (new Predicates())->withAnnotation($annotation); }

  /**
   * Returns members that match a given predicate
   *
   * @param  function(lang.mirrors.Member): bool $predicate
   * @return lang.mirrors.Predicates
   */
  public static function with($predicate) { return (new Predicates())->with($predicate); }

  /**
   * Creates a string representation
   *
   * @return string
   */
  public function toString() {
    $s= nameof($this)."@[\n";
    foreach ($this as $member) {
      $s.= '  '.(string)$member."\n";
    }
    return $s.']';
  }
}