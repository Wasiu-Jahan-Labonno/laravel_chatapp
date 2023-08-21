var express = require('express');
var app = express();
var http = require('http');
var server = http.createServer(app);
var io = require('socket.io')(server, {
    cors: {
        origin: "*",
        "Access-Control-Allow-Origin": "*",
        credentials: true,
    }
});

server.listen(3000, () => {
    console.log("Server started");
});

app.get("/", (req, res) => {
    res.json({ msg: "Hello" })
})
var mysql = require('mysql');
var moment = require('moment');

var sockets = {};

var mysql = require('mysql');
const { disconnect } = require('process');
var connection = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'chat'
});

connection.connect(function(err) {
    if (err)
        throw err;
    console.log("Database Connected")
});

io.on('connection', function(socket) {
    if (!sockets[socket.handshake.query.user_id]) {
        sockets[socket.handshake.query.user_id] = [];
    }
    socket.broadcast.emit('user_connected', socket.handshake.query.user_id);
    connection.query(`UPDATE users SET is_online=1 where id=${socket.handshake.query.user_id}`, function(err, res) {
        if (err) {
            throw err;
        }
        console.log("user Connected", socket.handshake.query.user_id)
    });

    socket.on('sent_message', function(data) {
        var group_id = (data.user_id > data.other_user_id) ? data.user_id + data.other_user_id : data.user_id + data.user_id;
        var time = moment().format("h:mm A");
        data.time = time;
        for (var index in socket[data.user_id]) {
            sockets[data.user_id][index].emit('receive_message', data);
        }
        for (var index in socket[data.other_user_id]) {
            sockets[data.other_user_id][index].emit('receive_message', data);
        }
        connection.query(`INSERT INTO chats (user_id,other_user_id,message,group_id) values (${data.user_id},${data.other_user_id},"${data.message}",${group_id})`, function(err, res) {
            if (err) {
                throw err;
            }
            console.log("message sent");
        })
    })
    socket.on('disconnect', function(err) {
        socket.broadcast.emit('user_disconnected', socket.handshake.query.user_id);
        for (var index in sockets[socket.handshake.query.user_id]) {
            if (socket.id == sockets[socket.handshake.query.user_id][index].id) {
                sockets[socket.handshake.query.user_id].splice(index, 1);
            }
        }
        connection.query(`UPDATE users SET is_online=0 where id=${socket.handshake.query.user_id}`, function(err, req) {
            if (err) {
                throw err;
            };
            console.log("user disConnected", socket.handshake.query.user_id)
        });
    });
});