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

/**
 * Get all messages between two users.
 */
function getConversationMessages(
    PDO $pdo,
    int $userId,
    int $otherUserId
): array {

    $stmt = $pdo->prepare("
        SELECT 
            m.*,
            u.username
        FROM messages m
        JOIN users u 
            ON m.sender_id = u.id
        WHERE
            (m.sender_id = ? AND m.receiver_id = ?)
            OR
            (m.sender_id = ? AND m.receiver_id = ?)
        ORDER BY m.created_at ASC
    ");

    $stmt->execute([
        $userId,
        $otherUserId,
        $otherUserId,
        $userId
    ]);

    return $stmt->fetchAll();
}

function sendMessage(
    PDO $pdo,
    int $senderId,
    int $receiverId,
    string $message
): bool
{
    $message = trim($message);

    if ($receiverId <= 0 || $message === '') {
        return false;
    }

    $stmt = $pdo->prepare("
        INSERT INTO messages (
            sender_id,
            receiver_id,
            message
        )
        VALUES (?, ?, ?)
    ");

    return $stmt->execute([
        $senderId,
        $receiverId,
        $message
    ]);
}