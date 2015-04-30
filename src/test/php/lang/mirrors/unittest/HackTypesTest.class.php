<?php namespace lang\mirrors\unittest;

use lang\mirrors\HackTypes;
use lang\Type;
use lang\XPClass;
use lang\Primitive;
use lang\ArrayType;
use lang\MapType;
use lang\FunctionType;

class HackTypesTest extends \unittest\TestCase {
  private $types;

  public function setUp() {
    $this->types= new HackTypes(new \ReflectionClass(self::class));
  }

  #[@test]
  public function hh_int() {
    $this->assertEquals(Primitive::$INT, $this->types->map('HH\\int'));
  }

  #[@test]
  public function hh_string() {
    $this->assertEquals(Primitive::$STRING, $this->types->map('HH\\string'));
  }

  #[@test]
  public function hh_double() {
    $this->assertEquals(Primitive::$DOUBLE, $this->types->map('HH\\double'));
  }

  #[@test]
  public function hh_bool() {
    $this->assertEquals(Primitive::$BOOL, $this->types->map('HH\\bool'));
  }

  #[@test]
  public function hh_void() {
    $this->assertEquals(Type::$VOID, $this->types->map('HH\\void'));
  }

  #[@test]
  public function hh_mixed() {
    $this->assertEquals(Type::$VAR, $this->types->map('HH\\mixed'));
  }

  #[@test]
  public function untyped_callable() {
    $this->assertEquals(Type::$CALLABLE, $this->types->map('callable'));
  }

  #[@test]
  public function untyped_array() {
    $this->assertEquals(Type::$ARRAY, $this->types->map('array'));
  }

  #[@test]
  public function array_of_string() {
    $this->assertEquals(new ArrayType(Primitive::$STRING), $this->types->map('array<HH\string>'));
  }

  #[@test]
  public function array_of_array_of_string() {
    $this->assertEquals(new ArrayType(new ArrayType(Primitive::$STRING)), $this->types->map('array<array<HH\string>>'));
  }

  #[@test]
  public function array_of_map_to_string() {
    $this->assertEquals(new ArrayType(new MapType(Primitive::$STRING)), $this->types->map('array<array<HH\string, HH\string>>'));
  }

  #[@test]
  public function map_to_string() {
    $this->assertEquals(new MapType(Primitive::$STRING), $this->types->map('array<HH\string, HH\string>'));
  }

  #[@test]
  public function map_to_array_of_string() {
    $this->assertEquals(new MapType(new ArrayType(Primitive::$STRING)), $this->types->map('array<HH\string, array<HH\string>>'));
  }

  #[@test]
  public function testcase() {
    $this->assertEquals(XPClass::forName('unittest.TestCase'), $this->types->map('unittest\TestCase'));
  }

  #[@test]
  public function nullable_testcase() {
    $this->assertEquals(XPClass::forName('unittest.TestCase'), $this->types->map('?unittest\TestCase'));
  }

  #[@test]
  public function nullable_string() {
    $this->assertEquals(Primitive::$STRING, $this->types->map('?string'));
  }

  #[@test]
  public function self_reference() {
    $this->assertEquals(new XPClass(self::class), $this->types->map('self'));
  }

  #[@test]
  public function parent_reference() {
    $this->assertEquals(new XPClass(parent::class), $this->types->map('parent'));
  }

  #[@test]
  public function hh_this_reference() {
    $this->assertEquals(new XPClass(self::class), $this->types->map('HH\this'));
  }

  #[@test]
  public function function_without_parameters() {
    $this->assertEquals(
      new FunctionType([], Type::$VOID),
      $this->types->map('(function(): HH\void)')
    );
  }

  #[@test]
  public function function_with_one_parameter() {
    $this->assertEquals(
      new FunctionType([Primitive::$STRING], Type::$VOID),
      $this->types->map('(function(HH\string): HH\void)')
    );
  }

  #[@test]
  public function function_with_two_parameters() {
    $this->assertEquals(
      new FunctionType([Primitive::$STRING, Primitive::$INT], Type::$VOID),
      $this->types->map('(function(HH\string, HH\int): HH\void)')
    );
  }
}