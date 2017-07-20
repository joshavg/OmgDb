<?php

namespace AppBundle\Service;


class CsvParser
{

    public function parseFile(string $path)
    {
        return array_map('str_getcsv', file($path));
    }

}
