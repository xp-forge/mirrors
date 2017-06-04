<?php namespace lang\mirrors;

class Predicates implements \util\Filter {
  private $filters= [];

  /**
   * Returns instance members
   *
   * @return self
   */
  public function ofInstance() {
    return $this->with(function($member) {
      return !$member->modifiers()->isStatic();
    });
  }

  /**
   * Returns class members
   *
   * @return self
   */
  public function ofClass() {
    return $this->with(function($member) {
      return $member->modifiers()->isStatic();
    });
  }

  /**
   * Returns members with a given annotation
   *
   * @param  string $annotation
   * @return self
   */
  public function withAnnotation($annotation) {
    return $this->with(function($member) use($annotation) {
      return $member->annotations()->provides($annotation);
    });
  }

  /**
   * Returns members that match a given predicate
   *
   * @param  function(lang.mirrors.Member): bool $predicate
   * @return self
   */
  public function with($predicate) {
    $this->filters[]= $predicate;
    return $this;
  }

  /**
   * Returns whether to accept this member
   *
   * @param  lang.mirrors.Member $member
   * @return bool
   */
  public function accept($member) {
    foreach ($this->filters as $filter) {
      if (!$filter($member)) return false;
    }
    return true;
  }
}