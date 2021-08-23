<?php

    namespace App\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;

    class SubItemRequest extends FormRequest{
        public function authorize(){
            return true;
        }

        public function rules(){
            if($this->method() == 'PATCH'){
                return [
                    'category_id' => 'required',
                    'name' => 'required'
                ];
            }else{
                return [
                    'category_id' => 'required',
                    'name' => 'required'
                ];
            }
        }

        public function messages(){
            return [
                'category_id.required' => 'Please select category',
                'name.required' => 'Please enter name'
            ];
        }
    }
