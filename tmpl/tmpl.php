<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" prefix="og: http://ogp.me/ns#">
<head>
    <title>Sample Application</title>
    <link href="css/style.css" media="screen" rel="stylesheet" type="text/css" />
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8" />

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <script type="text/javascript" src="js/stuff.js"></script>
    <script type="text/javascript" src="js/lz-string.min.js"></script>

    <script type="text/javascript">
    $(document).ready(function()
    {
        $(document).jsInit(
        {
            case01 : {name : 'all'}
        });
    });
    </script>
</head>
<body itemscope itemtype="http://schema.org/WebPage">
    <div class="first_column">
        <h3>Context search</h3>
        <div class="iSearch_cs">
            <div class="search_body">
                <input name="q" type="text" maxlength="128" autocomplete="off" value="Search by name" onblur="if (this.value=='') this.value=this.defaultValue;" onfocus="if (this.value==this.defaultValue) this.value='';" />
                <div class="search_list"></div>
            </div>
        </div>
        <h3>By Category view</h3>
        <ul class="catList">
            <?php
                foreach ($data['tree'] as $k => $v)
                {
                    if (empty($v['children']))
                    {
                        $plus = $allP = "&nbsp;&nbsp;&nbsp;";
                    }
                    else
                    {
                        $plus = "&nbsp;<a class='expand' href='?cmd=expand&id=".$v['id']."' title='Expand'>+</a>&nbsp;";
                        $allP = "&nbsp;(<a class='view' href='?cmd=viewall&id=".$v['id']."' title='View'>Все товары раздела</a>)&nbsp;";
                    }
                    echo "<li>".$plus."<a class='view' href='?cmd=viewcat&id=".$v['id']."' title='View'>".$v['name']."</a>$allP</li>";
                }
            ?>
        </ul>
        <h3>By brand view</h3>
        <ul class="brandList">
            <?php
                foreach ($data['brandList'] as $k => $v)
                {
                    echo "<li><a class='view' href='?cmd=viewbrand&brand=".$v['brandName']."' title='View'>".$v['brandName']."</a></li>";
                }
            ?>
        </ul>
    </div>
    <div class="second_column">
        <h1>Sample Application</h1>
        <p><a href=".">Home</a>
        <div id="content"></div>
    </div>
</body>
</html>
