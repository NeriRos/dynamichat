<?php
    $plugin_path = '/wp-content' . explode( 'wp-content', str_replace( '\\', '/', __DIR__ ) )[1];
    $assets = explode( 'templates', $plugin_path )[0] . 'assets';
?>
<link rel="stylesheet" href="<?php echo $assets ?>/frontend/style.css">
<!-- <link rel="stylesheet" href="<?php // echo $assets ?>/frontend/libs/font-awesome.css"> -->
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">
<link rel="stylesheet" href="<?php echo $assets ?>/frontend/libs/bootstrap.min.css">

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="jumbotron dynamichat_container">
            <div id="dynamichat">
                <div class="row justify-content-center">
                    <div class="dynamichat_header">
                    <h2 id="chat_header"></h2>
                    <div id="chat_header_description">
                        <span>Rep name: </span>
                        <span id="repName"></span>
                    </div>
                    <hr>
                </div>
            </div>
            <div class="row">
                <div class="dynamichat_messages col">
                    <div class="row message_row" id="first_msg">
                        <div class="message col">
                            <div class="row">
                                <div class="col-2">
                                    <img src="#" height="50" width="50" class="message_picture" alt="user_picture">
                                </div>
                                <div class="col-6 message_body_container">
                                    <div class="message_body">
                                        <label class="message_text"></label>
                                        <br/>
                                        <label class="message_date"></label>
                                        <label class="message_status"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <label id="waiting" class="text-muted">waiting a representative to join..</label>
                </div>
            </div>
            <div class="row">
                <div class="dynamichat_inputs col">
                    <hr>
                    <div class="row">
                        <div class="col-10">
                            <input type="text" class="form-control" id="message" />
                        </div>
                        <div class="col-2">
                            <button class="btn btn-primary" onclick="sendMessage()">Send</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo $assets ?>/frontend/connection.js"></script>
<script type="text/javascript" src="<?php echo $assets ?>/frontend/socketMessageManager.js"></script>
<script type="text/javascript" src="<?php echo $assets ?>/frontend/chatClient.js"></script>
<script>
    var support = {client: {}};

    <?php
        if( isset( $_POST['support'] ) ) {
            echo 'window.support = ' . $_POST['support'] . ';';
        } else {
            echo 'console.log("no post data");';
            die;
        }
    ?>
</script>