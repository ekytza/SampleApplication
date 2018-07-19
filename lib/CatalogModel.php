<?php
namespace SampleApplication;

trait DBSettings
{
    /**
        * @var array Database connection settings
    */
    protected static $dsn = array(
        'user'    => 'bullseye',
        'pass'    => 'cca.ru',
        'db'      => 'pokrov'
    );

    /**
        * @var string Database table prefix
    */
    protected static $tablePrefix = 'tz_';

    /**
        * @var mixed Database connection pointer
    */
    protected static $dbConn = null;

    /**
         * Database settings getter
         *
         * @param string option name
         * @return string|null
    */
    public static function getDBSettings($var)
    {
        if (isset(static::$$var))    return static::$$var;
        else return null;
    }

    /**
         * Database connection singleton
         *
         * @return mixed Database connection pointer
    */
    public static function getInstance()
    {
        return (static::$dbConn) ? static::$dbConn : static::$dbConn = new SafeMySQL(static::$dsn);
    }
}

interface CatalogModelInterface
{
    /**
         * Constructor
         *
         * @param int Primary key for instance
    */
    public function __construct($id = null);

    /**
         * Getter method
         *
         * @param string route name
         * @param mixed value
         * @return array
    */
    public function get($method, $value);
}

abstract class CatalogModel implements CatalogModelInterface
{
    use DBSettings;

    /**
        * @var mixed Database connection pointer
     */
    protected $db;

    /**
        * @var string Database table name
     */
    protected $table;

    /**
        * @var array Instance fields
     */
    protected $fields;

    /**
        * @var null|int Primary key ID
     */
    protected $id;

    /**
         * Constructor
         *
         * @param int Primary key for instance
    */
    function __construct($id = null)
    {
        $this->db = DBSettings::getInstance();
        $this->table = DBSettings::getDBSettings('tablePrefix') . get_class($this);
        if ($id != null)
        {
            $this->setFields($id);
        }
    }

    /**
         * Instance of object getter
         *
         * @param int Primary key for instance
    */
    protected function setFields($id)
    {
        try {
            $this->fields = $this->db->getRow('SELECT * FROM ?n WHERE id = ?i', $this->table, $id);
        } catch (\Exception $e) {
            echo json_encode(array('error' => $e->getMessage()));
        }
    }

    /**
         * Getter method
         *
         * @param string route name
         * @param mixed value
         * @return array
    */
    public function get($method, $value)
    {
        $arr = array();
        switch ($method)
        {
            case "id":
                $this->setFields($value);
                $arr = $this->fields;
                break;
            case "match":
            case "cat":
            case "brand":
                $arr = $this->getList($method, $value);
                break;
            default:
                $arr = array("error" => "Unknown method: ".$method);
                break;
        }
        if (empty($arr))    return array("error" => "No matches found");
        return $arr;
    }

    /**
         * Get list of objects
         *
         * @param string route name
         * @param mixed value
         * @return array
    */
    private function getList($method, $value)
    {
        $arr = array();
        switch ($method)
        {
            case "brand":
                $sql = "SELECT * FROM ?n WHERE brandName=?s";
                break;
            case "cat":
                $sql = "SELECT * FROM ?n WHERE id IN (?a)";
                break;
            case "match":
                $value = "%$value%";
                $sql = "SELECT * FROM ?n WHERE name LIKE ?s";
                break;
        }
        $rc = $this->db->query($sql, $this->table, $value);

        while ($row = $this->db->fetch($rc))
        {
            array_push($arr, $row);
        }
        return $arr;
    }

    /**
         * List of brands method
         *
         * @return array
    */
    public function getBrandList()
    {
        $arr = array();
        $sql = "SELECT DISTINCT brandName FROM ?n ORDER BY brandName";
        $rc = $this->db->query($sql, $this->table);

        while ($row = $this->db->fetch($rc))
        {
            array_push($arr, $row);
        }
        return $arr;
    }
}

/**
     * Simple Product
     *
*/
class Product extends CatalogModel
{
    public function __construct($id = null)
    {
        parent::__construct($id);
    }
}

/*
    * Nested Sets as category engine sample inplementation
*/
class Category extends CatalogModel
{
    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    /**
         * Get Root Node method
         *
         * @return int
    */
    function getRoot()
    {
        $sql = "SELECT id FROM ?n WHERE level=0 LIMIT 1";
        return $this->db->getOne($sql, $this->table);
    }

    /**
         * Get childs of a current node
         *
         * @param int Node primary key
         * @return array
    */
    public function getTree($id = 1)
    {
        $tree = array();
        $this->setFields($id);

        $sql = "SELECT *, FLOOR((rright-lleft-1)/2) as children  FROM ?n WHERE lleft BETWEEN  ?i AND  ?i  AND level = ?i ORDER BY lleft";

        $rc = $this->db->query($sql, $this->table, $this->fields['lleft'], $this->fields['rright'], $this->fields['level']+1);

        while ($row = $this->db->fetch($rc))
        {
            array_push($tree, $row);
        }
        return $tree;
    }
}

/**
     * Product-Category multiple relations
     *
*/
class CategoryProductRel extends CatalogModel
{
    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    /**
         * Get relations for object
         *
         * @param string object type
         * @param int object prinary key
         * @return array
    */
    public function getRel($method, $value)
    {
        switch ($method)
        {
            case "cat":
                $sql = "SELECT p_id FROM ?n WHERE c_id=?i";
                $list = $this->db->getCol($sql, $this->table, $value);
                break;
        }
        return $list;
    }

    /**
         * Get relations for object and all children of him
         *
         * @param array list of nodes
         * @return array
    */
    public function getRelAll($value)
    {
        $arr = array();
        foreach ($value as $k => $v)
        {
            $arr[] = $v['id'];
        }
        $sql = "SELECT p_id FROM ?n WHERE c_id IN (?a)";
        return $this->db->getCol($sql, $this->table, $arr);
    }
}

?>
