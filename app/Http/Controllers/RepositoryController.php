<?php

namespace App\Http\Controllers;

use App\Models\Repository;
use Illuminate\Http\Request;

class RepositoryController extends Controller
{
    public function index()
    {
        return Repository::query()
            ->with('team')
            ->where('team_id', auth()->user()->current_team_id)
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'url' => 'required|string|max:2048',
            'is_public' => 'required|boolean',
            'use_v_in_version' => 'required|boolean',
        ]);

        $resource = auth()->user()->currentTeam->repositories()->create($data);
        return $resource->refresh();
    }

    public function show(Repository $repository)
    {
        return $repository;
    }

    public function update(Request $request, Repository $repository)
    {
        $repository->update($request->all());

        return $repository->refresh();
    }


    public function destroy(Repository $repository)
    {
        $repository->delete();
        return response([], 204);
    }
}
