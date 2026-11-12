@php
    $current = request('sort') === $col;
    $next = ($current && request('dir') === 'asc') ? 'desc' : 'asc';
@endphp
<th>
    <a href="{{ request()->fullUrlWithQuery(['sort' => $col, 'dir' => $next, 'page' => null]) }}"
       class="text-decoration-none text-reset">
        {{ $label }} @if($current) {{ request('dir') === 'asc' ? '▲' : '▼' }} @endif
    </a>
</th>