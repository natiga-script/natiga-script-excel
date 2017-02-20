<?php

if(!class_exists('Natiga_Security')){
    require_once('class.security.php');
}

/**
 * This class is responsible for retrieving student scores.
 * 
 * @author Abood Nour
 * @copyright 2017
 * @uses Natiga_Security to escape and secure certain parameters
 * @version 1.00
 * */
class Natiga_Fetcher
{

    private $statement;
    private $studentInfo;
    private $grade;
    private $search_config;
    const SEARCH_BY_NUM = 2;
    const SEARCH_BY_NAME = 4;
    
    /**
     * Class constructor
     * 
     * @param string|int $studentInfo
     *  Student Info to use in search, can be either student name or ID (e.g. seat number)
     * @param string $grade
     *  Grade name to use in search
     * @param array $config
     *  (optional) Search configuration array
     * 
     * @return Natiga_Fetcher
     *  Returns an instance of the class
     * */
    function __construct($studentInfo, $grade, $config = array())
    {

        $this->studentInfo = trim($studentInfo);
        $this->grade = trim($grade);
        //Default Search Config
        $this->search_config = array();
        $this->search_config['allow_partial_names'] = true;
        $this->search_config['allow_partial_grades'] = true;
        $this->search_config['allow_search_all_grades'] = true;
        $this->search_config['search_by'] = self::SEARCH_BY_NUM | self::SEARCH_BY_NAME; //Search by both
        $this->set_search_config_array($config);
    }
    
    /**
     * Set search configuration 
     * 
     * @param array $config
     *  Set of search options
     * 
     * @return Natiga_Fetcher
     *  returns current instance of this class
     * */

    function set_search_config_array($config)
    {
        if (!empty($config) && is_array($config))
        {
            $this->search_config = array_merge($this->search_config, $config);
        }
        return $this;
    }
    
    /**
     * Set search configuration
     * 
     * @param string $key
     *  Search option key
     * @param mixed $val
     *  Search option value
     * 
     * @return Natiga_Fetcher
     *  returns current instance of this class
     * */

    function set_search_config($key, $val)
    {
        $this->search_config[$key] = $val;
        return $this;
    }
    
    /**
     * Get search configuration
     * 
     * @param string $key
     *  Search option key
     * 
     * @return mixed
     *  returns the value of search configuration
     * */

    function get_search_config($key)
    {

        return (in_array($key, $this->search_config)) ? $this->search_config[$key] : null;
    }
    
    /**
     * Retrieve search results (student score sheet)
     *
     * @uses Natiga_Fetcher::$studentInfo, Natiga_Fetcher::$grade to look up student
     * @uses Natiga_Security::escapeLike() to escape grade name if partial names are allowed
     *  
     * @return PDOStatement|bool
     *  returns instance of PDOStatment if query succeeded or false if it didn't 
     * */

    function get_result()
    {
        global $PDO;

        $this->statement = false; //default return value

        //select proper where clauses based on provided info
        $info = $this->parse_info();

        //Check if student info is not empty and (grade is not empty or owner allowed searching in all grades)
        if ($info['value'] !== null && (!empty($this->grade) || $this->get_search_config('allow_search_all_grades') == true))
        {
            $grade_condition = (empty($this->grade) && $this->get_search_config('allow_search_all_grades') == true) ? '' : ' AND `grade` like ?';
            $preparedSQL = sprintf('SELECT * FROM `' . DB_PREFIX . 'sheet_schema` WHERE `%s` %s ? %s', $info['field_name'], $info['condition'], $grade_condition);
            $replacements = array($info['value']);
            if ($grade_condition !== '')
            {
                $grade = Natiga_Security::escapeLike($this->grade);
                $replacements[] = ($this->get_search_config('allow_partial_grades')) ? "%" . $grade . "%" : $grade;
            }
            try
            {
                $this->statement = $PDO->prepare($preparedSQL);
                $this->statement->execute($replacements);

            }
            catch (exception $e)
            {
                //var_dump($e);
            }
        }

        return $this->statement;
    }
    
    /**
     * Retrieve last search results
     * 
     * @return PDOStatement|bool|null
     *  returns instance of PDOStatment if query succeeded,
     *          false if it didn't,
     *          null if search hasn't been inititated 
     * */
    
    function get_statement()
    {
        return $this->statement;
    }

    /**
     * Determine search parameters to use
     * based on student info provided and search configuration
     *  
     * @return array
     *  returns information to use in search (field name, comparison operator, what to search for)
     *  array keys respectively are, (field_name, condition, value)
     * */
    
    function parse_info()
    {
        $info = array(
            'field_name' => '',
            'condition' => '',
            'value' => null);
        if ($this->get_search_config('search_by') === self::SEARCH_BY_NUM)
        {
            $info = $this->parse_as_num();
        } elseif ($this->get_search_config('search_by') === self::SEARCH_BY_NAME)
        {
            $info = $this->parse_as_name();
        } else
        {
            if (is_numeric($this->studentInfo))
            {
                $info = $this->parse_as_num();
            } else
            {
                $info = $this->parse_as_name();
            }
        }
        //var_dump($info);
        return $info;
    }
    
    /**
     * Forces parsing search parameter (studentinfo) as student ID (number)
     * 
     * @uses Natiga_Fetcher::$studentInfo to parse info
     * @uses Natiga_Security::toInt() to force parsing info as absolute integers
     * 
     * @return array
     *  returns information to use in search (field name, comparison operator, what to search for)
     *  array keys respectively are, (field_name, condition, value)
     * */

    function parse_as_num()
    {
        $info = array(
            'field_name' => 'num',
            'condition' => '=',
            'value' => Natiga_Security::toInt($this->studentInfo));
        return $info;
    }


    /**
     * Forces parsing search parameter (studentinfo) as student name
     * 
     * @uses Natiga_Fetcher::$studentInfo to parse info
     * @uses Natiga_Security::escapeRegEx() to escape regular expression
     * 
     * @return array
     *  returns information to use in search (field name, comparison operator, what to search for)
     *  array keys respectively are, (field_name, condition, value)
     * */

    function parse_as_name()
    {
        $info = array(
            'field_name' => 'name',
            'condition' => 'rlike',
            'value' => null);

        //Prepare student info
        //remove control chars and space chars
        //$info['value'] = trim($this->studentInfo, "\x00..\x20 \t\r\n"); //no longer necessary
        $info['value'] = $this->studentInfo;
        //escape user input for regex and create our own
        $info['value'] = (!empty($info['value'])) ? self::prepare_arabic_regex(Natiga_Security::escapeRegEx($info['value'])) : null;
        //If owner has chosen not to permit searching with partial names, then prevent it
        $info['value'] = ($info['value'] !== null && $this->get_search_config('allow_partial_names') === false) ? sprintf('^%s$', $info['value']) : $info['value'];

        return $info;
    }
    
    /**
     * Transform arabic names into a RegEx to compensate for common misspelling of names
     * 
     * @return string
     *  returns regular expression to use in search
     * */


    public static function prepare_arabic_regex($str)
    {
        $misspelled = array();
        $misspelled[] = array(
            'ا',
            'أ',
            'إ',
            'آ');
        $misspelled[] = array(
            'ى',
            'ئ',
            'ي');
        $misspelled[] = array('ه', 'ة');
        $misspelled[] = array('و', 'ؤ');
        $final_regex = $str;
        foreach ($misspelled as $regBase)
        {
            $regex = sprintf('(%s)', implode('|', $regBase));
            $final_regex = preg_replace('#' . $regex . '#', $regex, $final_regex);
        }
        //return $final_regex;
        return preg_replace('#\s+#', '[[:space:]]*', $final_regex); //count for extra spaces eg. عبد الرحمن can also find عبدالرحمن
    }
}

?>