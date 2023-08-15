<?php

namespace App\Http\Requests;

use App\Helpers\ResponseHelper;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseRequest extends FormRequest {

    protected bool $jsonError = false;
    protected bool $pagination = false;
    const OFFSET = 0;
    const LIMIT = 20;
    const PAGINATION_RULE = [
        'offset' => 'required|integer',
        'limit' => 'required|integer',
    ];
    protected bool $isSetId = false;


    public function authorize() {
        return true;
    }

    /**
     * Return error response in case validation errors
     * @param Validator $validator
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator) {
        if ($this->jsonError) {
            throw new HttpResponseException(
                ResponseHelper::error((new ValidationException($validator))->errors())
            );
        }

        parent::failedValidation($validator);
    }

    /**
     * Create the default validator instance.
     *
     * @param  \Illuminate\Contracts\Validation\Factory  $factory
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function createDefaultValidator(ValidationFactory $factory): Validator
    {
        $rules = $this->container->call([$this, 'rules']);
        if($this->pagination) $rules = array_merge($rules, self::PAGINATION_RULE);

        if ($this->isPrecognitive()) {
            $rules = $this->filterPrecognitiveRules($rules);
        }

        return $factory->make(
            $this->validationData(), $rules,
            $this->messages(), $this->attributes()
        )->stopOnFirstFailure($this->stopOnFirstFailure);
    }

    /**
     * Перед валидацией подставляем занчения по дефолту
     */
    protected function prepareForValidation()
    {
        if($this->isSetId){
            if ($this['id'] !== null) {
                $this->merge([
                    'id' => $this['id']
                ]);
            }
        }

        if($this->pagination) {
            $data = [];
            if ($this['offset'] === null) {
                $data['offset'] = self::OFFSET;
            }

            if ($this['limit'] === null) {
                $data['limit'] = self::LIMIT;
            }
            $this->merge($data);
        }

    }
}
