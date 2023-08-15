var http = require('http').Server();
var io = require('socket.io')(http);
const Redis = require("ioredis");
const redis = new Redis({
    host: "redis",
    port: 6379
});

redis.subscribe("create-post", (err, count) => {
    if (err) {
        // Just like other commands, subscribe() can fail for some reasons,
        // ex network issues.
        console.error("Failed to subscribe: %s", err.message);
    } else {
        // `count` represents the number of channels this client are currently subscribed to.
        console.log(
            `Subscribed successfully! This client is currently subscribed to ${count} channels.`
        );
    }
});

redis.subscribe("update-post", (err, count) => {
    if (err) {
        // Just like other commands, subscribe() can fail for some reasons,
        // ex network issues.
        console.error("Failed to subscribe: %s", err.message);
    } else {
        // `count` represents the number of channels this client are currently subscribed to.
        console.log(
            `Subscribed successfully! This client is currently subscribed to ${count} channels.`
        );
    }
});

redis.subscribe("delete-post", (err, count) => {
    if (err) {
        // Just like other commands, subscribe() can fail for some reasons,
        // ex network issues.
        console.error("Failed to subscribe: %s", err.message);
    } else {
        // `count` represents the number of channels this client are currently subscribed to.
        console.log(
            `Subscribed successfully! This client is currently subscribed to ${count} channels.`
        );
    }
});

redis.on("message", (channel, message) => {
    console.log(`Received ${message} from ${channel}`);
    io.emit(channel, message.data);
});

// There's also an event called 'messageBuffer', which is the same as 'message' except
// it returns buffers instead of strings.
// It's useful when the messages are binary data.
redis.on("messageBuffer", (channel, message) => {
    // Both `channel` and `message` are buffers.
    console.log(channel, message);
});

http.listen(3000, () => {
    console.log('listening on *:3000');
})
