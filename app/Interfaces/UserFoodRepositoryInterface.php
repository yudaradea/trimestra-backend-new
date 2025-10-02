<?php

namespace App\Interfaces;

interface UserFoodRepositoryInterface
{
    public function getAll($userId, ?string $search, ?int $limit, bool $execute);

    public function getAllPaginated($userId, ?string $search, ?int $rowPerPage);

    public function getById(string $id, $userId);

    public function create(array $data);

    public function update(string $id, array $data, $userId);

    public function delete(string $id, $userId);
}
