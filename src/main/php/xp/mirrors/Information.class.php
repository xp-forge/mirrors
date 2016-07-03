<?php namespace xp\mirrors;

use lang\mirrors\Kind;
use lang\IllegalStateException;

abstract class Information {

  /**
   * Returns declaration
   *
   * @param  lang.mirrors.TypeMirror $mirror
   * @return string
   */
  protected static function declarationOf($mirror) {
    switch ($mirror->kind()) {
      case Kind::$ENUM: return $mirror->modifiers()->names().' enum '.$mirror->name(); break;
      case Kind::$TRAIT: return 'public trait '.$mirror->name(); break;
      case Kind::$INTERFACE: return 'public interface '.$mirror->name(); break;
      case Kind::$CLASS: return $mirror->modifiers()->names().' class '.$mirror->name(); break;
      default: throw new IllegalStateException('Unknown kind '.$mirror->kind()->name());
    }
  }

  /** @return php.Generator */
  public abstract function sources();

  /**
   * Display information
   *
   * @param  io.StringWriter $out
   * @return void
   */
  public abstract function display($out);
}