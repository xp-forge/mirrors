<?php namespace lang\mirrors\unittest\fixture;

trait FixtureTrait {

  /** @type int */
  private $traitField;

  #[@fixture]
  private $annotatedTraitField;

  /** @return void */
  private function traitMethod() { }

  #[@fixture]
  private function annotatedTraitMethod() { }
}