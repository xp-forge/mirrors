<?php namespace lang\mirrors\unittest\fixture;

trait FixtureTrait {

  /** @type int */
  private $traitField;

  #[Fixture]
  private $annotatedTraitField;

  /** @return void */
  private function traitMethod() { }

  #[Fixture]
  private function annotatedTraitMethod() { }
}