@php $array = []; @endphp

@foreach($inventory_items as $k => $v)
    @php array_push($array, $v['item_id']); @endphp
@endforeach

@foreach($data as $row)
    <tr>
        <td>
            <div class="form-check">
                <label class="form-check-label">
                    <input type="checkbox" id="items{{ $row->id }}" class="form-check-input items" 
                        value="{{ $row->id }}"
                        @if(!empty($items) && in_array($row->id, $items)) checked @endif
                        @if(in_array($row->id, $array)) checked @endif
                    >
                    <span class="form-check-sign"></span>
                </label>
            </div>
        </td>
        <td>{{ $row->name }}</td>
        <td>{{ $row->description }}</td>
    </tr>
@endforeach
