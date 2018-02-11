<?php

namespace OpenEuropa\TaskRunner\Commands;

use EC\OpenEuropa\TaskRunner\Contract\ComposerAwareInterface;
use EC\OpenEuropa\TaskRunner\Traits\ComposerAwareTrait;
use Robo\Common\ConfigAwareTrait;
use Robo\Common\IO;
use Robo\Contract\ConfigAwareInterface;
use Robo\Contract\IOAwareInterface;
use Robo\Contract\BuilderAwareInterface;
use Robo\Exception\TaskException;
use Robo\LoadAllTasks;
use Robo\Robo;
use Symfony\Component\Console\Event\ConsoleCommandEvent;

/**
 * Class AbstractCommands
 *
 * @package OpenEuropa\TaskRunner\Commands
 */
abstract class AbstractCommands implements BuilderAwareInterface, IOAwareInterface, ComposerAwareInterface, ConfigAwareInterface
{
    use ComposerAwareTrait;
    use ConfigAwareTrait;
    use LoadAllTasks;
    use IO;

    /**
     * Path to YAML configuration file containing command defaults.
     *
     * Command classes should implement this method.
     *
     * @return string
     */
    public function getConfigurationFile()
    {
        return __DIR__.'/../../config/commands/base.yml';
    }

    /**
     * Command initialization.
     *
     * @param \Symfony\Component\Console\Event\ConsoleCommandEvent $event
     *
     * @hook pre-command-event *
     */
    public function initializeRuntimeConfiguration(ConsoleCommandEvent $event)
    {
        Robo::loadConfiguration([$this->getConfigurationFile()], $this->getConfig());
    }

    /**
     * @param  string $name
     * @return string
     *
     * @throws TaskException
     */
    protected function getBin($name)
    {
        $filename = $this->getConfig()->get('runner.bin_dir').'/'.$name;
        if (!file_exists($filename) && !$this->isSimulating()) {
            throw new TaskException($this, "Executable '{$filename}' not found.");
        }

        return $filename;
    }

    /**
     * Set runtime "runner.bin_dir" configuration value.
     *
     * @param \Symfony\Component\Console\Event\ConsoleCommandEvent $event
     *
     * @hook command-event *
     */
    public function setRuntimeBinDir(ConsoleCommandEvent $event)
    {
        if ($this->getConfig()->get('runner.bin_dir') === null) {
            if ($composerBinDir = $this->getComposer()->getConfig('bin-dir')) {
                if (strpos($composerBinDir, './') === FALSE) {
                    $composerBinDir = './' . $composerBinDir;
                }
                $composerBinDir = rtrim($composerBinDir, DIRECTORY_SEPARATOR);
            }
            else {
                $composerBinDir = './vendor/bin';
            }
            $this->getConfig()->set('runner.bin_dir', $composerBinDir);
        }
    }

    /**
     * @return bool
     */
    protected function isSimulating()
    {
        return (bool) $this->input()->getOption('simulate');
    }
}
