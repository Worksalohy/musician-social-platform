<?php

/**
 * Retrieve a user for a chat conversation.
 */
function getChatUser(PDO $pdo, int $userId): array|false
{
    $stmt = $pdo->prepare("
        SELECT id, username, avatar
        FROM users
        WHERE id = ?
    ");

    $stmt->execute([$userId]);

    return $stmt->fetch();
}

/**
 * Mark messages from another user as read.
 */
function markConversationAsRead(
    PDO $pdo,
    int $senderId,
    int $receiverId
): void {

    $stmt = $pdo->prepare("
        UPDATE messages
        SET is_read = 1
        WHERE sender_id = ?
          AND receiver_id = ?
          AND is_read = 0
    ");

    $stmt->execute([
        $senderId,
        $receiverId
    ]);
}