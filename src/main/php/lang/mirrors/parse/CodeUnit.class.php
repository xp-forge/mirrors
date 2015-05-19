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

  /**
   * Returns an incomplete code unit
   *
   * @param  string $class
   * @return self
   */
  public static function ofIcomplete($class) {
    $p= strrpos($class, '\\');
    return new self(substr($class, 0, $p), [], [
      'kind'        => null,
      'name'        => false === $p ? $class : substr($class, $p + 1),
      'annotations' => [null => null],
      'field'       => [],
      'method'      => [],
    ]);
  }

  /** @return bool */
  public function isIncomplete() { return null === $this->declaration['kind']; }

  /** @return string */
  public function package() { return $this->package; }

  /** @return string[] */
  public function imports() { return $this->imports; }

  /** @return [:var] */
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