<?php
require("func/conn.php");
require_once("func/settings.php");
function addComment($toid, $authorId, $text) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO comments (toid, author, text, date) VALUES (:toid, :author, :text, NOW())");
    $success = $stmt->execute(array(':toid' => $toid, ':author' => $authorId, ':text' => $text));
    return $success;
}

function fetchComments($toid, $limit=null)
{
    global $conn;
    $query = "SELECT * FROM `comments` WHERE toid = :toid ORDER BY id DESC";
    if ($limit !== null) {
        $query .= " LIMIT :limit";
    }

    $stmt = $conn->prepare($query);

    $stmt->bindParam(':toid', $toid);
    if ($limit !== null) {
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>