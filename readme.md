# SimpleMySQL
  * Tired of scouring php.net for the function names to utilize PDO every time you need to
interact with MySQL? 
  * Frustrated with the amount of redundant code you have to write to build
  a result set that's easily usable by your application? (I mean all you want
  to do is `SELECT * FROM table WHERE col = ?` and display the results as html table rows...why
  are you now thinking about data types of parameters which you'll have to bind
  to a prepared statement...why are you wasting time?)
  * Stuck working outside modern php frameworks that include fancy ORMS?
  * Want simplicity in database interaction without the bloat of a full
  fledged ORM?
  * Are you a sane rational human being (or other form of entity which possesses 
  a rational thought pattern)?
  
  ### If you have answered `YES` to any of the above, `SimpleMySQL` might be right for you. Please consult with your senior developer today!
  
  
  ## Usage
  ```php
  use whitwhoa\SimpleMySQL\MySQLCon;
  
  /**
   * First parameter is an array of the following:
   *
   * [
   *      host, username, password, database
   * ]
   *
   * It is advised to store this information in a directory outside
   * of your webroot if using this package within a web application.
   * (for example create a file called connections.php containing multiple
   * array variables with connection details for various connections, include
   * the file at the top of your script and pass the variable containing the
   * connection information you wish to instantiate a MySQLCon object for.)
   *
   * If boolean true is passed as second parameter, error reporting will be turned on
   * (don't do this in production). Class could be updated to print more detailed errors.
   *
   */
  $db = new MySQLCon(['localhost', 'root', 'root', 'testDB'], true);
  
  // Obtain results as an array of stdClass objects
  $users = $db->query('SELECT * FROM users')->asObj()->exec();
  foreach($users as $u){
      echo $u->email . "\n";
  }
  
  // Obtain a single row as an stdClass object
  $user = $db->query("SELECT * FROM users WHERE id = ?", [7])->singleAsObj()->exec();
  echo $user->email;
  
  // Obtain results as an array of associative arrays
  $users = $db->query('SELECT * FROM users')->asArray()->exec();
  foreach($users as $u){
      echo $u['email'] . "\n";
  }
    
  // Obtain a single row as an associative array
  $user = $db->query("SELECT * FROM users WHERE id = ?", [7])->singleAsArray()->exec();
  echo $user['email'];
  
  // Insert a new record and obtain it's primary key value
  $id = $db->query("INSERT INTO test(name) VALUES(?)", ['Richard'])->exec();
  echo $id;
  
  // Update an existing record (returns void)
  $db->query("UPDATE test SET name = ? WHERE id = ?", ['Tom', 3])->exec();
  
  // Delete an existing record (returns void)
  $db->query("DELETE FROM test WHERE id = ?", [3])->exec();  
  ```
  
  
  
  ### If you experience any of the following symptoms please stop using SimpleMySQL and contact your senior developer immediately:
  * Data loss and or corruption
  * Poor performance
  * Random intermittent issues which no one can explain
  * Project managers now putting more pressure on you to get projects done faster since you've found a tool that allows you to be more efficient at your job
  * A burning sensation when you urinate
   
   