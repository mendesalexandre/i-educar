<?php

namespace App\Http\Controllers\Api\Resource\EvaluationRule;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Resource\EvaluationRule\ResourceEvaluationRuleRequest;
use App\Models\LegacyEvaluationRule;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceEvaluationRuleController extends Controller
{
    public function index(ResourceEvaluationRuleRequest $request): JsonResource
    {
        $institution = $request->get('institution');

        $evaluation_rules = LegacyEvaluationRule::select(['id', 'nome as name'])->whereInstitution($institution)
            ->orderByName()
            ->get();

        JsonResource::withoutWrapping();
        return JsonResource::collection($evaluation_rules);
    }
}
