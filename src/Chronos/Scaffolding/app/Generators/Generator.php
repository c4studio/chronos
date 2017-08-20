<?php

namespace Chronos\Scaffolding\Generators;

abstract class Generator
{
    /**
     * The content to be placed in the generated file
     *
     * @var string
     */
    private $content = '';

    /**
     * Desired indent for the code
     * For tabulator use \t
     * Double quotes are mandatory!
     *
     * @var string
     */
    private $indent_character = "    ";

    /**
     * Newline character for seed files
     * Double quotes are mandatory!
     *
     * @var string
     */
    private $newline_character = PHP_EOL;

    /**
     * Content of the generated file
     *
     * @var string
     */
    protected $file_content = '';

    /**
     * Name of the generated file
     *
     * @var string
     */
    private $file_name = '';

    /**
     * Path for stub files
     *
     * @var string
     */
    private $stub_path = __DIR__ . '/../../resources/stubs';

    /**
     * Name of stub file
     *
     * @var string
     */
    protected $stub_file;



    /**
     * Adds indentation to the content
     *
     * @param string    $content
     * @param int       $numberOfIndents
     */
    public function addIndent($no_indents = 1)
    {
        while ($no_indents > 0) {
            $this->content .= $this->indent_character;
            $no_indents--;
        }
    }

    /**
     * Adds text to the content
     *
     * @param $text
     */
    public function addContent($text)
    {
        $this->content .= $text;
    }

    /**
     * Adds new lines to the content
     *
     * @param string    $content
     * @param int       $numberOfLines
     */
    public function addNewLines($no_lines = 1)
    {
        while ($no_lines > 0) {
            $this->content .= $this->newline_character;
            $no_lines--;
        }
    }

    public function downloadFile()
    {
        header('Content-Disposition: attachment; filename="' . $this->file_name . '"');
        header('Content-Type: text/plain');
        header('Content-Length: ' . strlen($this->file_content));
        header('Connection: close');

        echo $this->file_content;
    }

    /**
     * Populate the stub file
     * 
     * @return string
     */
    public function generateFile()
    {
        $this->file_content = file_get_contents($this->stub_path . '/' . $this->stub_file);
        $this->file_content = str_replace('{{ content }}', $this->content, $this->file_content);

        return $this->file_content;
    }

    /**
     * Getter for indent character
     *
     * @return string
     */
    public function getIndentCharacter()
    {
        return $this->indent_character;
    }

    /**
     * Getter for newline character
     *
     * @return string
     */
    public function getNewlineCharacter()
    {
        return $this->newline_character;
    }

    /**
     * Setter for file name
     *
     * @param $file_name
     */
    public function setFilename($file_name)
    {
        $this->file_name = $file_name;
    }
}