
function OpenWindowWithPost(url, windowoption, name, params)
{
    var form = document.createElement("form");
    form.setAttribute("method", "post");
    form.setAttribute("action", url);
    form.setAttribute("target", name);

    if (Object.keys(params).length == 1) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = Object.keys(params)[0];
        input.value = JSON.stringify(params[Object.keys(params)[0]]);
        form.appendChild(input);
    } else {
        for (var i in params) {
            if (params.hasOwnProperty(i)) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = i;
                input.value = params[i];
                form.appendChild(input);
            }
        }
    }

    document.body.appendChild(form);

    //note I am using a post.htm page since I did not want to make double request to the page
    //it might have some Page_Load call which might screw things up.
    window.open("post.htm", name, windowoption);

    form.submit();

    document.body.removeChild(form);
}

/**
 * send client details to backend and receive support back.
 * open chatClient popup with support.
 * @param {Event} e button click event
 * @param {string} path of plugin templates directory
 */
function openSupport(e, path)
{
    e.preventDefault();

    var user = {
        'name': document.querySelector('#chat-name').value,
        'business': document.querySelector('#chat-business').value,
        'phone': document.querySelector('#chat-phone').value
    };

    ajax("POST", "/wp-json/chat/v1/openSupport", user, (res, err) => {
        if (err) {
            console.log("error:", err.responseText);
        }
        var support = JSON.parse(res);

        if (support.support) {
            OpenWindowWithPost(path + "chatClient.php", "width=600,height=500,left=100,top=100,resizable=yes,scrollbars=yes", "DynamiChat-Client", support);
        } else {
            alert("There is no available representative..");
        }
    });

    return false;
}

function businessInputToggle(show) {
    document.querySelector(".chat-business-name-row").style.display = show ? "flex" : "none";
}

function ajax(method, url, data, cb) {
    var xhttp = new XMLHttpRequest();

    xhttp.open(method, (url.startsWith('/') ? url : "/wp-json/"+url), true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.onreadystatechange = () => {
        if(xhttp.readyState == 4 && xhttp.status == 200)
        {
            if (cb)
                cb(xhttp.responseText);

        } else if (xhttp.readyState == 4) {
            cb(null, xhttp);
        }
    }
    xhttp.send(data && method == "POST" ? (typeof data === "string" ? data : JSON.stringify(data)) : "");
}
