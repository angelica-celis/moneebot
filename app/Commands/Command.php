<?php

namespace App\Commands;

use App\Classes\DataStorage;
use App\Classes\UpdateParser;

abstract class Command
{
    public $name;

    protected $updateParser;
    protected $dataStorage;
    protected $user;

    public function __construct(UpdateParser $updateParser, DataStorage $dataStorage)
    {
        $this->updateParser = $updateParser;
        $this->dataStorage = $dataStorage;
        $this->user = $this->updateParser->getUser();
    }

    /**
     * Run the command.
     *
     * @return mixed
     */
    abstract public function fire();

    /**
     * Check if command should be run.
     *
     * @return bool
     */
    public function shouldFire()
    {
        return $this->name === $this->updateParser->getMessageText();
    }
}