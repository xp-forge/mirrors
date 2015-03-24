<?php namespace lang\mirrors\parse;

use util\Objects;

class CodeUnit extends \lang\Object {
  private $package, $imports;

  /**
   * Creates a new code unit
   *
   * @param  string $package
   * @param  string[] $imports
   * @param  var $declaration
   */
  public function __construct($package, $imports, $declaration) {
    $this->package= $package;
    $this->imports= $imports;
    $this->declaration= $declaration;
  }

  /** @return string */
  public function package() { return $this->package; }

  /** @return string[] */
  public function imports() { return $this->imports; }

  /** @return var */
  public function declaration() { return $this->declaration; }

  /**
   * Returns whether a given value is equal to this code unit
   *
   * @param  var $cmp
   * @return bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && (
      $this->package === $cmp->package &&
      Objects::equal($this->imports, $cmp->imports) &&
      Objects::equal($this->declaration, $cmp->declaration)
    );
  }
}