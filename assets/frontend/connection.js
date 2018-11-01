class Connection {
    constructor(socket, user) {
        this.socket = socket;
        this.user = user;
        this.nodeConnectionId = -1;
        this.phpConnectionId = -1;
    }

    sendServerMessage(message, event) {
        if(this.socket.readyState === 1) {
            Connection.sendSocketMessage(this.socket, message, event, this.user, this.phpConnectionId, this.nodeConnectionId);
        } else {
            console.log("Socket is not open..", this.socket.readyState);
        }
    }

    sendServerInit(support) {
        if(this.socket.readyState === 1) {
            Connection.sendSocketMessage(this.socket, {support}, window.SOCKET_EVENTS.SUPPORT_INIT, this.user, this.phpConnectionId, this.nodeConnectionId);
        } else {
            console.log("Socket is not open..", this.socket.readyState);
        }
    }

    setConnectionIds(phpConnectionId, nodeConnectionId) {
        this.nodeConnectionId = nodeConnectionId;
        this.phpConnectionId = phpConnectionId;
    }

    static sendSocketMessage(socket, data, event, user, phpConnectionId, nodeConnectionId) {
        data.event = event;
        data.user = user;
        data.nodeConnectionId = nodeConnectionId;
        data.phpConnectionId = phpConnectionId;

        socket.send(JSON.stringify(data));
    }
}