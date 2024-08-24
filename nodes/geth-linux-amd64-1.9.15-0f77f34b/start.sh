#!/bin/bash
rm nohup.out
./geth --ws --ws.port 9546 --ws.origins 127.0.0.1 --http --syncmode "light" --http.addr "0.0.0.0" --http.api "admin,personal,eth,net,web3" --http.api "admin,personal,eth,net,web3" --allow-insecure-unlock
