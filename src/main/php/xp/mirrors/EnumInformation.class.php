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
      $out->write(' extends ', $parent->name());
    }

    $separator= false;
    $out->writeLine(' {');
    $this->displayConstants($this->mirror, $out, $separator);

    foreach (Enum::valuesOf(XPClass::forName($this->mirror->name())) as $member) {
      $out->write('  ',  $member->ordinal(), ': ', $member->name());
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

    $this->displayMembers($this->mirror->methods(), $out, $separator);
    $out->writeLine('}');
  }
}