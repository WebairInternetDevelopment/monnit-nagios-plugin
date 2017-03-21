monnitor - obtain current read values from Monnit sensors
Example usage:
         php monnitor.php --single 012345
         php monnitor.php --average "012345 012346 012347 012348"


-a/--average <argument>
     Returns average of temperatures from sensors. Optionally, a list of sensor IDs may be provided.
     They must be surrounded by quotes. Example: "[sensorID 1] [sensorID 2] [sensorID n]"


--help
     Show the help page for this command.


-s/--single <argument>
     Return current reading value of a single sensor.
