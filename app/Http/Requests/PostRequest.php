<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array(
            //
            'title'=>'required|min:3|max:40',//Создаем условие при котором поле title обязательно к заполнению. Минимальное кол-во символов должно быть 3, а максимальное 40
            'description'=>'required|min:10|max:550',//Создаем условие при котором поле description обязательно к заполнению. Минимальное кол-во символов должно быть 10, а максимальное 550
            'img'=>'mimes:jpeg,png|max:5000',//Создаем условие при котором картинка должна быть в формате jpeg или png, а размер ее должен быть не больше 5 мб(5000кб)
        );
    }
}
