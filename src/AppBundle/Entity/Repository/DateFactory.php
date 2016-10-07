<?php

namespace AppBundle\Entity\Repository;


class DateFactory
{

    /**
     * @param $str
     * @return \DateTime
     */
    public function fromString($str)
    {
        return \DateTime::createFromFormat(\DateTime::ISO8601, $str);
    }

    /**
     * @param \DateTime $date
     * @return string
     */
    public function toString(\DateTime $date = null)
    {
        if ($date === null) {
            return '';
        }
        return $date->format(\DateTime::ISO8601);
    }

    /**
     * @return false|string
     */
    public function nowString()
    {
        return date(\DateTime::ISO8601);
    }

}
