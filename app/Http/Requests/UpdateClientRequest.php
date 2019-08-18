<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'last_name' => ['required', 'string', 'min:3', 'max:100'],
            'id_card' => ['required', 'string', 'min:13', 'max:13', Rule::unique('person_clients')->ignore($this->client->id)->where(function ($query) {
                return $query->where('organization_id', $this->organization->id);
            })],
            'email' => ['nullable', 'string', 'email', Rule::unique('person_clients')->ignore($this->client->id)->where(function ($query) {
                return $query->where('organization_id', $this->organization->id);
            })],
            'phone' => ['nullable', 'string', 'min:10', 'max:13', Rule::unique('person_clients')->ignore($this->client->id)->where(function ($query) {
                return $query->where('organization_id', $this->organization->id);
            })],
        ];
    }
}
