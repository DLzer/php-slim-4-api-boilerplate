<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Data\UserData;
use App\Factory\QueryFactory;
use App\Support\Hydrator;

/**
 * Repository.
 */
final class UserFinderRepository
{
    private QueryFactory $queryFactory;

    private Hydrator $hydrator;

    private string $table;

    /**
     * The constructor.
     *
     * @param QueryFactory $queryFactory The query factory
     * @param Hydrator $hydrator The hydrator
     */
    public function __construct(QueryFactory $queryFactory, Hydrator $hydrator)
    {
        $this->queryFactory = $queryFactory;
        $this->hydrator = $hydrator;
        $this->table = 'users';
    }

    /**
     * Find users.
     *
     * @return UserData[] A list of users
     */
    public function findUsers(): array
    {
        $query = $this->queryFactory->newSelect($this->table);


        $query->select(
            [
                'id',
                'role_id',
                'first_name',
                'last_name',
                'email',
                'created_at'
            ]
        );

        // Add more "use case specific" conditions to the query
        // ...

        $rows = $query->execute()->fetchAll('assoc') ?: [];

        // Convert to list of objects
        return $this->hydrator->hydrate($rows, UserData::class);
    }
}