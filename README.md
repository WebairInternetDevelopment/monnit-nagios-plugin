Monnitor - a simple Nagios plugin for Monnit temperature sensors.

Requirements: PHP5.3+, Composer

Setup: Run "composer install" to install dependencies, rename .env.example to .env and add your Monnit API key to it.

Script usage:

The program uses sensor ID's to get temperature information and the "warning" and "critical" arguments as thresholds for comparison.
Example usage:
php monnitor.php --single 012345 -w 65 -c 75

php monnitor.php --average "012345 012346 012347 012348" -w 65 -c 75

-a/--average <argument>

Returns average of temperatures from sensors. Optionally, a list of sensor IDs may be provided. They must be
surrounded by quotes. Example: "[sensorID 1] [sensorID 2] [sensorID n]"

-c/--critical <argument>

Critical threshold- if this value is exceeded a CRIT resopnse will be issued to Nagios.

--help

Show the help page for this command.

-s/--single <argument>

Return current reading value of a single sensor.

-w/--warning <argument>

Warning threshold- if this value is exceeded a WARN response will be issued to Nagios.


Licensing

This software is under Apache2 licensing.
