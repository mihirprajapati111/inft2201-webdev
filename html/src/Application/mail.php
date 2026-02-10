<?php

namespace Application;

use PDO;

class Mail
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllMail(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM mail ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMailById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM mail WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $mail = $stmt->fetch(PDO::FETCH_ASSOC);
        return $mail ?: null;
    }

    public function createMail(string $subject, string $body): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO mail (subject, body) VALUES (:subject, :body) RETURNING id"
        );
        $stmt->execute([
            'subject' => $subject,
            'body' => $body
        ]);

        return (int)$stmt->fetchColumn();
    }

    public function updateMail(int $id, string $subject, string $body): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE mail SET subject = :subject, body = :body WHERE id = :id"
        );
        return $stmt->execute([
            'id' => $id,
            'subject' => $subject,
            'body' => $body
        ]);
    }

    public function deleteMail(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM mail WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
