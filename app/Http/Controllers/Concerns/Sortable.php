<?php

namespace App\Http\Controllers\Concerns;

trait Sortable
{
    /**
     * Apply a request-driven ?sort=&dir= to a query, restricted to an
     * allowlist of columns so users can't sort by arbitrary/unindexed
     * columns via the URL.
     */
    protected function applySort($query, array $allowed, string $default, string $defaultDir = 'asc', ?string $sortParam = 'sort', ?string $dirParam = 'dir')
    {
        $sort = request($sortParam, $default);
        $dir = request($dirParam, $defaultDir) === 'desc' ? 'desc' : 'asc';

        if (!in_array($sort, $allowed, true)) {
            $sort = $default;
            $dir = $defaultDir;
        }

        return $query->orderBy($sort, $dir);
    }
}
