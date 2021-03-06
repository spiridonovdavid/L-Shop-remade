<?php
declare(strict_types = 1);

namespace app\Http\Requests\Frontend\Profile\Character;

use app\Services\Validation\Rule;
use app\Services\Validation\RulesBuilder;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @param Repository $config
     *
     * @return array
     */
    public function rules(Repository $config): array
    {
        return [
            'password' => (new RulesBuilder())
                ->addRule(new Rule('required'))
                ->addRule(new Rule('string'))
                ->addRule(new Rule('min', $config->get('auth.validation.password.min')))
                ->addRule(new Rule('max', $config->get('auth.validation.password.max')))
                ->addRule(new Rule('confirmed'))
                ->build()
        ];
    }
}
