<?php

namespace SampleApplication;

require_once dirname(__FILE__) . '/vendor/autoload.php';

$dumpDir = 'dump/';
$suffix = '.csv';
$arr = array('tz_Category', 'tz_CategoryProductRel', 'tz_Product');

echo "Setup...<br/>";

try {
    $db = DBSettings::getInstance();

    $sql = "CREATE TABLE IF NOT EXISTS `tz_Category` (
                `id` int(10) unsigned NOT NULL,
                `lleft` int(10) unsigned NOT NULL,
                `rright` int(10) unsigned NOT NULL,
                `level` int(10) unsigned NOT NULL,
                `name` varchar(255) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    $rc = $db->query($sql);
    if (!$rc)    throw new \Exception("Error Create table with SQL: " . $sql, 1);

    $sql = "ALTER TABLE `tz_Category`
                ADD PRIMARY KEY (`id`), ADD KEY `lleft` (`lleft`), ADD KEY `rright` (`rright`), ADD KEY `level` (`level`)";
    $rc = $db->query($sql);
    if (!$rc)    throw new \Exception("Error alter table with SQL: " . $sql, 1);
    echo "Category table created.<br/>";

    $sql = "CREATE TABLE IF NOT EXISTS `tz_CategoryProductRel` (
                `c_id` int(10) unsigned NOT NULL,
                `p_id` int(10) unsigned NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    $rc = $db->query($sql);
    if (!$rc)    throw new \Exception("Error Create table with SQL: " . $sql, 1);

    $sql = "ALTER TABLE `tz_CategoryProductRel` ADD UNIQUE KEY `c_id` (`c_id`,`p_id`)";
    $rc = $db->query($sql);
    if (!$rc)    throw new \Exception("Error alter table with SQL: " . $sql, 1);
    echo "CategoryProductRel table created.<br/>";

    $sql = "CREATE TABLE IF NOT EXISTS `tz_Product` (
                `id` int(10) unsigned NOT NULL,
                `name` varchar(255) NOT NULL,
                `isAvailable` tinyint(3) unsigned NOT NULL DEFAULT '0',
                `price` decimal(10,2) NOT NULL DEFAULT '0.00',
                `brandName` varchar(255) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    $rc = $db->query($sql);
    if (!$rc)    throw new \Exception("Error Create table with SQL: " . $sql, 1);

    $sql = "ALTER TABLE `tz_Product`
                ADD PRIMARY KEY (`id`), ADD KEY `name` (`name`), ADD KEY `brandName` (`brandName`)";
    $rc = $db->query($sql);
    if (!$rc)    throw new \Exception("Error alter table with SQL: " . $sql, 1);
    echo "Product table created.<br/><br/>";


    foreach ($arr as $file)
    {
        echo "Processing file: ".$file.".<br/>";
        $i = 0;
        $f = fopen($dumpDir.$file.$suffix, "r");
        while (($arr = fgetcsv($f, 4096, ";")) !== false)
        {
            if (!count($arr))    coninue;

            $sql = "INSERT INTO ?n VALUES (?a)";
            $rc = $db->query($sql, $file, $arr);
            if (!$rc)    throw new \Exception("Insert Error with SQL: " . $sql, 1);
            $i++;
        }
        fclose($f);
        echo $i . " rows inserted.<br/>";
    }
} catch (\Exception $e)  {
    echo $e->getMessage();
    die;
}

echo "<br/><br/>All Done.<br/><br/><a href='.'>Click</a> for start Application";

?>
