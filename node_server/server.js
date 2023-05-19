const express = require("express");
const cors = require("cors");

const app = express();
app.use(cors());
const io = require("socket.io")(5000, {
    cors: {
        origin: "*",
    },
});
// Socket
io.on("connection", (socket) => {
    socket.on("customer_send_new_request", () => {
        io.emit("provider_refresh_request");
    });
    socket.on("provider_send_price", () => {
        io.emit("customer_refresh_request");
    });
    socket.on("provider_cancel_request", () => {
        io.emit("customer_refresh_request");
    });
    socket.on("customer_cancel_request", () => {
        io.emit("provider_refresh_request");
    });
    socket.on("disconnect", () => {});
});

// create server using socket io?
