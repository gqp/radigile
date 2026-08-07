<?php

namespace App\Http\Controllers\Concerns;

trait Searchable
{
    /**
     * Apply a request-driven ?search= term across one or more columns.
     * Columns can be direct ('title') or dot-notation into a relation
     * ('user.name'), which becomes a whereHas() on that relation.
     */
    protected function applySearch($query, array $columns, string $param = 'search')
    {
        $term = trim((string) request($param));

        if ($term === '') {
            return $query;
        }

        return $query->where(function ($q) use ($columns, $term) {
            foreach ($columns as $column) {
                if (str_contains($column, '.')) {
                    [$relation, $relationColumn] = explode('.', $column, 2);
                    $q->orWhereHas($relation, fn ($rq) => $rq->where($relationColumn, 'like', "%{$term}%"));
                } else {
                    $q->orWhere($column, 'like', "%{$term}%");
                }
            }
        });
    }
}
