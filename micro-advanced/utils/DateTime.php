<?php
/**
 * 
 * Advanced microFramework
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * 
 * @copyright Copyright (c) 2019 - 2020 Advanced microFramework
 * @author Advanced microFramework Team (Denzel Code, Soull Darknezz)
 * @link https://github.com/DenzelCode/Advanced
 * 
 */

namespace advanced\utils;

use DateTimeZone;
use advanced\Bootstrap;

/**
 * DateTime class
 */
class DateTime extends \DateTime {

    /**
     * @var string
     */
    public $format = "m-d-Y H:i:s";

    /**
     * @param string $time
     * @param DateTimeZone $timezone
     */
    public function __construct(string $time = "now", DateTimeZone $timezone = null) {
        parent::__construct($time, $timezone);
    }

    /**
     * @return string
     */
    public function getFormat() : string {
        return $this->format;
    }

    /**
     * @param string $format
     * @return void
     */
    public function setFormat(string $format) {
        $this->format = $format;
    }

    /**
     * Get how much time ago passed since this date.
     *
     * @return string
     */
    public function getAgo() : string {
        $time = (new DateTime())->getTimestamp() - $this->getTimestamp();

        $lang = Bootstrap::getLanguage(false)->get("time.ago");

        if ($time < 1) return $lang["just_now"];

        $string = [365 * 24 * 60 * 60 => "year", 30 * 24 * 60 * 60 => "month", 24 * 60 * 60 => "day", 60 * 60 => "hour", 60 => "minute", 1 => "second"];

        foreach ($string as $seconds => $str) {
            $d = $time / $seconds;

            if ($d >= 1) {
                $r = round($d);

                return $r . " " . ($r > 1 ? $lang["plural"][$str] : $lang["singular"][$str]) . " " . $lang["ago"];
            }
        }
    }
}