<?php
$conn = new mysqli("mysql", "bozgi", "hujgnuj", "db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}