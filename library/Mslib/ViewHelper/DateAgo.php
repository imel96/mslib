<?php

namespace Mslib\ViewHelper;

use Zend\View;

class DateAgo extends View\Helper\AbstractHelper
{
    public function __invoke(\DateTime $date)
    {
        $today = clone $date;
        $today->setTimestamp(time())
            ->setTime(0, 0, 0);
        $tmp = clone $date;
        $tmp->setTime(0, 0, 0);
        $diff = $today->diff($tmp);

        switch ($diff->d) {
        case 0:
            $format = '\t\o\d\a\y';
            break;
        case 1:
            $format = '\y\e\s\t\e\r\d\a\y';
            break;
        case 2:
            $format = "2 \d\a\y\s \a\g\o";
            break;
        default:
            if ($diff->y)
                $format = 'd/m/Y H:i';
            else
                $format = 'j M \a\t H:i';
            break;
        }
        return $date->format($format);
    }
}
