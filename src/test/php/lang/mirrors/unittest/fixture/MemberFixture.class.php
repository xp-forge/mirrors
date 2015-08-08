<?php namespace lang\mirrors\unittest\fixture;

class MemberFixture extends AbstractMemberFixture {
  use FixtureTrait;
  const CONSTANT= 1;

  public $publicInstanceField;
  protected $protectedInstanceField;
  private $privateInstanceField;
  public static $publicClassField;
  protected static $protectedClassField;
  private static $privateClassField;

  public function publicInstanceMethod() { }
  protected function protectedInstanceMethod() { }
  private function privateInstanceMethod() { }
  public static function publicClassMethod() { }
  protected static function protectedClassMethod() { }
  private static function privateClassMethod() { }

  #[@annotation]
  public function annotatedInstanceMethod() { }
  #[@annotation]
  public static function annotatedClassMethod() { }
}