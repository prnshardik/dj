@php $array = []; @endphp

@foreach($selected as $k => $v)
    @php array_push($array, $k); @endphp
@endforeach

@foreach($data as $row)
    <tr>
        <td>
            <div class="form-check">
                <label class="form-check-label">
                    <input type="checkbox" class="form-check-input sub_inventories" name="sub_inventories[]" value="{{ $row->id }}" data-name="{{ $row->title }}"
                    data-item="{{ $row->items }}"
                    @if(in_array($row->id, $array)) checked @endif>
                    <span class="form-check-sign"></span>
                </label>
            </div>
        </td>
        <td>{{ $row->title }}</td>
        <td>{{ $row->items }}</td>
    </tr>
@endforeach
