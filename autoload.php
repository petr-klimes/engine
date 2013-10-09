<?php
/**
 * Automaticky nacitani trid
 */
function __autoload($className)
{
        //class directories
        $directorys = array(
            'modules/',
            'modules/items/',
            'controlers/',
            'controlers/items/'
        );
        
        //for each directory
        foreach($directorys as $directory)
        {
            //see if the file exsists
            if(file_exists($directory.$className . '.php'))
            {
                require_once($directory.$className . '.php');
                //only require the class once, so quit after to save effort (if you got more, then name them something else 
                return;
            }            
        }
}
?>