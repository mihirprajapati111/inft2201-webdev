<?php

namespace Application;

class Page
{
    private function sendJson($data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function badRequest(string $message = 'Bad request'): void
    {
        $this->sendJson([
            'error' => $message
        ], 400);
    }

    public function item(array $item): void
    {
        $this->sendJson($item, 200);
    }

    public function list(array $items): void
    {
        $this->sendJson($items, 200);
    }

    public function serverError(string $message = 'Server error'): void
    {
        $this->sendJson([
            'error' => $message
        ], 500);
    }
}
