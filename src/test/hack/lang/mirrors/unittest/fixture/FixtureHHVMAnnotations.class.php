<?hh namespace lang\mirrors\unittest\fixture;

<<test, runtime('~3.6'), expect(['class' => 'lang.IllegalArgumentException'])>>
class FixtureHHVMAnnotations extends \lang\Object {

  // Fields cannot have attributes in Hack language
  // See https://github.com/facebook/hhvm/issues/3605
  #[@test, @runtime('~3.6'), @expect(class = 'lang.IllegalArgumentException')]
  public $field;

  <<test, runtime('~3.6'), expect(['class' => 'lang.IllegalArgumentException'])>>
  public function __construct() { }
  
  <<test, runtime('~3.6'), expect(['class' => 'lang.IllegalArgumentException'])>>
  public function method() { }
}