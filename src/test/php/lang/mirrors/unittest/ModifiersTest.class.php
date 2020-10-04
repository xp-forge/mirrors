<?php namespace lang\mirrors\unittest;

use lang\mirrors\Modifiers;
use unittest\{Test, Values};

class ModifiersTest extends AbstractMethodTest {

  /** @return var[][] */
  private function modifiers() {
    return [
      [Modifiers::IS_PUBLIC, 'public'],
      [Modifiers::IS_PROTECTED, 'protected'],
      [Modifiers::IS_PRIVATE, 'private'],

      [Modifiers::IS_STATIC | Modifiers::IS_PUBLIC, 'public static'],
      [Modifiers::IS_STATIC | Modifiers::IS_PROTECTED, 'protected static'],
      [Modifiers::IS_STATIC | Modifiers::IS_PRIVATE, 'private static'],

      [Modifiers::IS_FINAL | Modifiers::IS_PUBLIC, 'public final'],
      [Modifiers::IS_FINAL | Modifiers::IS_PROTECTED, 'protected final'],
      [Modifiers::IS_FINAL | Modifiers::IS_PRIVATE, 'private final'],

      [Modifiers::IS_ABSTRACT | Modifiers::IS_PUBLIC, 'public abstract'],
      [Modifiers::IS_ABSTRACT | Modifiers::IS_PROTECTED, 'protected abstract'],
      [Modifiers::IS_ABSTRACT | Modifiers::IS_PRIVATE, 'private abstract'],
    ];
  }

  /**
   * Returns fixtures filtered by a given modifier bit
   *
   * @param  int $filter
   * @return var[][]
   */
  public function modifiersWith($filter) {
    return array_filter($this->modifiers(), function($e) use($filter) { return $e[0] & $filter; });
  }

  #[Test, Values('modifiers')]
  public function bits_from_int($bits, $names) {
    $this->assertEquals($bits, (new Modifiers($bits))->bits());
  }

  #[Test, Values('modifiers')]
  public function bits_from_array($bits, $names) {
    $this->assertEquals($bits, (new Modifiers(explode(' ', $names)))->bits());
  }

  #[Test, Values('modifiers')]
  public function bits_from_string($bits, $names) {
    $this->assertEquals($bits, (new Modifiers($names))->bits());
  }

  #[Test]
  public function passing_zero_yields_public_as_default() {
    $this->assertEquals(Modifiers::IS_PUBLIC, (new Modifiers(0))->bits());
  }

  #[Test]
  public function passing_empty_string_yields_public_as_default() {
    $this->assertEquals(Modifiers::IS_PUBLIC, (new Modifiers(''))->bits());
  }

  #[Test]
  public function passing_empty_array_yields_public_as_default() {
    $this->assertEquals(Modifiers::IS_PUBLIC, (new Modifiers([]))->bits());
  }

  #[Test, Values('modifiers')]
  public function names($bits, $names) {
    $this->assertEquals($names, (new Modifiers($bits))->names());
  }

  #[Test, Values(['source' => 'modifiersWith', 'args' => Modifiers::IS_PUBLIC])]
  public function isPublic($bits, $names) {
    $this->assertTrue((new Modifiers($bits))->isPublic());
    $this->assertFalse((new Modifiers($bits))->isProtected());
    $this->assertFalse((new Modifiers($bits))->isPrivate());
  }

  #[Test, Values(['source' => 'modifiersWith', 'args' => Modifiers::IS_PROTECTED])]
  public function isProtected($bits, $names) {
    $this->assertFalse((new Modifiers($bits))->isPublic());
    $this->assertTrue((new Modifiers($bits))->isProtected());
    $this->assertFalse((new Modifiers($bits))->isPrivate());
  }

  #[Test, Values(['source' => 'modifiersWith', 'args' => Modifiers::IS_PRIVATE])]
  public function isPrivate($bits, $names) {
    $this->assertFalse((new Modifiers($bits))->isPublic());
    $this->assertFalse((new Modifiers($bits))->isProtected());
    $this->assertTrue((new Modifiers($bits))->isPrivate());
  }

  #[Test, Values(['source' => 'modifiersWith', 'args' => Modifiers::IS_STATIC])]
  public function isStatic($bits, $names) {
    $this->assertTrue((new Modifiers($bits))->isStatic());
  }

  #[Test, Values(['source' => 'modifiersWith', 'args' => Modifiers::IS_ABSTRACT])]
  public function isAbstract($bits, $names) {
    $this->assertTrue((new Modifiers($bits))->isAbstract());
  }

  #[Test, Values(['source' => 'modifiersWith', 'args' => Modifiers::IS_FINAL])]
  public function isFinal($bits, $names) {
    $this->assertTrue((new Modifiers($bits))->isFinal());
  }

  #[Test]
  public function equals_itself() {
    $modifiers= new Modifiers(Modifiers::IS_PUBLIC);
    $this->assertEquals($modifiers, $modifiers);
  }

  #[Test]
  public function equals_other_instance_with_same_bits() {
    $this->assertEquals(new Modifiers(Modifiers::IS_PUBLIC), new Modifiers(Modifiers::IS_PUBLIC));
  }

  #[Test]
  public function does_not_equal_instance_with_differing_bits() {
    $this->assertNotEquals(new Modifiers(Modifiers::IS_PUBLIC), new Modifiers(Modifiers::IS_PROTECTED));
  }
}