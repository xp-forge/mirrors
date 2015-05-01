<?hh namespace lang\mirrors\unittest\fixture;

/**
 * Fixture for Hack types
 *
 * @see    http://docs.hhvm.com/manual/en/hack.annotations.php
 * @see    http://docs.hhvm.com/manual/en/hack.annotations.this.php
 * @see    http://docs.hhvm.com/manual/en/hack.annotations.mixedtypes.php
 * @see    http://docs.hhvm.com/manual/en/hack.annotations.functiontypes.php
 * @see    http://docs.hhvm.com/manual/en/hack.nullable.php
 */
class FixtureHackTypedClass extends \lang\Object {
  public int $typed;
  public parent $parentTyped;
  public this $thisTyped;
  public array<string> $arrayTyped;
  public array<string, self> $mapTyped;
  public array $unTypedArrayTyped;
  public (function (string, int): void) $funcTyped;
  public ?int $nullableTyped;
  public mixed $mixedTyped;
  public $unTyped;

  public function typed(): int { }
  public function thisTyped(): this { }
  public function parentTyped(): parent { }
  public function arrayTyped(): array<string> { }
  public function mapTyped(): array<string, self> { }
  public function unTypedArrayTyped(): array { }
  public function funcTyped(): (function (string, int): void) { }
  public function nullableTyped(): ?int { }
  public function mixedTyped(): mixed { }
  public function unTyped() { }

  public function parameters(
    int $typed,
    parent $parentTyped,
    this $thisTyped,
    array<string> $arrayTyped,
    array<string, self> $mapTyped,
    array $unTypedArrayTyped,
    (function (string, int): void) $funcTyped,
    ?int $nullableTyped,
    mixed $mixedTyped,
    $unTyped
  ) { }
}