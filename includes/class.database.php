<?php
class Database extends SQLite3
{    
    function __construct()
    {
         $this->open(APP_DIR . DBPATH);
         $this->init();
    }
    
    function init()
    {
        
        // Should we check if it exists first ? If used MySQL we wouldn't be doing this in the first place.
        // I'm using sqllite and doing this just to make it as easy as possible to run this ... no sql configurations
        // and things like that.
        
        $schema = "CREATE TABLE IF NOT EXISTS hq_payments (
            id               INTEGER PRIMARY KEY AUTOINCREMENT,
            full_name         TEXT    NOT NULL,
            cctype            TEXT    NOT NULL,
            ccnumber          INTEGER NOT NULL,
            ccexpire          TEXT    NOT NULL,
            cccvv             INTEGER,
            price             REAL,
            currency          TEXT,
            gateway           TEXT,
            verified          INTEGER,
            payid           INTEGER
        );";
        
        $ret = $this->exec($schema);
        return $ret;
    }
    
    function checkIfExist()
    {
        if (file_exists(APP_DIR . DBPATH) && is_writeable(APP_DIR . DBPATH))
        {
            return true;
        }
        return false;
    }
    
}

   
   
?>