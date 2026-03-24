@extends('layouts.header')

@section('content')
<div class="container">
    <h4>{{$board->project->name}} - {{ $board->board }}</h4>

  <table class="table table-bordered text-center">
    <thead>
        <tr>
            <th>From \ To</th>
            @foreach($boards as $to)
                <th>{{ $to->board }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($boards->where('id',$board->id) as $from)
        <tr>
            <td><strong>{{ $from->board }}</strong></td>

            @foreach($boards as $to)
                @php
                    $transition = $transitions->first(function($t) use ($from, $to) {
                        return $t->from_board_id == $from->id &&
                               $t->to_board_id == $to->id;
                    });
                @endphp
                <td>
                    @if($from->id == $to->id)
                        —
                    @else
                        <form method="POST" action="{{ route('workflow.transition.save') }}">
                            @csrf
                            <input type="hidden" name="project_id" value="{{ $board->project_id }}">
                            <input type="hidden" name="from_board_id" value="{{ $from->id }}">
                            <input type="hidden" name="to_board_id" value="{{ $to->id }}">

                            <input type="checkbox"
                                   name="is_allowed"
                                   onchange="this.form.submit()"
                                   {{ optional($transition)->is_allowed ? 'checked' : '' }}>
                        </form>
                    @endif
                </td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>
</div>
@endsection
