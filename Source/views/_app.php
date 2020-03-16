<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <!--<meta name="viewport" content="width=device-width, initial-scale=1.0">-->
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel = "stylesheet" type = "text/css" href = '<?= url("Source/assets/scss/app.css") ?>'>
    <title><?= SITE_NAME ?></title>
</head>
<body>
    <nav class = "sidebar">
        <?php 
            if (isset($_SESSION) && !empty($_SESSION)) {
                $v->insert("sidebar");    
            }
        ?>      
    </nav>
    
    <section class = 'main-content'>
        <?= $v->section("content"); ?>
    </section>

    <script type = "text/javascript" src = '<?= url("Source/assets/js/jquery.js"); ?>'></script>
    <script type = "text/javascript" src = '<?= url("Source/assets/js/app.js");?>'></script>
    <?php 
        if ($v->section("sidebar-script")) {
            echo $v->section("sidebar-script");
        }

        if ($v->section("script")) {
            echo $v->section("script");
        }
    ?>
</body>
</html>