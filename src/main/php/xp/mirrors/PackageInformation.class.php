<?php namespace xp\mirrors;

use lang\ClassLoader;
use lang\mirrors\Package;

class PackageInformation extends CollectionInformation {
  private $package;

  /**
   * Creates a new type information instance
   *
   * @param  string|lang.mirrors.Package $package
   */
  public function __construct($package) {
    $this->package= $package instanceof Package ? $package : new Package($package);
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
    $this->displayCollection($this->package, ClassLoader::getDefault(), $out);
    $out->writeLine('}');
  }
}