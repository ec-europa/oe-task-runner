<?php

namespace EC\OpenEuropa\TaskRunner\Tasks\ProcessConfigFile;

use EC\OpenEuropa\TaskRunner\Traits\ConfigurationTokensTrait;
use Robo\Common\BuilderAwareTrait;
use Robo\Contract\BuilderAwareInterface;
use Robo\Task\BaseTask;
use Robo\Task\File\Replace;
use Robo\Task\Filesystem\FilesystemStack;

/**
 * Class ProcessConfigFile
 *
 * @package EC\OpenEuropa\TaskRunner\Tasks\ProcessConfigFile
 */
class ProcessConfigFile extends BaseTask implements BuilderAwareInterface
{
    use BuilderAwareTrait;
    use ConfigurationTokensTrait;

    /**
     * Source file.
     *
     * @var string
     */
    protected $source;

    /**
     * Destination file.
     *
     * @var string
     */
    protected $destination;

    /**
     * @var \Robo\Task\Filesystem\FilesystemStack
     */
    protected $filesystem;

    /**
     * @var \Robo\Task\File\Replace
     */
    protected $replace;

    /**
     * ProcessConfigFile constructor.
     *
     * @param string $source
     * @param string $destination
     */
    public function __construct($source, $destination)
    {
        $this->source = $source;
        $this->destination = $destination;
        $this->filesystem = new FilesystemStack();
        $this->replace = new Replace($destination);
    }

    /**
     * @return \Robo\Result
     */
    public function run()
    {
        $content = file_get_contents($this->source);
        $tokens = $this->extractProcessedTokens($content);

        return $this->collectionBuilder()->addTaskList([
            $this->filesystem->copy($this->source, $this->destination, true),
            $this->replace->from(array_keys($tokens))->to(array_values($tokens)),
        ])->run();
    }
}