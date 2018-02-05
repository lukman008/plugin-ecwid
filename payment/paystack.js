const express = require('express');
const bodyParser = require('body-parser');

const app = express();
const port = process.env.PORT || 3000;

app.listen(port);
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: false }));

console.log(`todo list RESTful API server started on: ${port}`);
