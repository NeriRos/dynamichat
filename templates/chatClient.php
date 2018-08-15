<?php
    use Inc\Pages\ChatClient;
    $chatClients = new ChatClient();
    $chats = $chatClients->get_chats();
?>

<script>
    function send_message() {
        console.log("Sending message");
        var xhttp = new XMLHttpRequest();
        var message = document.querySelector('#message').value;

        xhttp.open("POST", "/wp-json/chat/v1/new_message", true);
        xhttp.setRequestHeader("Content-type", "application/json");
        xhttp.onreadystatechange = function()
        {
            if(xhttp.readyState == 4 && xhttp.status == 200)
            {
                alert(xhttp.responseText);
            }
        }
        xhttp.send(JSON.stringify({message: message}));
    }
</script>

<div class="wrap">
    <div class="dynamichat">
        <div class="dynamichat_header">
            <h2><?php get_option( 'chat_title' ) ?></h2>
        </div>
        <div class="dynamichat_messages">
            <?php 
            foreach ($chats as $chat) 
            {
                ?>
                <div class="row">
                    <div class="message col <?php echo $chat['isSenderSelf'] ? 'text-right' : 'text-left' ?>">
                        <div class="row">
                            <div class="col-2" style="display: <?php echo $chat['isSenderSelf'] ? 'none' : 'block' ?>">
                                <img src="<?php echo $chat['picture'] ?>" alt="user_photo">
                            </div>
                            <div class="col">
                                <div class="message_body">
                                    <label class="message_text"><?php echo $chat['text'] ?></label>
                                    <br/>
                                    <label class="message_date"><?php echo $chat['date'] ?></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php    
            }
            ?>
        </div>
        <div class="dynamichat_inputs">
            <input type="text" class="form-control" id="message"/>
            <button onclick="send_message()">Send</button>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">