<?php

namespace AppBundle\Entity;

class Directory
{
    protected $directory;

    public function getdirectory()
    {
        return $this->directory;
    }

    public function setDirectory($directory)
    {
        $this->directory = $directory;
    }

}