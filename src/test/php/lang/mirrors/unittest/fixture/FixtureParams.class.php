<?php namespace lang\mirrors\unittest\fixture;

use lang\Type;

class FixtureParams extends \lang\Object {
  const CONSTANT = 'Test';

  private function noParam() { }

  private function oneParam($arg) { }

  private function oneOptionalParam($arg= null) { }

  private function oneConstantOptionalParam($arg= self::CONSTANT) { }

  private function oneArrayOptionalParam($arg= [1, 2, 3]) { }

  private function oneVariadicParam(... $arg) { }

  private function oneTypeHintedParam(Type $arg) { }

  private function oneSelfTypeHintedParam(self $arg) { }

  private function oneArrayTypeHintedParam(array $arg) { }

  private function oneCallableTypeHintedParam(callable $arg) { }

  /** @param lang.Type */
  private function oneDocumentedTypeParam($arg) { }

  /**
   * Fixture
   *
   * @param lang.Type $a
   * @param var $b
   */
  private function twoDocumentedTypeParams($a, $b) { }

  #[@$arg: test]
  private function oneAnnotatedParam($arg) { }
}