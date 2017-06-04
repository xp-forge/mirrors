<?php namespace lang\mirrors;

/**
 * Base class for fields and methods
 */
abstract class Members implements \lang\Value, \IteratorAggregate {
  use ListOf;

  protected $mirror;

  /**
   * Creates a new members instance
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
   * Iterates over members.
   *
   * @param  util.Filter $filter
   * @return iterable
   */
  public abstract function all($filter= null);

  /**
   * Iterates over declared members.
   *
   * @param  util.Filter $filter
   * @return iterable
   */
  public abstract function declared($filter= null);

  /**
   * Iterates over all methods
   *
   * @return iterable
   */
  public function getIterator() { return $this->all(); }

}