<?php namespace xp\mirrors;

use lang\reflect\Package;
use lang\ClassLoader;
use lang\mirrors\TypeMirror;

class PackageInformation {
  private $package;

  /**
   * Creates a new type information instance
   *
   * @param  lang.reflect.Package $package
   */
  public function __construct($package) {
    $this->package= $package;
  }

  public function source() { return $this->package; }

  /**
   * Display information
   *
   * @param  io.StringWriter $out
   * @return void
   */
  public function display($out) {
    $out->writeLine('package ', $this->package->getName(), ' {');

    // Child packages
    foreach ($this->package->getPackages() as $child) {
      $out->writeLine('  package ', $child->getName());
    }
    
    // Classes
    $order= [
      'interface' => [],
      'trait'     => [],
      'enum'      => [],
      'class'     => []
    ];
    foreach ($this->package->getClassNames() as $class) {
      $mirror= new TypeMirror($class);
      $kind= $mirror->kind()->name();
      $order[$kind][]= $mirror->modifiers()->names().' '.$kind.' '.$class;
    }
    foreach ($order as $type => $classes) {
      if (empty($classes)) continue;

      $out->writeLine();
      sort($classes);
      foreach ($classes as $name) {
        $out->writeLine('  ', $name);
      }
    }

    $out->writeLine('}');
  }
}