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
    // customer create a new request
    socket.on("customer_send_new_request", () => {
        io.emit("provider_refresh_request_new");
    });
    // provider send request price
    socket.on("provider_send_price", () => {
        io.emit("customer_refresh_request_new");
    });
    // provider cancel request
    socket.on("provider_cancel_request", () => {
        io.emit("customer_refresh_request_canceled");
    });
    // customer cancel request
    socket.on("customer_cancel_request", () => {
        io.emit("provider_refresh_request_canceled");
    });
    // customer accept request's price
    socket.on("customer_accept_price", () => {
        io.emit("provider_refresh_request_appointed");
    });
    // provider complete appointment
    socket.on("provider_complete_appointment", () => {
        io.emit("customer_refresh_request_appointed");
    });
    // customer confirmed done appointment
    socket.on("customer_confirmed_done", () => {
        io.emit("provider_refresh_request_done");
    });
    socket.on("disconnect", () => {});
});

// create server using socket io?
