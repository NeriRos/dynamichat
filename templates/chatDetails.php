<div class="chatDetailsForm">
    <form action="/wp-json/chat/v1/details" method="POST" onsubmit="return openChatClient(event)">
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
        <div class="row">
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
            <div class="col text-right">
                <button class="btn btn-outline-light chatDetailsSubmit" name="chat" type="submit">שלח</button>
            </div>
        </div>
    </form>
</div>
<script>
   function OpenWindowWithPost(url, windowoption, name, params)
   {
		var form = document.createElement("form");
		form.setAttribute("method", "post");
		form.setAttribute("action", url);
		form.setAttribute("target", name);

		for (var i in params) {
			if (params.hasOwnProperty(i)) {
				var input = document.createElement('input');
				input.type = 'hidden';
				input.name = i;
				input.value = params[i];
				form.appendChild(input);
			}
		}
		
		document.body.appendChild(form);
		
		//note I am using a post.htm page since I did not want to make double request to the page 
	   //it might have some Page_Load call which might screw things up.
		window.open("post.htm", name, windowoption);
		
		form.submit();
		
		document.body.removeChild(form);
    }

   function openChatClient(e)
   {
        e.preventDefault();
        
        var xhttp = new XMLHttpRequest();

        var user = {
            'name': document.querySelector('#chat-name').value,
            'business': document.querySelector('#chat-business').value,
            'phone': document.querySelector('#chat-phone').value,
            'id' : ''
        };

        xhttp.open("POST", "/wp-json/chat/v1/details", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.onreadystatechange = function()
        {
            if(xhttp.readyState == 4 && xhttp.status == 200 && xhttp.responseText)
            {
                user.id = xhttp.responseText;
                OpenWindowWithPost("/wp-content/plugins/dynamichat/templates/chatClient.php", "width=500,height=400,left=100,top=100,resizable=yes,scrollbars=yes", "DynamiChat-Client", user);
            }
        }
        xhttp.send(JSON.stringify(user));	    		
        
        return false;
    }
</script>
<style>
    :root {
        --main-color: coral; 
    }
	.chatDetailsInput {
		margin-bottom: 10px !important;
		border: solid 1px var(--main-color) !important;
	}
	.chatDetailsInput, .chatDetailsInput::placeholder {
		font: 14pt Calibri;
		color: var(--main-color) !important;
		text-align: right;
	}
	.chatDetailsHeader {
		font: 20pt Calibri !important;
		font-size: 24pt !important;
		color: var(--main-color) !important;
		text-align: right;
	}
	.chatDetailsSubmit {
		font-size: 14pt !important;
		border: solid 1px var(--main-color) !important;
		text-align: center !important;
	}
	
</style>