<?php namespace lang\mirrors\unittest;

class MemberFixture extends AbstractMemberFixture {
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
}