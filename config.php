<?php
#####################################################
# File: Config.php                                  #
# Author: Mr.David_Jones@hotmail.co.uk              #
# Purpose: Config file for currency conversion API  #
# Version: 1.0                                      #
#####################################################

#Define Timezone
@date_default_timezone_set("GMT");

#Define URL & file constants.
define('RATES_URL', "http://www.floatrates.com/daily/gbp.xml");
define('COUNTRIES_FILE', "countries.xml");

#Error hash & error messages.
$errors = [
  1000 => 'Currency type not recognized',
	1100 => 'Required parameter is missing',
	1200 => 'Parameter not recognized',
	1300 => 'Currency amount mustbe a decimal number',
	1400 => 'Error in service',
	2000 => 'Method not recognized or is missing',
	2100 => 'Rate in wrong format or is missing',
	2200 => 'Currency code in wrong format or is missing',
	2300 => 'Country name in wrong format or is missing',
	2400 => 'Currency code not found for update',
	2500 => 'Error in service'
]
?>
