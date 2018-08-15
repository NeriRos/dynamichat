<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>cool!</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <link rel="stylesheet" type="text/css" media="screen" href="main.css" /> -->
    <!-- <script src="main.js"></script> -->
</head>
<body>
    <div class="wrap">
        <h1>DynamiChat Settings</h1>
        <?php settings_errors(); ?>

        <form method="post" action="options.php">
            <?php 
                settings_fields( 'chat_options_group' );
                do_settings_sections( 'dynamichat_settings' );
                submit_button();
            ?>
        </form>
    </div>
</body>
</html>