<?php

namespace App\Interfaces;

interface ExerciseRepositoryInterface
{
    public function getAll(?string $search, ?int $limit, ?string $jenis, bool $execute);

    public function getAllPaginated(?string $search, ?int $rowPerPage, ?string $jenis);

    public function getById(string $id);

    public function create(array $data);

    public function update(string $id, array $data);

    public function delete(string $id);
}
