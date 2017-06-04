<?php namespace lang\mirrors;

class Throws implements \IteratorAggregate {
  private $mirror, $tags;

  /**
   * Creates a new traits instance
   *
   * @param  lang.mirrors.Method $mirror
   */
  public function __construct($mirror) {
    $this->mirror= $mirror;
    $this->tags= $this->mirror->tags()['throws'];
  }

  /**
   * Checks whether a given thrown exception is contained in this collection
   *
   * @param  var $arg Either a string or a type mirror
   * @return bool
   */
  public function contains($arg) {
    $search= $arg instanceof TypeMirror ? $arg->name() : strtr($arg, '\\', '.');
    foreach ($this->tags as $tag) {
      if ($search === $tag->name()) return true;
    }
    return false;
  }

  /**
   * Iterates over all interfaces
   *
   * @return iterable
   */
  public function getIterator() {
    $type= $this->mirror->declaredIn();
    foreach ($this->tags as $tag) {
      yield $type->resolve($tag->name());
    }
  }
}