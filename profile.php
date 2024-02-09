<?php
require("func/conn.php");
require_once("func/settings.php");
require("func/site/user.php");
require("func/site/comment.php");
require("func/site/friend.php");

// Fetch user information
$userInfo = fetchUserInfo($_GET['id']);
$user = $userInfo ? $userInfo['username'] : '';
$userId = $userInfo['id'];
$id = $userId;


$userInterests = $userInfo['interests'];
$interests = json_decode($userInterests, true);

// Fetch blogs and friends using the user's username
$blogs = fetchUserBlogs($conn, $user);

$friends = array_merge(
    fetchFriends($conn, 'ACCEPTED', 'receiver', $userId),
    fetchFriends($conn, 'ACCEPTED', 'sender', $userId)
);
// Fetch comments
$limitedComments = fetchComments($id, 20);
$countComments = count($limitedComments);
$countTotalComments = count(fetchComments($id));

?>

<!DOCTYPE html>
<html>

<head>
    <title><?= $user ?>'s Profile | <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="static/css/header.css">
    <link rel="stylesheet" href="static/css/base.css">
    <link rel="stylesheet" href="static/css/my.css">

    <!-- USER STYLES -->
    <?php
    $stmt = $conn->prepare("SELECT * FROM `users` WHERE id = :id");
    $stmt->execute(array(':id' => $_GET['id']));

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<style>" . $row['css'] . "</style>";
    }
    ?>
</head>

<body>
    <div class="master-container">
        <?php
        require("navbar.php");
        ?>
        <main>
            <div class="row profile" itemscope itemtype="https://schema.org/Person">
                <meta itemprop="url"
                    content="https://<?= htmlspecialchars(DOMAIN_NAME); ?>profile.php?id=<?= htmlspecialchars($userInfo['username']); ?>">
                <meta itemprop="identifier" content="<?= htmlspecialchars($userInfo['username']); ?>">
                <div class="col w-40 left">
                    <span itemprop="name">
                        <?php if ($userInfo): ?>
                            <br><br>
                            <h1 style='margin: 0px;'>
                                <?= htmlspecialchars($userInfo['username']); ?>
                            </h1>
                        </span>
                        <div class="general-about">
                            <div class="profile-pic">
                                <img class='pfp-fallback' width='235px;'
                                    src='pfp/<?= htmlspecialchars($userInfo['pfp']); ?>'>
                            </div>
                            <div class="details">
                                <p class="online"><img src="static/img/green_person.png" aria-hidden="true"
                                        alt="Online icon" loading="lazy"> ONLINE!</p>
                            </div>
                        </div>
                        <audio controls autoplay>
                            <source src="music/<?= htmlspecialchars($userInfo['music']); ?>" type="audio/ogg">
                        </audio>
                        <div class="mood">
                            <p>
                                <b>Mood: </b>
                                <?= htmlspecialchars($userInfo['status']); ?>
                            </p>
                            <p>
                                <b>View my:
                                    <a href="#">Blog</a>
                                </b>
                            </p>
                        </div>
                        <div class="contact">
                            <div class="heading">
                                <h4>Contacting
                                    <?= htmlspecialchars($userInfo['username']); ?>
                                </h4>
                            </div>
                            <div class="inner">
                                <div class="f-row">
                                    <div class="f-col">
                                        <a href="friends.php?action=add&id=<?= htmlspecialchars($userInfo['id']); ?>"
                                            rel="nofollow">
                                            <img src="static/icons/add.png" class="icon" aria-hidden="true" loading="lazy"
                                                alt=""> Add to Friends
                                        </a>
                                    </div>
                                    <div class="f-col">
                                        <a href="#" rel="nofollow">
                                            <img src="static/icons/award_star_add.png" class="icon" aria-hidden="true"
                                                loading="lazy" alt=""> Add to Favorites
                                        </a>
                                    </div>
                                </div>
                                <div class="f-row">
                                    <div class="f-col">
                                        <a href="#" rel="nofollow">
                                            <img src="static/icons/comment.png" class="icon" aria-hidden="true"
                                                loading="lazy" alt=""> Send Message
                                        </a>
                                    </div>
                                    <div class="f-col">
                                        <a href="#" rel="nofollow">
                                            <img src="static/icons/arrow_right.png" class="icon" aria-hidden="true"
                                                loading="lazy" alt=""> Forward to Friend
                                        </a>
                                    </div>
                                </div>
                                <div class="f-row">
                                    <div class="f-col">
                                        <a href="#" rel="nofollow">
                                            <img src="static/icons/email.png" class="icon" aria-hidden="true" loading="lazy"
                                                alt=""> Instant Message
                                        </a>
                                    </div>
                                    <div class="f-col">
                                        <a href="#" rel="nofollow">
                                            <img src="static/icons/exclamation.png" class="icon" aria-hidden="true"
                                                loading="lazy" alt=""> Block User
                                        </a>
                                    </div>
                                </div>
                                <div class="f-row">
                                    <div class="f-col">
                                        <a href="#">
                                            <img src="static/icons/group_add.png" class="icon" aria-hidden="true"
                                                loading="lazy" alt=""> Add to Group
                                        </a>
                                    </div>
                                    <div class="f-col">
                                        <a href="#" rel="nofollow">
                                            <img src="static/icons/flag_red.png" class="icon" aria-hidden="true"
                                                loading="lazy" alt=""> Report User
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="url-info">
                            <p><b>
                                    <?= htmlspecialchars(SITE_NAME); ?> URL:
                                </b></p>
                            <p>
                                https://
                                <?= htmlspecialchars(DOMAIN_NAME); ?>/profile.php?id=<?= htmlspecialchars($userInfo['id']); ?>
                            </p>
                        </div>
                        <div class="table-section">
                            <div class="heading">
                                <h4>
                                    <?= htmlspecialchars($userInfo['username']); ?>'s Interests
                                </h4>
                            </div>
                            <div class="inner">
                                <table class="details-table" cellspacing="3" cellpadding="3">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <p>General</p>
                                            </td>
                                            <td>
                                                <p>
                                                    <?= htmlspecialchars($interests['General']); ?>
                                                </p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p>Music</p>
                                            </td>
                                            <td>
                                                <p>
                                                    <?= htmlspecialchars($interests['Music']); ?>
                                                </p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p>Movies</p>
                                            </td>
                                            <td>
                                                <p>
                                                    <?= htmlspecialchars($interests['Movies']); ?>
                                                </p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p>Television</p>
                                            </td>
                                            <td>
                                                <p>
                                                    <?= htmlspecialchars($interests['Television']); ?>
                                                </p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p>Books</p>
                                            </td>
                                            <td>
                                                <p>
                                                    <?= htmlspecialchars($interests['Books']); ?>
                                                </p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p>
                                                    Heroes
                                                </p>
                                            </td>
                                            <td>
                                                <p>
                                                    <?= htmlspecialchars($interests['Heroes']); ?>
                                                </p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p>User not found.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col right">
                    <div class="blog-preview">
                        <h4>
                            <?= htmlspecialchars($userInfo['username']); ?>'s Latest Blog Entries [<a href="#">View
                                Blog</a>]
                        </h4>
                        <p><i>There are no Blog Entries yet.</i></p>
                    </div>
                    <div class="blurbs">
                        <div class="heading">
                            <h4>
                                <?= htmlspecialchars($userInfo['username']); ?>'s Bio
                            </h4>
                        </div>
                        <div class="inner">
                            <div class="section">
                                <p itemprop="description">
                                    <?= htmlspecialchars($userInfo['bio']); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="friends">
                        <div class="heading">
                            <h4>
                                <?= htmlspecialchars($userInfo['username']); ?>'s Friend Space
                            </h4>
                            <a class="more" href="friends.php?id=<?= $userId ?>">[view all]</a>
                        </div>
                        <div class="inner">
                            <p><b>
                                    <?= htmlspecialchars($userInfo['username']); ?> has <span class="count">
                                        <?= htmlspecialchars(count($friends)); ?>
                                    </span> friends.
                                </b></p>
                            <div class="friends-grid">
                                <?php
                                foreach ($friends as $friend) {
                                    $friendId = $id === $friend['sender'] ? $friend['receiver'] : $friend['sender'];

                                    if ($friendId == $id) {
                                        continue;
                                    }
                                    $friendName = fetchName($friendId);
                                    $friendPfp = fetchPFP($friendId);

                                    echo "<div class='person'><a href='profile.php?id=" . htmlspecialchars($friendId) . "'><center><b>" . htmlspecialchars($friendName) . "</b></center><br><img width='125px' src='pfp/" . htmlspecialchars($friendPfp) . "'></a></div>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="friends" id="comments">
                        <div class="heading">
                            <h4>
                                <?= htmlspecialchars($userInfo['username']); ?>'s Friends Comments
                            </h4>
                        </div>
                        <div class="inner">
                            <p>
                                <b>
                                    Displaying <span class="count"><?= $countComments ?></span> of <span class="count"><?= $countTotalComments ?></span> comments
                                    ( <a href="comments.php?id=<?= $userInfo['id'] ?>">View all</a> | <a
                                        href="addcomment.php?id=<?= $userInfo['id'] ?>">Add
                                        Comment</a> )
                                </b>
                            </p>
                            <table class="comments-table" cellspacing="0" cellpadding="3" bordercolor="ffffff"
                                border="1">
                                <tbody>
                                    <?php foreach ($limitedComments as $comment): ?>
                                        <tr>
                                            <td>
                                                <a href="profile.php?id=<?= htmlspecialchars($comment['author']) ?>">
                                                    <p>
                                                        <?= htmlspecialchars(fetchName($comment['author'])) ?>
                                                    </p>
                                                </a>
                                                <a href="profile.php?id=<?= htmlspecialchars($comment['author']) ?>">
                                                    <?php
                                                    $pfpPath = fetchPFP($comment['author']);
                                                    $pfpPath = $pfpPath ? $pfpPath : 'default.png';
                                                    ?>
                                                    <img class="pfp-fallback" src="pfp/<?= $pfpPath ?>"
                                                        alt="<?= htmlspecialchars(fetchName($comment['author'])) ?>'s profile picture"
                                                        loading="lazy" width="50px">
                                                </a>
                                            </td>
                                            <td>
                                                <p><b><time class="">
                                                            <?= time_elapsed_string($comment['date']) ?>
                                                        </time></b></p>
                                                <p>
                                                    <?= htmlspecialchars($comment['text']) ?>
                                                </p>
                                                <br>
                                                <p class="report">
                                                    <a href="/report?type=comment&id=<?= htmlspecialchars($comment['id']) ?>"
                                                        rel="nofollow">
                                                        <img src="https://static.spacehey.net/icons/flag_red.png"
                                                            class="icon" aria-hidden="true" loading="lazy" alt=""> Report
                                                        Comment
                                                    </a>
                                                </p>
                                                <a
                                                    href="/addcomment?id=<?= $toid ?>&reply=<?= htmlspecialchars($comment['id']) ?>">
                                                    <button>Add Reply</button>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

            </div>
        </main>
</body>

</html>