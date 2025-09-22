@props([
    'title' => 'Itens',
    'total' => 0,
    'tusuarios' => 0,
    'desc' => null,
    'class' => '',
])

<div {{ $attributes->merge(['class' => 'stats shadow gap-3 m-3 ' . $class]) }}>
  <div class="stat space-y-4">
    <div class="stat-title">Total de {{ $title }}</div>
    <div class="stat-value">{{ $total }}</div>
    @if ($desc)
      <div class="stat-desc">{{ $desc }}</div>
    @else
      <div class="stat-desc">Por {{ $tusuarios }} usuários</div>
    @endif
  </div>
</div>