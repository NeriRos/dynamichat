<?php
    $plugin_path = '/wp-content' . explode( 'wp-content', str_replace( '\\', '/', __DIR__ ) )[1];
    $assets = explode( 'templates', $plugin_path )[0] . 'assets';
?>

<link rel="stylesheet" href="<?php echo $assets ?>/frontend/style.css">
<link rel="stylesheet" href="<?php echo $assets ?>/frontend/libs/bootstrap.min.css">


<div class="chatDetailsForm">
    <form action="/wp-json/chat/v1/details" method="POST" onsubmit="return openSupport(event, '<?php echo $plugin_path . '/'; ?>')">
        <div class="row">
            <div class="col">
                <h2 class="text-right chatDetailsHeader">השאירו פרטים<br/><br/></h2>
            </div>
        </div>
        <div class="row">
            <div class="col form-group">
                <input id="chat-name" autocomplete="name" name="name" class="form-control chatDetailsInput" type="text" placeholder="שם מלא">
            </div>
        </div>
        <div class="row chat-business-name-row">
            <div class="col form-group">
                <input id="chat-business" name="business" class="form-control chatDetailsInput" type="text" placeholder="שם בית עסק">
            </div>
        </div>
        <div class="row">
            <div class="col form-group">
                <input id="chat-phone" autocomplete="tel" name="phone" class="form-control chatDetailsInput" type="tel" placeholder="טלפון">
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                <span><input name="is-business" class="chat-is-business chatDetailsInput" value="false" type="radio" onclick="businessInputToggle(false)"></span><label>:פרטי</label>
            </div>
            <div class="col-4">
                <span><input name="is-business" class="chat-is-business chatDetailsInput" value="true" checked="checked" type="radio" onclick="businessInputToggle(true)"></span><label>:עסקי</label>
            </div>
            <div class="col-4">
                <label>:סוג לקוח</label>
            </div>
        </div>

        <div class="row">
            <div class="col text-right">
                <button class="btn chatDetailsSubmit" name="chat" type="submit">שלח</button>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript" src="<?php echo $assets ?>/frontend/chatDetails.js"></script>
