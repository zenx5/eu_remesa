<?php
    $template = $_GET['template'];
    $type = $_GET['type'];
    $scripts = $_GET['script'];
    $token =  $_GET['token'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/vuetify@3.3.2/dist/vuetify.min.css" rel="stylesheet"></link>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vuetify@3.3.2/dist/vuetify.min.js"></script>
    <style>
        body{
            padding:20px;
            background-color:white;
        }
    </style>
    <script>
        sessionStorage.setItem('eu_token',"<?=$token?>");
    </script>
</head>
<body>
    <div id="app" style="background-color: white;">
        <?php include 'templates/'.$template.'.'.$type; ?>
    </div>
    <?php foreach(explode(',',$scripts) as $script):?>
    <script>
        <?php include 'js/'.$script.'.js'; ?>
    </script>
    <?php endforeach;?>
</body>
</html>