<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">{{ $title ?? 'List' }}</h5>
    </div>

    <hr class="m-0">
    <div class="table-responsive text-nowrap p-2">
        <table class="table table-striped table-hover dataTableList">
            <thead>
                <tr>
                    @foreach ($columns as $col)
                        <th>{{ $col }}</th>
                    @endforeach
                    @if(!empty($actions))
                        <th> Actions </th>
                    @endif
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($rows as $row)
                    <tr>
                        @foreach ($fields as $field)
                            <td>
                                @if(is_callable($field))
                                    {!! $field($row) !!}
                                @else
                                    {{ data_get($row, $field) }}
                                @endif
                            </td>
                        @endforeach

                        @if(!empty($actions))
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @foreach ($actions as $action)
                                            <a class="dropdown-item" href="{{ $action['url']($row) }}"
                                               @if(!empty($action['confirm']))
                                                   onclick="return confirm($action['confirm'])"
                                               @endif
                                            >
                                                <i class="{{ $action['icon'] ?? 'bx bx-link' }} me-1"></i> {{ $action['label'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="{{ count($columns) + (empty($actions) ? 0 : 1) }}" class="text-center">No records found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
