<?php

class sfAssetsBuildMakefileTask extends sfBaseTask
{
    /**
     * @see sfTask
     */
    protected function configure()
    {
        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', 'frontend'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
        ));

        $this->namespace = 'assets';
        $this->name = 'build-makefile';
    }


    /**
     * Executes the task.
     */
    protected function execute($arguments = array(), $options = array())
    {
        $packages = sfConfig::get('app_sf_assets_packages');
        $makefile = __DIR__."/../../config/makefile.mk";
        $webDir = sfConfig::get('sf_web_dir').'/css/';

        $targetDir = sfConfig::get('app_sf_assets_compile_dir');
        if (!$targetDir) {
            throw new Exception("Expected target dir `app_sf_assets_compile_dir` for app `{$options['application']}`");
        }
        $targetDir = sfConfig::get('sf_web_dir') . '/' . ltrim($targetDir, '/');

        $targets = array();
        $deps = array();
        foreach ($packages as $targetName => $files) {
            $targetFile = $targetDir.'/'.$targetName;
            $deps[] = sprintf("%s: %s", $targetFile, $webDir.implode(' '.$webDir, $files));
            $targets[] = $targetFile;
        }
        $targets = implode(' ', $targets);
        $deps = implode(PHP_EOL, $deps);

        $template = <<< EOD
# Target dir
{$targetDir}:
\tmkdir -p $@

# Targets
asset_css_targets := {$targets}

# Deps
{$deps}

# Compile
$(asset_css_targets): {$targetDir}
\tcat $(filter %.css,$^) > $@
EOD;

        file_put_contents($makefile, $template);
    }
}
