<!DOCTYPE html>
<html>
<head>
    <title>Data Frame Error Page</title>
    <link href="/<?=$css?>/DataFrame/mahad/css/metro.css" rel="stylesheet">
    <link href="css/metro-icons.css" rel="stylesheet">
    <link href="css/metro-responsive.css" rel="stylesheet">
    <link href="css/metro-schemes.css" rel="stylesheet">
</head>
<body class="bg-darkTeal window warning">
    <div class="panel">
        <div class="heading" style="min-height:20px;height:200px;">
            <div class="title">AN ERROR OCCURED: <b><?=$err['message']?></b></div>
        </div>
        <div class="content">
        	<div class="margin10">
            <!--?php print_r($err)?-->
            <span class="button loading-cube lighten danger">In <b><?=$err['file']?></b></span>
            </div>
            
            <div class="margin10">
                <span class="button loading-pulse lighten primary">On LINE: <b><?=$err['line']?></b></span>
            </div>
        </div>
    </div>
</body>
</html>