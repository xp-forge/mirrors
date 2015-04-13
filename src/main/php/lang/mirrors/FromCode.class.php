<?php namespace lang\mirrors;

use lang\mirrors\parse\ClassSyntax;
use lang\mirrors\parse\ClassSource;

class FromCode extends \lang\Object implements Source {
  private $unit;

  public function __construct($name) {
    $this->unit= (new ClassSyntax())->parse(new ClassSource(strtr($name, '\\', '.')));
  }

  /** @return string */
  public function typeName() { 
    $package= $this->unit->package();
    return ($package ? $package.'.' : '').$this->unit->declaration()['name'];
  }
}