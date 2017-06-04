<?php namespace lang\mirrors;

use lang\Type;
use lang\XPClass;
use lang\ArrayType;
use lang\MapType;
use lang\FunctionType;

/**
 * Maps a Hack type literal as returned e.g. by HHVM reflection's `getTypeText()`
 * methods on various of its reflection objects to an XP type.
 *
 * @see    http://docs.hhvm.com/manual/en/hack.annotations.php
 * @test   xp://lang.mirrors.unittest.HackTypesTest
 */
class HackTypes {
  private $reflect;

  /**
   * Creates a new type mapper
   *
   * @param  php.ReflectionClass $reflect
   */
  public function __construct($reflect) {
    $this->reflect= $reflect;
  }

  /**
   * Parses an array type literal - `array<T>` or `array<K, V>`.
   *
   * @param  string $literal
   * @return lang.Type
   */
  private function arrayType($literal) {
    $components= [];
    for ($brackets= 1, $o= $i= 6, $s= strlen($literal); $i < $s; $i++) {
      if ('>' === $literal{$i} && 1 === $brackets) {
        $components[]= $this->map(ltrim(substr($literal, $o, $i - $o), ' '));
        break;
      } else if (',' === $literal{$i} && 1 === $brackets) {
        $components[]= $this->map(ltrim(substr($literal, $o, $i - $o), ' '));
        $o= $i + 1;
      } else if ('<' === $literal{$i}) {
        $brackets++;
      } else if ('>' === $literal{$i}) {
        $brackets--;
      }
    }

    if (1 === sizeof($components)) {
      return new ArrayType($components[0]);
    } else {
      return new MapType($components[1]);
    }
  }

  /**
   * Parses a function type literal - `(function(T1, T2, ..., Tn): T)`.
   *
   * @param  string $literal
   * @return lang.Type
   */
  private function functionType($literal) {
    $signature= [];
    $o= strpos($literal, '(', 1) + 1;
    if (')' !== $literal{$o}) for ($brackets= 0, $i= --$o, $s= strlen($literal); $i < $s; $i++) {
      if (':' === $literal{$i} && 0 === $brackets) {
        $signature[]= $this->map(substr($literal, $o + 1, $i - $o- 2));
        $o= $i+ 1;
        break;
      } else if (',' === $literal{$i} && 1 === $brackets) {
        $signature[]= $this->map(substr($literal, $o + 1, $i - $o - 1));
        $o= $i+ 1;
      } else if ('(' === $literal{$i}) {
        $brackets++;
      } else if (')' === $literal{$i}) {
        $brackets--;
      }
    }
    return new FunctionType($signature, $this->map(trim(substr($literal, $o + 1, -1), ': ')));
  }

  /**
   * Map
   *
   * @param  string $type
   * @return lang.Type
   */
  public function map($type) {
    if ('self' === $type || 'HH\\this' === $type) {
      return new XPClass($this->reflect);
    } else if ('parent' === $type) {
      return new XPClass($this->reflect->getParentClass());
    } else if ('array' === $type) {
      return Type::$ARRAY;
    } else if ('callable' === $type) {
      return Type::$CALLABLE;
    } else if ('HH\\mixed' === $type) {
      return Type::$VAR;
    } else if (0 === strncmp($type, 'array<', 6)) {
      return $this->arrayType($type);
    } else if (0 === strncmp($type, '(function', 9)) {
      return $this->functionType($type);
    } else {
      return Type::forName(ltrim(strtr($type, ['HH\\' => '']), '?'));
    }
  }
}