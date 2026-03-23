<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreOrganizationRequest;
use App\Http\Requests\Api\V1\UpdateOrganizationRequest;
use App\Http\Resources\V1\OrganizationResource;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrganizationController extends Controller
{
    public function index(Request $request)
    {
        return OrganizationResource::collection($request->user()->organizations);
    }

    public function store(StoreOrganizationRequest $request)
    {
        $organization = Organization::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name).'-'.Str::random(5),
            'admin_user_id' => $request->user()->id,
            'logo' => $request->logo,
        ]);

        $organization->users()->attach($request->user());

        return new OrganizationResource($organization);
    }

    public function update(UpdateOrganizationRequest $request, Organization $organization)
    {
        if ($organization->admin_user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $organization->update($request->validated());

        return new OrganizationResource($organization);
    }

    public function destroy(Request $request, Organization $organization)
    {
        if ($organization->admin_user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $organization->delete();

        return response()->json(['message' => 'Organization deleted successfully.']);
    }
}
