<?php

namespace App\Interfaces;

interface FoodRepositoryInterface
{
    public function getAll(
        ?string $search,
        ?int $limit,
        // filter sesuai kategori
        ?string $filterByFoodCategoryId,
        bool $execute
    );

    public function getAllPaginated(
        ?string $search,
        ?int $rowPerPage,
        ?string $filterByFoodCategoryId
    );

    public function getById(string $id);

    public function create(array $data);

    public function update(string $id, array $data);

    public function delete(string $id);
}
