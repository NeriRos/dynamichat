class SocketMessageManager {
    constructor(data) {
        this.data = data;
        this.events = data.event;

        if (typeof this.events !== "object")
            this.events = [this.events];

        this.events.forEach((event) => {
            console.log("Event:", event, "data:", data);

            this.sortMessage(data, event);
        });
    }

    sortMessage(data, event) {
        switch (event) {
            case window.SOCKET_EVENTS.SUPPORT_INIT:
                console.log("Connection ids - PHP:", data.phpConnectionId, "- Node:", data.nodeConnectionId);
                this.onSupportInit(data);
                break;

            case window.SOCKET_EVENTS.GET_REPRESENTATIVE:
                this.onGetRepresentative(data);
                break;

            case window.SOCKET_EVENTS.GET_CONNECTION_ID:
                console.log("Connection ids - php:", data.phpConnectionId, "- node:", data.nodeConnectionId);
                this.onGetConnectionId(data);
                break;

            case window.SOCKET_EVENTS.FIND_AVAILABLE_REP:
                this.onFindAvailableRepresentative(data);
                break;

            case window.SOCKET_EVENTS.CLIENT_MESSAGE:
                this.onClientMessage(data);
                break;

            case window.SOCKET_EVENTS.SUPPORT_MESSAGE:
                this.onSupportMessage(data);
                break;

            case window.SOCKET_EVENTS.MESSAGE_READ:
                this.onSupportMessageRead(data);
                break;
        }
    }

    /**
     * support init event from server.
     *
     * add php connection id to connection object for session id on server.
     * @param {phpConnectionId: number} data with session ids.
     */
    onSupportInit(data) {
        // TODO: add ids to connection object.
    }

    onSupportMessageRead(data) {
        // TODO: search for id and update
        var status = getStatus(data.chat.status);

        document.getElementById(data.chat._id).querySelector('.message_status').innerHTML = status;
    }

    onGetRepresentative(data) {
        toggleWaitingRep(false, data.representative.name);
    }

    onGetConnectionId(data) {
    }

    onFindAvailableRepresentative(data) {
        const representative = data.support.representative;
        window.support.representative = representative;

        if (data.chat)
            addMessages(data.chat, true, representative);

        toggleWaitingRep(false, representative.name);
    }

    onClientMessage(data) {
        var chat = data.chat;
        var chatId = chat.id - 1; // TODO: find why need to minus 1
        var status = getStatus(chat.status);

        window.chatIds[window.chatIds.indexOf(chatId)] = chat._id;

        window.allElementsAndChats.forEach((elementAndChat) => {
            if (elementAndChat.chat.chatId === chatId) {
                elementAndChat.element.querySelector('.message_status').innerHTML = status;
                elementAndChat.element.id = chat._id;
                elementAndChat.chat._id = chat._id;
                elementAndChat.chat.status = chat.status;

                return elementAndChat;
            }
        });
    }

    onSupportMessage(data) {
        var support = data.support;
        var message = data.chat;

        if (support && message) {
            addMessages(message, true, support.representative);

            window.connection.sendServerMessage({support, chat: message, user: window.user}, window.SOCKET_EVENTS.MESSAGE_READ);
        } else {
            console.log("MISSING DATA: support -", support, "message -", message, "Data:", data);
        }
    }
}

