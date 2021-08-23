<?php

    namespace App\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;

    class ItemInventoryRequest extends FormRequest{
        public function authorize(){
            return true;
        }

        public function rules(){
            if($this->method() == 'PATCH'){
                return [
                    'title' => 'required',
                    'items_id' => 'required|array|min:1',
                ];
            }else{
                return [
                    'title' => 'required',
                    'items_id' => 'required|array|min:1',
                ];
            }
        }

        public function messages(){
            return [
                'title.required' => 'Please enter title',
                'items_id.required' => 'Please select items',
                'name.array' => 'Items must be array',
                'name.min' => 'Please select atleast one item',
            ];
        }
    }
