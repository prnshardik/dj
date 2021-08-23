<?php
    namespace App\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;

    class SubItemCategoryRequest extends FormRequest{
        public function authorize(){
            return true;
        }

        public function rules(){
            if($this->method() == 'PATCH'){
                return [
                    'title' => 'required|unique:items_categories,title,'.$this->id
                ];
            }else{
                return [
                    'title' => 'required|unique:items_categories,title'
                ];
            }
        }

        public function messages(){
            return [
                'title.required' => 'Please enter title',
                'title.unique' => 'Title already exists, Please use another title'
            ];
        }
    }
