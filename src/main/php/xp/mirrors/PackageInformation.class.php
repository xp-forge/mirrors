<?php namespace xp\mirrors;

use lang\ClassLoader;
use lang\mirrors\TypeMirror;
use lang\mirrors\Package;

class PackageInformation extends Information {
  private $package;

  /**
   * Creates a new type information instance
   *
   * @param  lang.mirrors.Package $package
   */
  public function __construct($package) {
    $this->package= $package;
  }

  /** @return php.Generator */
  public function sources() {
    $name= $this->package->name();
    foreach (ClassLoader::getLoaders() as $loader) {
      if ($loader->providesPackage($name)) yield $loader;
    }
  }

  /**
   * Display information
   *
   * @param  io.StringWriter $out
   * @return void
   */
  public function display($out) {
    $out->writeLine('package ', $this->package->name(), ' {');

    // Child packages
    $reflect= \lang\reflect\Package::forName($this->package->name());
    foreach ($reflect->getPackages() as $child) {
      $out->writeLine('  package ', $child->getName());
    }
    
    // Types
    $order= [
      'interface' => [],
      'trait'     => [],
      'enum'      => [],
      'class'     => []
    ];
    foreach ($reflect->getClassNames() as $class) {
      $mirror= new TypeMirror($class);
      $order[$mirror->kind()->name()][]= self::declarationOf($mirror);
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