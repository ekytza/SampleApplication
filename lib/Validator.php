<?php
namespace SampleApplication;
/*
    * Simple Validator
*/
trait Validator
{
    /**
        * @var array Valid params array
    */
    protected $validParams = array(
        "cmd" => array("expand", "viewbrand", "viewcat", "viewall", "getitem", "match"),
        "id" => "int",
        "brand" => "string"
    );

    /**
        * @var string Valid method
    */
    protected $validcmd = "index";

    /**
        * @var int Valid Id
    */
    protected $validid = null;

    /**
        * @var string Valid Brand name
    */
    protected $validbrand = null;

    /**
        * @var string Valid context search query string
    */
    protected $validq = null;

    /**
         *Validator method
         *
         * @return array (bool|string result, string message)
    */
    public function Validate()
    {
        if (empty($_GET))    return array($this->validcmd, "OK");

        foreach ($_GET as $k => $v)
        {
            $v = $this->stripInput($v);
            switch ($k)
            {
                case "cmd":
                    if (!in_array($v, $this->validParams['cmd']))    return array(false, $k . ':' . $v);
                    $this->validcmd = $v;
                    break;
                case "id":
                    $id = intval($v);
                    if ($id == 0)    return array(false, $k . ':' . $v);
                    $this->validid = $id;
                    break;
                case "q":
                    if (strlen($v) < 3)    return array(false, $k . ':' . $v);
                    $this->validq = $v;
                    break;
                case "brand":
                    if (!strlen($v))    return array(false, $k . ':' . $v);
                    $this->validbrand = $v;
                    break;
                default:
                    return array(false, $k . ':' . $v);
                    break;
            }
        }
        return array($this->validcmd, "OK");
    }

    /**
         *Strip bad symbols method
         *
         * @param string value of GET variable
         * @return string text
    */
    protected function stripInput($text)
    {
        $quotes = array ("\x27", "\x22", "\x60", "\t", "\n", "\r", "*", "%", "<", ">", "?", "!" );
        $goodquotes = array ("-", "+", "#" );
        $repquotes = array ("\-", "\+", "\#" );
        $text = trim(strip_tags($text));
        $text = str_replace($quotes, '',$text);
        $text = str_replace( $goodquotes, $repquotes, $text);

        return $text;
    }

}
?>
