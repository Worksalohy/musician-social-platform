<?php

function getUserById(PDO $pdo, int $userId)
{
    $sql = "
        SELECT 
            id,
            username,
            email,
            instrument,
            created_at,
            avatar
        FROM users
        WHERE id = :id
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        "id" => $userId
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}


function getUserMusicStyles(PDO $pdo, int $userId): array
{
    $sql = "
        SELECT ms.name
        FROM user_music_styles ums
        INNER JOIN music_styles ms
            ON ums.style_id = ms.id
        WHERE ums.user_id = ?
        ORDER BY ms.name
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $userId
    ]);

    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}


function isFollowing(PDO $pdo, int $followerId, int $followingId): bool
{
    $sql = "
        SELECT 1
        FROM follows
        WHERE follower_id = ?
        AND following_id = ?
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $followerId,
        $followingId
    ]);

    return (bool) $stmt->fetchColumn();
}


function getAvatarPath(?string $avatar): string
{
    if (!empty($avatar)) {
        return "../" . $avatar;
    }

    return "../assets/musicculture-default-avatar.png";
}