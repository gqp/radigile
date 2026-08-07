@props(['field', 'label', 'sortParam' => 'sort', 'dirParam' => 'dir', 'pageParam' => 'page', 'class' => ''])

@php
    $currentSort = request($sortParam);
    $currentDir = request($dirParam, 'asc');
    $isActive = $currentSort === $field;
    $nextDir = ($isActive && $currentDir === 'asc') ? 'desc' : 'asc';
    $url = request()->fullUrlWithQuery([$sortParam => $field, $dirParam => $nextDir, $pageParam => null]);
@endphp
<th class="{{ $class }}">
    <a href="{{ $url }}" class="text-decoration-none text-reset d-inline-flex align-items-center gap-1">
        {{ $label }}
        @if ($isActive)
            <i class="fas fa-sort-{{ $currentDir === 'asc' ? 'up' : 'down' }}"></i>
        @else
            <i class="fas fa-sort text-muted small"></i>
        @endif
    </a>
</th>
