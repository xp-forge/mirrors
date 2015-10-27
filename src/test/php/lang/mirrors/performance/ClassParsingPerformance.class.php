<?php namespace lang\mirrors\performance;

use lang\mirrors\parse\ClassSyntax;
use lang\mirrors\parse\ClassSource;
use lang\reflect\ClassParser;
use lang\XPClass;

class ClassParsingPerformance extends \util\profiling\Measurable {

  /** @return var[][] */
  public static function classes() {
    return [
      [XPClass::forName('lang.mirrors.unittest.SourceTest')],
      [XPClass::forName('lang.mirrors.FromReflection')],
      [XPClass::forName('lang.mirrors.Sources')]
    ];
  }

  #[@measure, @values('classes')]
  public function codeUnitOf($class) {
    $unit= (new ClassSyntax())->codeUnitOf($class->getName())->declaration();
    return isset($unit['name']);
  }

  #[@measure, @values('classes')]
  public function parseDetails($class) {
    $details= XPClass::detailsForClass($class->getName());
    return isset($details['class']);
  }
}