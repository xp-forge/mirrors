<?php namespace xp\mirrors;

use lang\mirrors\TypeMirror;
use lang\Enum;
use lang\XPClass;

class EnumInformation extends TypeKindInformation {

  /**
   * Display information
   *
   * @param  io.StringWriter $out
   * @return void
   */
  public function display($out) {
    $out->write(self::declarationOf($this->mirror));
    $parent= $this->mirror->parent();
    if ('lang.Enum' !== $parent->name()) {
      $this->displayExtensions([$parent], $out, 'extends');
    }
    $this->displayExtensions($this->mirror->interfaces()->declared(), $out, 'implements');

    $separator= false;
    $out->writeLine(' {');
    $this->displayMembers($this->mirror->constants(), $out, $separator);

    foreach (Enum::valuesOf(XPClass::forName($this->mirror->name())) as $member) {
      $out->write('  ', $member->name(), '(', $member->ordinal(), ')');
      $mirror= new TypeMirror(typeof($member));
      if ($mirror->isSubtypeOf($this->mirror)) {
        $out->writeLine(' {');
        foreach ($mirror->methods()->declared() as $method) {
          $out->writeLine('    ', (string)$method);
        }
        $separator= true;
        $out->writeLine('  }');
      } else {
        $out->writeLine();
        $separator= true;
      }
    }

    $constructor= $this->mirror->constructor();
    if ($constructor->present()) {
      $this->displayMembers([$constructor], $out, $separator);
    }
    $this->displayMembers($this->mirror->methods(), $out, $separator);
    $out->writeLine('}');
  }
}