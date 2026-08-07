@props(['param' => 'search', 'placeholder' => 'Search...', 'pageParam' => 'page', 'id' => null, 'live' => false])

<form method="GET" class="d-flex gap-2" {{ $live ? 'onsubmit=return false' : '' }}>
    {{-- Preserve every other active query param (e.g. another section's sort/page
         on a page with multiple independent lists) except this search field and
         its own page param, which should reset to page 1 on a new search. --}}
    @foreach (request()->except([$param, $pageParam]) as $key => $value)
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
    @endforeach
    <input type="text" name="{{ $param }}" value="{{ request($param) }}" class="form-control form-control-sm"
           @if($id) id="{{ $id }}" @endif data-page-param="{{ $pageParam }}"
           style="max-width: 260px;" placeholder="{{ $placeholder }}" autocomplete="off">
    <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="fas fa-search"></i></button>
    @if (request($param))
        <a href="{{ request()->fullUrlWithQuery([$param => null, $pageParam => null]) }}" class="btn btn-sm btn-outline-secondary">Clear</a>
    @endif
</form>
