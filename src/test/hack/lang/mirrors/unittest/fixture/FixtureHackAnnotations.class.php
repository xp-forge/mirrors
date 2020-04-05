<?hh namespace lang\mirrors\unittest\fixture;

/**
 * Fixture for HHVM annotations test
 *
 * *Note: Fields cannot have attributes in Hack language*
 *
 * @see    http://docs.hhvm.com/manual/en/hack.attributes.php
 * @see    https://github.com/facebook/hhvm/issues/3605
 */
<<test, runtime('~3.6'), expect(['class' => 'lang.IllegalArgumentException'])>>
class FixtureHackAnnotations {

  #[@test, @runtime('~3.6'), @expect(['class' => 'lang.IllegalArgumentException'])]
  public $field;

  #[@field(['type' => 'varchar(255)'])]
  public $type;

  #[@field(['num' => 'int'])]
  public $num;

  <<test, runtime('~3.6'), expect(['class' => 'lang.IllegalArgumentException'])>>
  public function __construct() { }

  <<test, runtime('~3.6'), expect(['class' => 'lang.IllegalArgumentException'])>>
  public function method(
    <<test, runtime('~3.6'), expect(['class' => 'lang.IllegalArgumentException'])>> $param
  ) { }
}