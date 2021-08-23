@php $array = []; @endphp

@foreach($selected as $k => $v)
    @php 
        array_push($array, $k); 
    @endphp
@endforeach

@php $selected = (array) $selected; @endphp

@foreach($data as $row)
    <tr>
        <td>
            <div class="form-check">
                <label class="form-check-label">
                    <input type="checkbox" class="form-check-input option" name="option[]" value="{{ $row->id }}" id="{{ $row->id }}" data-id="{{ $row->id }}"
                        @if(!empty($array) && in_array($row->id, $array)) checked @endif>
                    <span class="form-check-sign"></span>
                </label>
            </div>
        </td>
        <td>{{ $row->name }}</td>
        <td><input type="number" name="height[]" min="0" class="form-control height" value="{{ $selected[$row->id]->height ?? '' }}" id="height{{ $row->id }}"></td>
        <td><input type="number" name="width[]" min="0" class="form-control width" value="{{ $selected[$row->id]->width ?? '' }}" id="width{{ $row->id }}"></td>
        <td><input type="number" name="quantity[]" min="0" class="form-control quantity" value="{{ $selected[$row->id]->quantity ?? '' }}" id="quantity{{ $row->id }}"></td>
    </tr>
@endforeach
