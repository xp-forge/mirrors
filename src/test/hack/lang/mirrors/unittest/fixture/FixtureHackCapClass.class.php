<?hh namespace lang\mirrors\unittest\fixture;

/**
 * Fixture for Hack's Constructor Argument Promotion.
 *
 * @see    http://docs.hhvm.com/manual/en/hack.constructorargumentpromotion.php
 */
class FixtureHackCapClass {

  public function __construct(
    public string $name,
    protected int $age,
    private bool $gender
  ) { }
}