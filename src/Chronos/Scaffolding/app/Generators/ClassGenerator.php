<?php

namespace Chronos\Scaffolding\Generators;

abstract class ClassGenerator extends Generator
{
    /**
     * Class name of generated class
     *
     * @var string
     */
    private $class_name = '';

    /**
     * Namespace of generated class
     *
     * @var string
     */
    private $namespace = '';

    /**
     * Uses classes
     *
     * @var array
     */
    private $uses = [];



    /**
     * Adds uses
     *
     * @param $class_name
     */
    public function addUses($uses)
    {
        $this->uses[] = $uses;
    }

    /**
     * Populate the stub file
     *
     * @return string
     */
    public function generateFile()
    {
        parent::generateFile();

        if (count($this->uses) > 0) {
            sort($this->uses);
            $uses = '';
            foreach ($this->uses as $use) {
                $uses .= 'use ' . $use . ';' . $this->getNewlineCharacter();
            }
            $this->file_content = str_replace('{{ uses }}', $uses, $this->file_content);
        } else {
            $this->file_content = str_replace('{{ uses }}', '', $this->file_content);
        }
        $this->file_content = str_replace('{{ className }}', $this->class_name, $this->file_content);

        return $this->file_content;
    }

    /**
     * Getter for class name
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->class_name;
    }

    /**
     * Getter for namespace
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Getter for uses
     *
     * @return string
     */
    public function getUses()
    {
        return $this->uses;
    }

    /**
     * Check if uses exists
     *
     * @param $use
     * @return boolean
     */
    public function hasUses($use)
    {
        return in_array($use, $this->uses);
    }

    /**
     * Setter for class name
     *
     * @param $class_name
     */
    public function setClassName($class_name)
    {
        $this->class_name = $class_name;
    }

    /**
     * Setter for namespace
     *
     * @param $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }
}