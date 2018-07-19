<?php
namespace SampleApplication;

/**
    * Simple View
*/
class view
{
    public function __construct()
    {
    }

    /**
        * Print out template file
        * @param mixed Application data
    */
    public function show($data)
    {
        include 'tmpl/tmpl.php';
    }

    /**
        * Print out JSON string
        * @param mixed Application data
    */
    public function showJSON($data)
    {
        echo json_encode($data);
    }
}
?>
