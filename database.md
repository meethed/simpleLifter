CLICK ON "RAW" AND DISABLE WORD WRAP SO IT PRESENTS CORRECTLY

ok so the php code needs a mysql (or similar) database rather than just using JSON. This is a hangover from when it all started out in a slightly different direction.

The database must have a table called 'comps'. I mean, you can change it but that's just what I use for the SQL select statements. 

The 'comps' table is set up as follows:

+----------------+--------------+------+-----+---------------------+-------------------------------+
| Field          | Type         | Null | Key | Default             | Extra                         | My added description just now
+----------------+--------------+------+-----+---------------------+-------------------------------+
| compLetters    | char(3)      | YES  |     | NULL                |                               | The TLA - auto incremements so you MUST manually establish an "AAA" competition as index 0
| compName       | char(255)    | YES  |     | NULL                |                               | Competition name in plain text
| contact        | char(255)    | YES  |     | NULL                |                               | Contact details (for me to reach out)
| compID         | int(4)       | NO   | PRI | NULL                | auto_increment                | Comp ID is just a number, not sure if i use this TBH
| startdate      | date         | YES  |     | NULL                |                               | Comp start date - used to hide old comps 
| enddate        | date         | YES  |     | NULL                |                               | Comp end date - used to hide old comps
| leftLight      | tinyint(1)   | NO   |     | NULL                |                               | Left light status (0-off, 1-white, 2-red, 3-yellow, 4-blue, 5+ - red with no small light)
| centreLight    | tinyint(1)   | NO   |     | NULL                |                               | Centre light status (0-off, 1-white, 2-red, 3-yellow, 4-blue, 5+ - red with no small light)
| rightLight     | tinyint(1)   | NO   |     | NULL                |                               | Right light status (0-off, 1-white, 2-red, 3-yellow, 4-blue, 5+ - red with no small light)
| timeTo         | timestamp    | NO   |     | 0000-00-00 00:00:00 |                               | UTC target for the barloaded timer
| timeTwo        | timestamp    | NO   |     | 0000-00-00 00:00:00 |                               | UTC target for the second timer
| lifterName     | char(255)    | YES  |     | NULL                |                               | Current lifter name (for live stream only)
| currentAttempt | double(5,2)  | YES  |     | NULL                |                               | Current attempt (for live stream only)
| total          | double(6,2)  | YES  |     | NULL                |                               | Current lifter's total (for live stream only)
| compStatus     | tinyint(4)   | YES  |     | NULL                |                               | Competition status (1-9 for SQ1,2,3,BP1,2,3,DL1,2,3)
| lifterTeam     | varchar(255) | YES  |     | NULL                |                               | Current lifter's team (for live stream only) 
| updated        | timestamp    | NO   |     | current_timestamp() | on update current_timestamp() | Last updated timestamp to work with short polling
| lifterBW       | double(5,2)  | YES  |     | NULL                |                               | Current lifer bodyweight (for live stream only)
| lifterClass    | varchar(255) | YES  |     | NULL                |                               | Current lifter weight class (for live stream only)
| lifterCat      | varchar(255) | YES  |     | NULL                |                               | Current lifter division / category (for live stream only)
| lifterFlight   | char(1)      | YES  |     | NULL                |                               | Current lifter flight (for live stream only)
| nextLot        | int(11)      | YES  |     | NULL                |                               | Next lifter lot number (for platform display only)
| nextName       | char(255)    | YES  |     | NULL                |                               | Next lifter name (for platform display only)
| nextRack       | char(255)    | YES  |     | NULL                |                               | Next lifter rack heights (for platform display only)
| nextLoad       | double(5,2)  | YES  |     | NULL                |                               | Next lifter attempt (for platform display only)
| lot            | int(11)      | YES  |     | NULL                |                               | Current lifter lot number (for platform display only)
| rack           | char(16)     | YES  |     | NULL                |                               | current lifter rack heights (for platform display only)
| hish           | char(255)    | YES  |     | NULL                |                               | "Access code" hash (note it's not a secure password and it's not used for all transactions, it's only used to access the competition spreadsheet or referee buttons pages)
| bar            | double(5,2)  | YES  |     | NULL                |                               | Bar weight (for platform display only)
+----------------+--------------+------+-----+---------------------+-------------------------------+

Most of the live stream and platform display stuff should move into the simpleLifter JSON. It was originally done like this to support nextLifter excel and VBA 
Now that I never need to see or use nextLifter again, I'll make another job to transition this out of the database.

________________________________________________________________

Ok so the config.php (or config.inc) file is located in /var/www, with the web root starting at /var/www/html. This means it's visible to PHP but not to the internets.

The config.php needs to create a connection $conn, to your applicable database:

eg:

 /var/www/config.php (or /var/www/config.inc)
------------------------------------------

$conn = new mysqli("localhost", "username", "userpwd", "lightsdb");

------------------------------------------

that's it
