<?php namespace lang\mirrors;

/**
 * Class kinds
 */
abstract class Kind extends \lang\Enum {
  public static $CLASS, $INTERFACE, $TRAIT, $ENUM;

  static function __static() {
    self::$CLASS= newinstance(__CLASS__, [0, 'class'], '{
      static function __static() { }
      public function isClass() { return true; }
    }');
    self::$INTERFACE= newinstance(__CLASS__, [1, 'interface'], '{
      static function __static() { }
      public function isInterface() { return true; }
    }');
    self::$TRAIT= newinstance(__CLASS__, [2, 'trait'], '{
      static function __static() { }
      public function isTrait() { return true; }
    }');
    self::$ENUM= newinstance(__CLASS__, [3, 'enum'], '{
      static function __static() { }
      public function isEnum() { return true; }
    }');
  }

  /** @return bool */
  public function isClass() { return false; }

  /** @return bool */
  public function isInterface() { return false; }

  /** @return bool */
  public function isTrait() { return false; }

  /** @return bool */
  public function isEnum() { return false; }
}