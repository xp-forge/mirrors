<?php namespace lang\mirrors\performance;

use lang\XPClass;
use lang\mirrors\parse\{ClassSource, ClassSyntax};
use unittest\{Measure, Values};

class ClassParsingPerformance extends \util\profiling\Measurable {

  /** @return var[][] */
  public static function classes() {
    return [
      [XPClass::forName('lang.mirrors.unittest.SourceTest')],
      [XPClass::forName('lang.mirrors.FromReflection')],
      [XPClass::forName('lang.mirrors.Sources')]
    ];
  }

  #[Measure, Values('classes')]
  public function codeUnitOf($class) {
    $unit= (new ClassSyntax())->codeUnitOf($class->getName())->declaration();
    return isset($unit['name']);
  }

  #[Measure, Values('classes')]
  public function parseDetails($class) {
    $details= XPClass::detailsForClass($class->getName());
    return isset($details['class']);
  }
}