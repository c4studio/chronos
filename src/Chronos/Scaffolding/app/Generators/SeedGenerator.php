<?php

namespace Chronos\Scaffolding\Generators;

class SeedGenerator extends ClassGenerator
{
    /**
     * Name of stub file
     *
     * @var string
     */
    protected $stub_file = 'seeder.stub';



    public function __construct()
    {
        $this->addUses('Illuminate\Database\Seeder');
    }
}